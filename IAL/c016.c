
/* c016.c: **********************************************************}
{* Téma:  Tabulka s Rozptýlenými Polo¾kami
**                      První implementace: Petr Pøikryl, prosinec 1994
**                      Do jazyka C prepsal a upravil: Vaclav Topinka, 2005
**                      Úpravy: Karel Masaøík, øíjen 2014
**                      Úpravy: Radek Hranický, øíjen 2014
**
** Vytvoøete abstraktní datový typ
** TRP (Tabulka s Rozptýlenými Polo¾kami = Hash table)
** s explicitnì øetìzenými synonymy. Tabulka je implementována polem
** lineárních seznamù synonym.
**
** Implementujte následující procedury a funkce.
**
**  HTInit ....... inicializuje tabulku pøed prvním pou¾itím
**  HTInsert ..... vlo¾ení prvku
**  HTSearch ..... zji¹tìní pøítomnosti prvku v tabulce
**  HTDelete ..... zru¹ení prvku
**  HTRead ....... pøeètení hodnoty prvku
**  HTClearAll ... zru¹ení obsahu celé tabulky (inicializace tabulky
**                 poté, co ji¾ byla pou¾ita)
**
** Definici typù naleznete v souboru c016.h.
**
** Tabulka je reprezentována datovou strukturou typu tHTable,
** která se skládá z ukazatelù na polo¾ky, je¾ obsahují slo¾ky
** klíèe 'key', obsahu 'data' (pro jednoduchost typu float), a
** ukazatele na dal¹í synonymum 'ptrnext'. Pøi implementaci funkcí
** uva¾ujte maximální rozmìr pole HTSIZE.
**
** U v¹ech procedur vyu¾ívejte rozptylovou funkci hashCode.  Pov¹imnìte si
** zpùsobu pøedávání parametrù a zamyslete se nad tím, zda je mo¾né parametry
** pøedávat jiným zpùsobem (hodnotou/odkazem) a v pøípadì, ¾e jsou obì
** mo¾nosti funkènì pøípustné, jaké jsou výhody èi nevýhody toho èi onoho
** zpùsobu.
**
** V pøíkladech jsou pou¾ity polo¾ky, kde klíèem je øetìzec, ke kterému
** je pøidán obsah - reálné èíslo.
*/

#include "c016.h"

int HTSIZE = MAX_HTSIZE;
int solved;

/*          -------
** Rozptylovací funkce - jejím úkolem je zpracovat zadaný klíè a pøidìlit
** mu index v rozmezí 0..HTSize-1.  V ideálním pøípadì by mìlo dojít
** k rovnomìrnému rozptýlení tìchto klíèù po celé tabulce.  V rámci
** pokusù se mù¾ete zamyslet nad kvalitou této funkce.  (Funkce nebyla
** volena s ohledem na maximální kvalitu výsledku). }
*/

int hashCode (tKey key) 
{
  int retval = 1;
  int keylen = strlen(key);
  for (int i=0; i<keylen; i++) retval += key[i];
  return (retval % HTSIZE);
}

/*
** Inicializace tabulky s explicitnì zøetìzenými synonymy.  Tato procedura
** se volá pouze pøed prvním pou¾itím tabulky.
*/

void htInit (tHTable* ptrht) 
{
  if (!ptrht) return;
  for (int i = 0; i < HTSIZE; i++) (*ptrht)[i] = NULL;
  return;
}

/* TRP s explicitnì zøetìzenými synonymy.
** Vyhledání prvku v TRP ptrht podle zadaného klíèe key.  Pokud je
** daný prvek nalezen, vrací se ukazatel na daný prvek. Pokud prvek nalezen není, 
** vrací se hodnota NULL.
**
*/

tHTItem* htSearch ( tHTable* ptrht, tKey key ) 
{
  if ((!ptrht) || !(*ptrht)) return NULL;
  else
  {
    int arrayIndex = hashCode(key);   
    tHTItem* item = (*ptrht)[arrayIndex];

    if ((item) && (strcmp(item->key, key)))
    {
      item = item->ptrnext;
     
      while (item) 
      { 
        if (!(strcmp(item->key, key))) return item;
        else if (strcmp(item->key, key)) item = item->ptrnext;
      }
      return NULL; // nenalezeno
    }
    else 
    {
      if (!(ptrht[arrayIndex])) return NULL;
      else return (*ptrht)[arrayIndex]; 
    }
  }
}

