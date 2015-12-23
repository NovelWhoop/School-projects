/**
 * Roman Halik, xhalik01
 * 80% zmen
 * 18.12.2015
 **/

/*******************************************************************************
   main.c: LCD + keyboard demo
   Copyright (C) 2009 Brno University of Technology,
                      Faculty of Information Technology
   Author(s): Zdenek Vasicek <vasicek AT stud.fit.vutbr.cz>

   LICENSE TERMS

   Redistribution and use in source and binary forms, with or without
   modification, are permitted provided that the following conditions
   are met:
   1. Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
   2. Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in
      the documentation and/or other materials provided with the
      distribution.
   3. All advertising materials mentioning features or use of this software
      or firmware must display the following acknowledgement:

        This product includes software developed by the University of
        Technology, Faculty of Information Technology, Brno and its
        contributors.

   4. Neither the name of the Company nor the names of its contributors
      may be used to endorse or promote products derived from this
      software without specific prior written permission.

   This software or firmware is provided ``as is'', and any express or implied
   warranties, including, but not limited to, the implied warranties of
   merchantability and fitness for a particular purpose are disclaimed.
   In no event shall the company or contributors be liable for any
   direct, indirect, incidental, special, exemplary, or consequential
   damages (including, but not limited to, procurement of substitute
   goods or services; loss of use, data, or profits; or business
   interruption) however caused and on any theory of liability, whether
   in contract, strict liability, or tort (including negligence or
   otherwise) arising in any way out of the use of this software, even
   if advised of the possibility of such damage.

   $Id$


*******************************************************************************/

#include <fitkitlib.h>
#include <timer_b.h>
#include <keyboard/keyboard.h>
#include <lcd/display.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#define TRUE 1
#define FALSE 0
#define EPSILON 0.001

int char_cnt = 0;
char last_ch; //naposledy precteny znak
char number[16]; // pole pro ulozeni nacteneho cisla z klavesnice
char buf[16]; // buffer pro prevedeni a vypis cisla

/*******************************************************************************
 * Funkce na vymazani pomocneho bufferu pro ulozeni textove podoby cisla pro tisk
*******************************************************************************/
void bufferErase(void)
{
  int i;
  for(i = 0; i < 16; i++)
    buf[i] = '\0';
}

/*******************************************************************************
 * Funkce na vymazani nacteneho cisla po vypoctu
*******************************************************************************/
void numberErase(void)
{
  int i;
  for(i = 0; i < 16; i++)
    number[i] = '\0';
}

/*******************************************************************************
 * Funkce vymaze docasne promenne, aby mohl zapocit novy vstup a vypocet
*******************************************************************************/
void countInit(void)
{
  LCD_clear();
  numberErase();
  bufferErase();
  char_cnt = 0;
}

/*******************************************************************************
 * Funkce na tisk vysledku na terminal i LCD
*******************************************************************************/
void printResult(float result)
{
  // tisk vysledku na LDC display
  int r = result;
  int d = (result - r) * 1000;
  sprintf(buf, "%d.%d", r, d);
  LCD_write_string(buf);

  // tisk vysledku do terminalu
  term_send_str("Pocitame odmocninu z cisla: ");
  term_send_str(number);
  term_send_str(", vysledek je: ");
  term_send_str(buf);
  term_send_crlf();

  delay_ms(1500); // vysledek nechame zobrazeny na display po dobu 1,5 sekundy
}

/*******************************************************************************
 * Vypis uzivatelske napovedy (funkce se vola pri vykonavani prikazu "help")
*******************************************************************************/
void print_user_help(void)
{
}

/*******************************************************************************
 * Tisk chybovych hlasek na terminal a LCD
*******************************************************************************/
void printError(int error_code)
{
  switch(error_code)
  {
    case 1:
      term_send_str("Mimo rozsah.");
      LCD_write_string("Mimo rozsah.");
      break;
    case 2:
      term_send_str("Neplatny vstup.");
      LCD_write_string("Neplatny vstup.");
      break;
  }
  term_send_crlf();
  delay_ms(1000); // nechame zpravu 1s na display
  countInit();
}

