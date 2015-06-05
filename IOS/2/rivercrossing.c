/*
 * Soubor:  rivercrossing.c
 * Datum:   2014/5/4
 * Autor:   Roman Halik, xhalik01@stud.fit.vutbr.cz
 * Projekt: Projekt do predmetu IOS - Operacni systemy,
 * modifikovany synchronizacni problem River Crossing Problem
 */

#include <stdio.h>
#include <time.h>
#include <stdlib.h>
#include <unistd.h>
#include <sys/wait.h>
#include <sys/types.h>
#include <sys/mman.h>
#include <sys/shm.h>
#include <sys/ipc.h>
#include <sys/sem.h>
#include <sys/stat.h>
#include <semaphore.h>
#include <fcntl.h>
#include <ctype.h>

#include <stdbool.h>
#include <pthread.h>
#include <string.h>


struct countingStructure
{
   int poradi;
   int hacker;
   int serf;
   int isOnMolo;
   int hackerOnMolo;
   int serfOnMolo;
   int countOnBoard;
   int dvojce;
};

void getHelp(void) // vypise napovedu programu
{
   fprintf(stderr, "Napoveda k programu rivercrossing.\n"
   " Pouziti ./rivercrossing P H S R\n"
   " P - pocet clenu v kazde kategorii (hackers, serfs)\n"
   " H - maximalni doba, po kterou je generovan novy proces hacker, 0 - 5001 ms\n"
   " S - maximalni doba, po kterou je generovan novy proces serf, 0 - 5001 ms\n"
   " R - maximalni doba plavby, 0 - 5001ms\n"
   );
   return;
}

void getSorry(void)
{
   fprintf(stderr, "Program musi mit alespon jeden argument.\n");
   getHelp();
}

void getBadArgument(void)
{
   fprintf(stderr, "Spatne zadane argumenty.\n");
   return;
}

void hackersGenerator(int pocetOsob, int dobaH, struct countingStructure *counting, sem_t *semNaMoloH, sem_t *mutex, FILE *fp)
{
   for (int i=0; i<pocetOsob; i++)
   {
      int randomNumber = rand()%(dobaH + 1); // generovani cisla v rozsahu 1 az H
      usleep(randomNumber*1000);  // nasobime 1000, abychom dostali hodnotu v ms

      pid_t hackers_pid;
      hackers_pid = fork();

      if (hackers_pid == 0)
      {
// startovani
         sem_wait(mutex);
         fprintf(fp, "%d: hacker: %d: started \n", counting->poradi++, i+1);
         sem_post(mutex);

// na molo?
         sem_wait(semNaMoloH);
         sem_wait(mutex);
         counting->hackerOnMolo++;
         sem_post(mutex);

         if (counting->hackerOnMolo==2)
            counting->dvojce++;

         if ((counting->hackerOnMolo<=4) && (counting->isOnMolo<=3))
         {
            sem_post(semNaMoloH);
            sem_wait(mutex);
            fprintf(fp, "%d: hacker: %d: waiting for boarding: %d: %d\n", counting->poradi++, i+1, counting->hackerOnMolo, counting->serfOnMolo);
            counting->isOnMolo++;
            counting->hacker++;
            sem_post(mutex);
         }
         else
            sem_post(semNaMoloH);

         exit(0);
      }
      else if (hackers_pid == -1)
         fprintf(stderr, "Nepodarilo se vytvorit hackers.\n");
      else
      {
         while (wait(NULL) > 0);
      }
   }
   exit(0);
}