/* 
** TRP s explicitnì zøetìzenými synonymy.
** Tato procedura vkládá do tabulky ptrht polo¾ku s klíèem key a s daty
** data.  Proto¾e jde o vyhledávací tabulku, nemù¾e být prvek se stejným
** klíèem ulo¾en v tabulce více ne¾ jedenkrát.  Pokud se vkládá prvek,
** jeho¾ klíè se ji¾ v tabulce nachází, aktualizujte jeho datovou èást.
**
** Vyu¾ijte døíve vytvoøenou funkci htSearch.  Pøi vkládání nového
** prvku do seznamu synonym pou¾ijte co nejefektivnìj¹í zpùsob,
** tedy proveïte.vlo¾ení prvku na zaèátek seznamu.
**/

void htInsert (tHTable* ptrht, tKey key, tData data) 
{
  if (!ptrht) return;
  else
  {
    tHTItem* item = htSearch(ptrht, key);

    if (item) item->data = data;
    else
    {
      int arrayIndex = hashCode(key); 

      item = malloc(sizeof(tHTItem)); 
      item->key = malloc(strlen(key)+1); 

      if (!(item->key)) free(item);
      else 
      {
        item->data = data;
        strcpy(item->key, key);
        item->ptrnext = (*ptrht)[arrayIndex]; // navazani ukazatele
        (*ptrht)[arrayIndex] = item; // navazani do tabulky
      }
      return;
    } 
    return; 
  }  
}

/*
** TRP s explicitnì zøetìzenými synonymy.
** Tato funkce zji¹»uje hodnotu datové èásti polo¾ky zadané klíèem.
** Pokud je polo¾ka nalezena, vrací funkce ukazatel na polo¾ku
** Pokud polo¾ka nalezena nebyla, vrací se funkèní hodnota NULL
**
** Vyu¾ijte døíve vytvoøenou funkci HTSearch.
*/

tData* htRead (tHTable* ptrht, tKey key) 
{
  if (!ptrht) return NULL;
  else
  {
    tHTItem* item = htSearch(ptrht, key); // hledani podle klice

    if (item) return &(item->data); // nalezeno
    else return NULL;  
  }
}


/*
** TRP s explicitnì zøetìzenými synonymy.
** Tato procedura vyjme polo¾ku s klíèem key z tabulky
** ptrht.  Uvolnìnou polo¾ku korektnì zru¹te.  Pokud polo¾ka s uvedeným
** klíèem neexistuje, dìlejte, jako kdyby se nic nestalo (tj. nedìlejte
** nic).
**
** V tomto pøípadì NEVYU®ÍVEJTE døíve vytvoøenou funkci HTSearch.
*/

void htDelete (tHTable* ptrht, tKey key) 
{
  tHTItem *tmp, *deleted;
  int arrayIndex = hashCode(key);

  if (!((*ptrht) && ((*ptrht)[arrayIndex]))) return;
  else
  {
    if (strcmp(key, (*ptrht)[arrayIndex]->key)) // klic nenalezen
    {
      deleted = (*ptrht)[arrayIndex];
      tmp = (*ptrht)[arrayIndex]->ptrnext;
       
      while (tmp) 
      {
        if (strcmp(key, tmp->key)) // dokud nenaleznu klic
        { 
          deleted = tmp;  
          tmp = tmp->ptrnext;
        }
        else break; // nalezeno
      }
      
      if (tmp) 
      {
        deleted->ptrnext = tmp->ptrnext;
        free(tmp->key); // navazani ukazatele
        free(tmp); // uvolneni
      } 
    }
    else // pokud klic najdu
    {
      if ((*ptrht)[arrayIndex]->ptrnext) 
      {
        tmp = (*ptrht)[arrayIndex];
        (*ptrht)[arrayIndex] = tmp->ptrnext;
        free(tmp->key);
        free(tmp);
      }
      else // neexistuje dalsi prvek, uvolnim
      { 
        free((*ptrht)[arrayIndex]->key);
        free((*ptrht)[arrayIndex]);
        (*ptrht)[arrayIndex] = NULL;
      }
    }
  }
}
/* TRP s explicitnì zøetìzenými synonymy.
** Tato procedura zru¹í v¹echny polo¾ky tabulky, korektnì uvolní prostor,
** který tyto polo¾ky zabíraly, a uvede tabulku do poèáteèního stavu.
*/

void htClearAll (tHTable* ptrht) 
{
  if (!ptrht) return;
  else
  {
    for (size_t i = 0; i < HTSIZE; i++)
    {
      for(tHTItem* item = (*ptrht)[i]; item;)
      {
        tHTItem* tmp = item;
        item = item->ptrnext;
        free(tmp->key);
        free(tmp);
      }
    }
    memset(*ptrht, 0, sizeof(struct tHTItem*)*HTSIZE);
  } 
}