/*******************************************************************************
 * Newtonova (Babylonska)  metoda pro vypocet odmocniny
*******************************************************************************/  
float newton(int number)
{
  float result = number;
  float previous_result;

  // opakujeme tak dlouho, dokud nedosahneme pozadovane presnosti
  do
  {
    previous_result = result;
    result = (number/result + result) / 2.0;
  } while((previous_result - result) >= EPSILON);
    
  return result;
}

/*******************************************************************************
 * Funkce vypocte odmocninu metodou puleni intervalu
*******************************************************************************/
float intervalBisection(int number)
  {
    float upper = number;
    float lower = 0.0;
    float guess = 0.0;
    
    // opakujeme dokud nedosahneme chyby mensi nez EPSILON
    while((upper - lower) >= EPSILON)
    {
      guess = (lower + upper) / 2.0;
      
      if(guess * guess > number) upper = guess;
      else lower = guess;
    }
    return guess;
  }

/*******************************************************************************
 * Funkce zkontroluje, jestli vstup nepresahuje rozsah typu int
*******************************************************************************/
int checkRange(int number)
{
  if(number > 32767) return 1;
  return 0;
}

/*******************************************************************************
 * Obsluha klavesnice
*******************************************************************************/
int keyboard_idle()
{
  char ch;
  int long_input = FALSE;
  ch = key_decode(read_word_keyboard_4x4());
  
  if (ch != last_ch) 
  {
    last_ch = ch;
    if (ch != 0) 
    {
      // prekroceni delky displaye
      if (char_cnt == 16)
      {
        printError(1);
        long_input = TRUE;
      }

      if(ch == 'A') // uzivatel zvolil pocitani pomoci metody A (newton)
      {
        if((atoi(number) - 32767) > 0) printError(1); // kontrola prekroceni rozsahu datoveho typu int
        else
        {
          printResult(newton(atoi(number))); // volani funkce pro vypocet a tisk vysledku na terminal a LCD
          countInit(); // vymazani LCD, pole (cisla), bufferu, a pocitadla zadanych cisel    
        }
      }
      else if(ch == 'B') // uzivatel zvolil pocitani pomoci metody B (Metoda puleni intervalu)
        if((atoi(number) - 32767) > 0) printError(1); // kontrola prekroceni rozsahu datoveho typu int
        else
        {
          printResult(intervalBisection(atoi(number))); // volani funkce pro vypocet a tisk vysledku na terminal a LCD
          countInit(); // vymazani LCD, pole (cisla), bufferu, a pocitadla zadanych cisel    
        }    
      else if(ch == 'C' || ch == 'D' || ch == '*' || ch == '#') printError(2); // neplatny vstup
      else // jinak je klavesa cislo - postupne ukladamae do pole a vypisujeme na LCD
      {
        if(long_input == FALSE) // konstrukce na preskoceni pocitani a tisku znaku, jestlize byl presazen limit delky vstupu
        {
          LCD_append_char(ch);
          number[char_cnt] = ch;
          char_cnt++;
        }
        else long_input = FALSE;
      }
    }
  }
  return 0;
}

/*******************************************************************************
 * Dekodovani a vykonani uzivatelskych prikazu
*******************************************************************************/
unsigned char decode_user_cmd(char *cmd_ucase, char *cmd)
{
  return CMD_UNKNOWN;
}

/*******************************************************************************
 * Inicializace periferii/komponent po naprogramovani FPGA
*******************************************************************************/
void fpga_initialized()
{
  LCD_init();
  LCD_clear();
}

/*******************************************************************************
 * Hlavni funkce
*******************************************************************************/
int main(void)
{
  unsigned int cnt = 0;
  char_cnt = 0;
  last_ch = 0;

  initialize_hardware();
  keyboard_init();
  numberErase();
  bufferErase();

  while (1)
  {
    delay_ms(10);
    cnt++;
    if (cnt > 50)
    {
      cnt = 0;
      flip_led_d6();                   // negace portu na ktere je LED
    }

    keyboard_idle();                   // obsluha klavesnice
    terminal_idle();                   // obsluha terminalu
  }         
}