void serfsGenerator(int pocetOsob, int dobaS, struct countingStructure *counting, sem_t *semNaMoloH, sem_t *mutex, FILE *fp)
{
   for (int i=0; i<pocetOsob; i++)
   {
      int randomNumber = rand()%(dobaS + 1); // generovani cisla v rozsahu 1 az H
      usleep(randomNumber*1000);  // nasobime 1000, abychom dostali hodnotu v ms

      pid_t serfs_pid;
      serfs_pid = fork();

      if (serfs_pid == 0)
      {
// startovani
         sem_wait(mutex);
         fprintf(fp,"%d: serf: %d: started \n", counting->poradi++, i+1);

// na molo?
         sem_post(mutex);
         sem_wait(semNaMoloH);
         sem_wait(mutex);
         counting->serfOnMolo++;
         sem_post(mutex);

         if (counting->serfOnMolo==2)
            counting->dvojce++;

        if ((counting->serfOnMolo<=4) && (counting->isOnMolo<=3))
         {
            sem_post(semNaMoloH);
            sem_wait(mutex);
            fprintf(fp, "%d: serf: %d: waiting for boarding: %d: %d\n", counting->poradi++, i+1, counting->hackerOnMolo, counting->serfOnMolo);
            counting->isOnMolo++;
            counting->serf++;
            sem_post(mutex);
         }
         else
            sem_post(semNaMoloH);

         exit(0);
      }
      else if (serfs_pid == -1)
         fprintf(stderr, "Nepodarilo se vytvorit serfs.\n");
      else
      {
         while (wait(NULL) > 0);
      }
   }
   exit(0);
}

int main(int argc, char *argum[])
{
   srand(time(0)); // pro generovani nahodnych cisel

   switch (argc)
   {
      case 1: // uzivatel nezadal zadny argument
      {
         getSorry();
         return (1);
         break;
      }
      case 5: // uzivatel zadal prave 4 argumenty
      {
         int P,H,S,R;
         if ((isdigit(*argum[1])) && (isdigit(*argum[2])) && (isdigit(*argum[3])) && (isdigit(*argum[4])))
         {
            P = atoi((argum[1]));
            H = atoi((argum[2]));
            S = atoi((argum[3]));
            R = atoi((argum[4]));
         }
         else
         {
            getBadArgument();
            return (1);
         }

         if (((P != 0) && (P % 2 == 0)) &&
            ((H >= 0) && (H < 5001)) &&
            ((S >= 0) && (S < 5001)) &&
            ((R >= 0) && (R < 5001)))
         {

// teprve po overeni agumentu ma smysl vytvaret soubory, semafory a sdilenou pamet
// soubor pro zapis akci
            FILE * fp;
            fp = fopen ("rivercrossing.out", "w+");
// sdilena pamet
            int sm = shm_open("/xhalik01-shm", O_CREAT | O_EXCL | O_RDWR, 0644);
            ftruncate(sm, sizeof(struct countingStructure));
            struct countingStructure *counting = mmap(NULL, sizeof(struct countingStructure), PROT_READ | PROT_WRITE, MAP_SHARED, sm, 0);
            counting->poradi=1;

 // vytvorime semafory
            sem_t *semNaMoloH = sem_open("/xhalik01-semNaMoloH", O_CREAT | O_EXCL, 0644, 1);
            sem_t *mutex = sem_open("/xhalik01-mutex", O_CREAT | O_EXCL, 0644, 1);

// vytvoreni dvou pomocnych procesu
            pid_t serfs_pid;
            pid_t hackers_pid;

            counting->isOnMolo=0;
            counting->countOnBoard=0;

            hackers_pid = fork();
            if (hackers_pid == 0)
            {
// pomocny proces hacker pro volani generatoru hackeru
               hackersGenerator(P, H, counting, semNaMoloH, mutex, fp);
            }
            else if (hackers_pid == -1)
               fprintf(stderr, "Nepodarilo se vytvorit hackers.\n");
            else
            {
               serfs_pid = fork();
               if (serfs_pid == 0)
               {
// pomocny proces serf pro volani generatoru serfu
                  serfsGenerator(P, S, counting, semNaMoloH, mutex, fp);
               }
               else if (serfs_pid == -1)
                  fprintf(stderr, "Nepodarilo se vytvorit serfs.\n");
               else
               {
                  wait(NULL);
               }
               wait(NULL);
            }
            sem_close(semNaMoloH);
            sem_close(mutex);
            sem_unlink("/xhalik01-semNaMoloH");
            sem_unlink("/xhalik01-mutex");

            munmap(counting, sizeof(struct countingStructure));
            shm_unlink("/xhalik01-shm");
            close(sm);
            fclose(fp);
            return (0);
         }
         else
         {
            getBadArgument();
            return (1);
         }
         break;
      }
      default: // jakykoli jiny pocet argumentu je spatny
      {
         getBadArgument();
         return (1);
      }
   }
   return 0;
}
