/*
 * Projekt do predmetu IPK, cast server
 * Autor: Roman Halik, xhalik@stud.fit.vutbr.cz
 * Datum: brezen 2015
 */

#include <stdio.h>
#include <stdlib.h>
#include <ctype.h>
#include <string.h>
#include <stdbool.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <sys/wait.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <netdb.h>
#include <pwd.h>

// priznaky
bool LFlag = false;
bool UFlag = false;
bool GFlag = false;
bool NFlag = false;
bool HFlag = false;
bool SFlag = false;

// pozor, tyto priznaky maji v kontextu serveru trochu jiny vyznam!
// znaci, jestli mam ukladat do pole uidu, nebo loginu (jestli jsem dostal pozadavek na -l nebo -u)
bool lFlag = false;
bool uFlag = false;

// pole stringu pro uchovani vsech loginu a iud
char msg[256];
char login[256][256];
char uid[256][256];
char serverMsg[256] = "";
char result[256] = "";
int arraySize = 0;
char Lpossition, Upossition, Gpossition, Npossition, Hpossition, Spossition;
char tmpResult[256][256];
int min = 100; // inicializacni hodnoty
int max = 0;
pid_t pid;

/*
 * Funkce provadi vypis chybovych hlasek na stderr podle zadanych parametru (erorrCode)
 */
int printError(int errorCode)
{
  switch(errorCode)
  {
    case 1:
      fprintf(stderr, "Spatne zadane parametry.\n");
      exit(EXIT_FAILURE);
    case 2:
      fprintf(stderr, "Connection error.\n");
      exit(EXIT_FAILURE);
    case 3:
      fprintf(stderr, "Write error.\n");
      exit(EXIT_FAILURE);
    case 4:
      fprintf(stderr, "Read error.\n");
      exit(EXIT_FAILURE);
    case 5:
      fprintf(stderr, "Close error.\n");
      exit(EXIT_FAILURE);
    case 6:
      fprintf(stderr, "Listen error.\n");
      exit(EXIT_FAILURE);
    case 7:
      fprintf(stderr, "Accept error.\n");
      exit(EXIT_FAILURE);
    case 8:
      fprintf(stderr, "Unknown user. Try another.\n");
      strcat(result, "Unknown user. Try another.\n");
      break;
    case 9:
      fprintf(stderr, "Fork failure.\n");
      exit(EXIT_FAILURE);
  }
  return -1;
}

