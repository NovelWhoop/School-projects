/*
 * Projekt do predmetu IPK, cast klient
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
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <netdb.h>

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
      break;
    case 4:
      fprintf(stderr, "Read error.\n");
      exit(EXIT_FAILURE);
      break;
    case 5:
      fprintf(stderr, "Close error.\n");
      exit(EXIT_FAILURE);
      break;
  }
}

/*
 * MAIN
 * Zpracovani parametru a odeslani pozadavku na server, prijeti odpovedi
 */
int main(int argc, char *argv[])
{
  // priznaky "zapnuti" prepinace pro kontrolu opakovani
  bool hFlag = false;
  bool pFlag = false;
  bool lFlag = false;
  bool uFlag = false;
  bool LFlag = false;
  bool UFlag = false;
  bool GFlag = false;
  bool NFlag = false;
  bool HFlag = false;
  bool SFlag = false;

  // promenne pro komunikaci
  int s, n; // n je pro pocet bajtu ktere prijimam, s reprezentuje socket
  struct sockaddr_in sin;
  struct hostent *hptr;
  int port = 0;
  char hostname[256];
  char login[256][256];
  char uid[256][256];
  int loginSize = 0; // slouzi jako index do tabulky loginu, a zaroven jako pocitadlo, kolik loginu sem ulozil (pocet polozek pole loginu)
  int uidSize = 0; // analogie s loginy
  int uORl;
  int ch;
  char possition, Lpossition, Upossition, Gpossition, Npossition, Hpossition, Spossition; // promenne pro pocitani pozice daneho prepinace, nutne pro vypis vysledku v poradi prepinacu
  char append[8];

  // priprava zpravy na server
  char msg[256] = "";
  char msgAnswer[256] = "";

  /*
   * Zpracovani parametru
   * parametry se nesmi opakovat, vyjma -l a -u, kde se bere v potaz posledni zjisteny
   */
  while ((ch = getopt(argc, argv, "h:p:l:u:LUGNHS")) != -1)
  {
    switch (ch)
    {
      case 'p':
        if(pFlag) printError(1);
        if (!atoi(optarg)) printError(1);
        else port = atoi(optarg);
        pFlag = true; // poznacim si, ze uz byl tento parametr zadan
        break;

       case 'h':
        if(hFlag) printError(1);
        strcpy(hostname, optarg);
        hFlag = true;
        break;

      case 'l':
        if(lFlag) printError(1);

        optind--;
        for( ; optind < argc && *argv[optind] != '-'; optind++)
        {
          strcpy(login[loginSize], argv[optind]);
          loginSize++; // posunu se na dalsi radek pole loginu   
        }
        uORl = 'l'; // pozdejsi argument je l
        lFlag = true;
        break;

      case 'u':
        if (uFlag) printError(1);

        optind--;
        for( ; optind < argc && *argv[optind] != '-'; optind++)
        {
          strcpy(uid[uidSize], argv[optind]);
          uidSize++; // posunu se na dalsi radek pole uidu   
        }
        uORl = 'u'; // pozdejsi argument je u
        uFlag = true;
        break;

      case 'L':
        if(LFlag) printError(1);
        Lpossition = possition; // poznamename si pozici prepinace
        LFlag = true;
        break;

       case 'U':
        if(UFlag) printError(1);
        Upossition = possition; // poznamename si pozici prepinace
        UFlag = true;
        break;

      case 'G':
        if(GFlag) printError(1);
        Gpossition = possition; // poznamename si pozici prepinace
        GFlag = true;
        break;

      case 'N':
        if(NFlag) printError(1);
        Npossition = possition; // poznamename si pozici prepinace
        NFlag = true;
        break;

      case 'H':
        if(HFlag) printError(1);
        Hpossition = possition; // poznamename si pozici prepinace
        HFlag = true;
        break;

      case 'S':
        if(SFlag) printError(1);
        Spossition = possition; // poznamename si pozici prepinace
        SFlag = true;
        break;

      default:
        printError(1);
        break;
    }
    possition++;
  }

  // klient nezadal cislo portu, hostname, nebo nezadal ani -u, ani -l
  if(!pFlag || !hFlag) printError(1);

  /* Kodovani dotazu na server
   * - nejprve ulozim na zacatek retezce priznaky LUGNHS
   * - pote ulozim u nebo l, podle toho, co chci vyhledat
   * - nakonec postupne vlozim obsah pole loginu nebo uidu
   */
  // nejprve koduji priznaky 
  if(LFlag)
  {
    strcat(msg, "t");
    sprintf(append,"%d", Lpossition); // kvuli konverzi intu na string
    strcat(msg, append); // vysledny tvar je tx, kde x je pozice prepinace
  }
  else strcat(msg, "f0");

  if(UFlag)
  {
    strcat(msg, "t");
    sprintf(append,"%d", Upossition);
    strcat(msg, append);
  }
  else strcat(msg, "f0");

  if(GFlag)
  {
    strcat(msg, "t");
    sprintf(append,"%d", Gpossition);
    strcat(msg, append);
  }
  else strcat(msg, "f0");

  if(NFlag)
  {
    strcat(msg, "t");
    sprintf(append,"%d", Npossition);
    strcat(msg, append);
  }
  else strcat(msg, "f0");

  if(HFlag)
  {
    strcat(msg, "t");
    sprintf(append,"%d", Hpossition);
    strcat(msg, append);
  }
  else strcat(msg, "f0");

  if(SFlag)
  {
    strcat(msg, "t");
    sprintf(append,"%d", Spossition);
    strcat(msg, append);
  }
  else strcat(msg, "f0");

  // a ted zakoduji u nebo l, podle kontextu
  switch(uORl)
  {
    case 'u':
      strcat(msg, "u"); // poznacim si pro server, ze ma nasledujici hodnoty ukladat jako uid
      for(int i = 0; i < uidSize; i++)
      {
        strcat(msg, uid[i]);
        strcat(msg, ","); // oddeleni jednotlivych uid
      }
      strcat(msg, ":"); // poznacim si konec zpravy, pro rekonstrukci v serveru
      break;

    case 'l':
      strcat(msg, "l"); // poznacim si pro server, ze ma nasledujici hodnoty ukladat jako uid
      for(int i = 0; i < loginSize; i++)
      {
        strcat(msg, login[i]);
        strcat(msg, ","); // oddeleni jednotlivych loginu
      }
      strcat(msg, ":"); // poznacim si konec zpravy, pro rekonstrukci v serveru  
      break;

    default:
      printError(1); // klient nezadal ani u, ani l, takze chybne parametry
      break;
  }

/*
 * Navazani komunikace a obsluha pozadavku.
 * Je treba serveru poslat veskera data, ktere chceme zpracovat.
 * Server tyto data zpracuje a posle klientovi vysledek.
 */

  // vytvoreni socketu
  if ((s = socket(PF_INET, SOCK_STREAM, 0 )) < 0) printError(2);

  sin.sin_family = PF_INET;
  sin.sin_port = htons(port);

  // overeni hostname
  if ((hptr =  gethostbyname(hostname)) == NULL) printError(2);
  memcpy( &sin.sin_addr, hptr->h_addr, hptr->h_length);

  // overeni pripojeni
  if (connect (s, (struct sockaddr *)&sin, sizeof(sin)) < 0) printError(2);

  // posilani zpravy na server
  if (write(s, msg, strlen(msg) +1) < 0) printError(3);
  
  // cteni zpravy od serveru a jeji vypis
  if ((n = read(s, msgAnswer, sizeof(msgAnswer))) <0) printError(4); 
  fprintf(stdout, msgAnswer);
  if (close(s) < 0)  printError(5); // zavreni komunikace

  return EXIT_SUCCESS;
}