int main(int argc, char *argv[])
{
  // promenne pro komunikaci
  int s, t, sinlen;
  struct sockaddr_in sin;
  int i;
  struct hostent * hp;
  int j;

  /* 
   * Zpracovani parametru
   * - pokud je pocet argumentu jiny, nez 3 (nazev programu, -p, a cislo portu), 
   *   nebo prvni argument neni -p, nebo druhy argument neni cislo, konec
   */
  if(argc != 3 || strcmp(argv[1], "-p") || !atoi(argv[2])) printError(1);

  // vytvoreni socketu
  if ((s = socket(PF_INET, SOCK_STREAM, 0 )) < 0) printError(2);
  sin.sin_family = PF_INET;              /*set protocol family to Internet */
  sin.sin_port = htons(atoi(argv[2]));  /* set port no. */
  sin.sin_addr.s_addr  = INADDR_ANY;   /* set IP addr to any interface */

  // navazani spojeni
  if (bind(s, (struct sockaddr *)&sin, sizeof(sin)) < 0) printError(2);

  // naslouchani
  if (listen(s, 5)) printError(6);
  sinlen = sizeof(sin);

  // prijeti nove zadosti o spojeni, 't' znaci id socketu
  while (1)
  {
    int retcode;
    pid = fork();
    if (pid < 0) printError(9);
    else if (pid == 0)
    {
      // provedeme potomka
      if ((t = accept(s, (struct sockaddr *) &sin, &sinlen)) < 0) printError(7);
      hp = (struct hostent *)gethostbyaddr((char *)&sin.sin_addr,4,AF_INET);
      j = (int)(hp->h_length);
      bzero(msg,sizeof(msg));

      // cteni zpravy od klienta
      if (read(t, msg, sizeof(msg)) <0)  printError(4);
      char decodedFlag = 't'; // probem s promennou t, takto to hezky obejdu..
      // rozkodovavam zpravu od klienta
      // kontroluji, zdali se --ty znak rovna t, a pokud ano, zapisu do flagu true
      // jinak se musi rovnat f, tedy false

      // zpracovani prepinacu, zjisteni nejmensi a nejvetsi hodnoty pro pozdejsi cteni pole
      if(msg[0] == decodedFlag)
      {
        LFlag = true;
        Lpossition = msg[1];
        if(Lpossition < min) min = Lpossition;
        if(Lpossition > max) max = Lpossition;
      }
      else LFlag = false;

      if(msg[2] == decodedFlag)
      {
        UFlag = true;
        Upossition = msg[3];
        if(Upossition < min) min = Upossition;
        if(Upossition > max) max = Upossition;
      }
      else UFlag = false;

      if(msg[4] == decodedFlag)
      {
        GFlag = true;
        Gpossition = msg[5];
        if(Gpossition < min) min = Gpossition;
        if(Gpossition > max) max = Gpossition;
      }
      else GFlag = false;

      if(msg[6] == decodedFlag)
      {
        NFlag = true;
        Npossition = msg[7];
        if(Npossition < min) min = Npossition;
        if(Npossition > max) max = Npossition;
      }
      else NFlag = false;

      if(msg[8] == decodedFlag)
      {
        HFlag = true;
        Hpossition = msg[9];
        if(Hpossition < min) min = Hpossition;
        if(Hpossition > max) max = Hpossition;
      }
      else HFlag = false;

      if(msg[10] == decodedFlag)
      {
        SFlag = true;
        Spossition = msg[11];
        if(Spossition < min) min = Spossition;
        if(Spossition > max) max = Spossition;
      }
      else SFlag = false;

      // mam ukladat uid nebo loginy?
      if(msg[12] == 'u') uFlag = true;
      else lFlag = true;

      /* Hlavni cast serveru - zpracovani uid/loginu a tvorba odpovedi
       * Nejprve ze zpravy od klienta ulozime uid/loginy do poli,
       * a nasledne rovnou vyhledavame v etc/passwd podle zadanych kriterii
       */
      if(lFlag == true) // zapisujeme loginy do pole
      {
        int a = 0;
        int b = 0;
        int c = 0;
        int k = 12; // nutne zlo -> potrebuji zacit az od pismene, ktere predchazi prvnimu znaku loginu/uidu

        while(1)
        {
          k++;
          c = msg[k];
        
          if(c == ':') break; // sme na konci
          if(c == ',') // konec slova, jdeme na dalsi
          {
            a++;
            b = 0;
            continue;
          }
          login[a][b] = c;
          b++;
        }
        arraySize = a;

        // konverze na lowercase
        for (int q = 0; q < arraySize; q++)
          for(int r = 0; r < 256; r++) login[q][r] = tolower(login[q][r]);

        // vyhledani informaci dle zadanych pozadavku, a ulozeni do pole vysledku
        for(int e = 0; e < a; e++) // cyklim podle 'a', ktere mi znaci pocet polozek v poli, viz vyse
        {
          struct passwd *pw;
          char *lgn = login[e];



          if (!lgn || (pw = getpwnam(lgn)) == NULL)
          {
            printError(8);
            continue;
          }
          // login, jmeno/gecos, uid, gid, domovsky adresar, shell 
          else
          {
            char append[256];

            if(LFlag) strcpy(tmpResult[Lpossition], pw->pw_name);
            if(UFlag) 
            {
              sprintf(append,"%d", pw->pw_uid); // kvuli konverzi intu na string
              strcpy(tmpResult[Upossition], append);
            }
            if(GFlag)
            {
              sprintf(append,"%d", pw->pw_gid); // kvuli konverzi intu na string
              strcpy(tmpResult[Gpossition], append);
            } 
            if(NFlag) strcpy(tmpResult[Npossition], pw->pw_gecos);
            if(HFlag) strcpy(tmpResult[Hpossition], pw->pw_dir);
            if(SFlag) strcpy(tmpResult[Spossition], pw->pw_shell);
          }
          // tu musim uz ulozit vysledek, abych si jej neprepsal
          // vysledky ulozeny v poli -> je treba je prevest do stringu
          for(int g = min; g <= max; g++)
          {
            strcat(result, tmpResult[g]);
            strcat(result, " ");
          }
          strcat(result, "\n");
        }
        lFlag = false;
        uFlag = false;
      }
      else // zapisujeme uid do pole
      {
        int a = 0;
        int b = 0;
        int c = 0;
        int k = 12; // nutne zlo -> potrebuji zacit az od pismene, ktere predchazi prvnimu znaku loginu/uidu

        while(1)
        {
          k++;
          c = msg[k];
        
          if(c == ':') break; // sme na konci
          if(c == ',') // konec slova, jdeme na dalsi
          {
            a++;
            b = 0;
            continue;
          }
          uid[a][b] = c;
          b++;
        }
        arraySize = a;
        for(int e = 0; e < a; e++) // cyklim podle 'a', ktere mi znaci pocet polozek v poli
        {
          struct passwd *pw;
          uid_t id = atoi(uid[e]); // musime prevest string na int

          if (!id || (pw = getpwuid(id)) == NULL)
          {
            printError(8);
            continue;
          }
          // login, jmeno/gecos, uid, gid, domovsky adresar, shell 
          else
          {
            char append[256];
    
            if(LFlag) strcpy(tmpResult[Lpossition], pw->pw_name);
            if(UFlag) 
            {
              sprintf(append,"%d", pw->pw_uid); // kvuli konverzi intu na string
              strcpy(tmpResult[Upossition], append);
            }
            if(GFlag)
            {
              sprintf(append,"%d", pw->pw_gid); // kvuli konverzi intu na string
              strcpy(tmpResult[Gpossition], append);
            } 
            if(NFlag) strcpy(tmpResult[Npossition], pw->pw_gecos);
            if(HFlag) strcpy(tmpResult[Hpossition], pw->pw_dir);
            if(SFlag) strcpy(tmpResult[Spossition], pw->pw_shell);
          }
          // tu musim uz ulozit vysledek, abych si jej neprepsal
          // vysledky ulozeny v poli -> je treba je prevest do stringu
          for(int g = min; g <= max; g++)
          {
            strcat(result, tmpResult[g]);
            strcat(result, " ");
          }
          strcat(result, "\n");
        }
        lFlag = false;
        uFlag = false;
      }
      sleep(1);
    }
    else
    {
      // cekani na potomka
      wait(&retcode);
    }

    // poslani vysledku zpet klientovi
    if (write(t, result, strlen(result)) < 0) printError(3);

    // a vymazani vysledku, kvuli dalsim dotazum v jednom spojeni
    for (int v = 0; v < strlen(result); v++) result[v] = '\0';

    // a vymazani docasneho pole
    for(int w = 0; w < 256; w++)
      for(int x = 0; x < 256; x++) tmpResult[w][x] = '\0';
        
    // vymazani pole loginu a uid
    for(int w = 0; w < 256; w++)
      for(int x = 0; x < 256; x++) login[w][x] = '\0';

    for(int w = 0; w < 256; w++)
      for(int x = 0; x < 256; x++) uid[w][x] = '\0';

    // ukonceni spojeni
    if (close(t) < 0) printError(5);
  }
  if (close(s) < 0) printError(5);
  return EXIT_SUCCESS;
}
