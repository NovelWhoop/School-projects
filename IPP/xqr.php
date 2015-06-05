<?php

#XQR:xhalik01

/*
 * Projekt do predmetu IPP
 * Varianta: XML Query
 * Autor: Roman Halik, xhalik@stud.fit.vutbr.cz
 * Datum: brezen 2015
 */

function getHelp()
{
  $stdout = fopen('php://stdout', 'w');
  fwrite($stdout, 
  "\n                                      NAPOVEDA\n
  ----------------------------------------------------------------------------------\n
  --help              - zobrazi napovedu\n
  --input=filename    - vstupni soubor ve formatu XML\n
  --output=filename   - vystupni soubor ve formatu XML\n
  --query='query'     - dotaz nad XML, nelze kombinovat s --qf\n
  --qf=filename       - dotaz nad XML v externím souboru, nelze kombinovat s --query\n
  -n                  - zakaz generovani XML hlavicky na vystup\n
  -root=element       - jmeno korenoveho elementu obalujici vysledky\n
  ----------------------------------------------------------------------------------\n");
  exit(0);
}

/*
 * - Funkce provadi vypis chybovych hlasek na stderr podle zadanych parametru (erorrCode)
 * - Ukoncuje provadeni skriptu
 */
function printError($errorCode)
{
  $stderr = fopen('php://stderr', 'w+');
  switch($errorCode)
  {
    case 1:
      fwrite($stderr, "Spatne zadane parametry, --help spusti napovedu.\n");
      exit(1);
      break;
    case 2:
      fwrite($stderr, "Semanticka chyba.\n");
      exit(80);
      break;
    case 3:
      fwrite($stderr, "Syntakticka chyba.\n");
      exit(80);
      break;
    case 4:
      fwrite($stderr, "Soubor neexistuje, nebo doslo k chybe pri otevirani souboru.\n");
      exit(2);
      break;
  }
}

/*
 * - Funkce zpracovava dotaz a uklada hodnoty jednotlivych klauzuli do promennych,
 *   ktere nasledne vlozi do pole, ktere vrati
 * - Zaroven provadi syntaktickou a semantickou analyzu dotazu
 */
function parseQuery($queryExploded)
{
  $queryFlipped = array_flip($queryExploded); // prehozeni indexu a hodnot pro pozdejsi vyhodnoceni poradi klauzuli

  // priznaky pouziti klauzule (nesmi byt zadane vicekrat, zaroven slouzi pro rozpoznani, ktere klauzule byly pouzity a ma se kontrolovat jejich poradi)
  $selectFlag = false;
  $limitFlag = false;
  $fromFlag = false;
  $whereFlag = false;
  $notFlag = false;
  $containsFlag = false;

  // nastaveni vsech promennych na false jako indikace jejich nepouziti
  $select = false;
  $limit = false;
  $from = false;
  $where = false;
  $contains = false;

  // zpracovani pole hodnot klauzuli, pristup jako by to byla fronta => s kazdym pruchodem odeberu prvek, pri nalezeni klauzule odeberu jeji hodnotu do promenne
  while($queryExploded)
  {
    $queryChunk = array_shift($queryExploded);
    switch($queryChunk)
    {
      case "SELECT":
        if($selectFlag == true) printError(2);
        $select = array_shift($queryExploded);
        $selectFlag = true;
        break;

      case "LIMIT":
        if($limitFlag == true) printError(2);
        $limit = array_shift($queryExploded);
        if (is_float($limit) || $limit < 0) printError(2);
        $limitFlag = true;
        break;

      case "FROM":
        if($fromFlag == true) printError(2);
        $from = array_shift($queryExploded);
        $fromFlag = true;
        break;

      case "WHERE":
        if($whereFlag == true) printError(2);
        if($queryExploded[0] == "NOT") // pokud je za WHERE NOT, posunu se až na NOT
        {
          array_shift($queryExploded);
          $notFlag = true;
        }
        $where = array_shift($queryExploded);
        if($where == '') printError(3);
        $whereFlag = true;
        break;

      case "CONTAINS":
        if($containsFlag == true) printError(2);
        $contains = array_shift($queryExploded);
        if($contains == '') printError(3);
        $containsFlag = true;
        break;

      default:
        printError(3);
    }
  }

  if(!($selectFlag && $fromFlag)) printError(2); // kontrola zadani SELECT a FROM, ktere jsou povinne

  // kontrola poradi klauzuli
  if($queryFlipped['SELECT'] > $queryFlipped['FROM']) printError(2);
  else if($limitFlag)
  {
    if($queryFlipped['LIMIT'] > $queryFlipped['FROM']) printError(2);
  }
  else if($whereFlag)
  {
    if($queryFlipped['FROM'] > $queryFlipped['WHERE']) printError(2);
  }
  else if($notFlag)
  {
    if($queryFlipped['WHERE'] > $queryFlipped['NOT']) printError(2);
  }
  else if($whereFlag && $containsFlag)
  {
    if($queryFlipped['WHERE'] > $queryFlipped['CONTAINS']) printError(2);
  }

  // vracim pole hodnot pro pouziti ve funkci xmlFilter
  return array($select, $limit, $from, $where, $contains, $notFlag, $limitFlag);
}

/*
 * - Funkce provadi zpracovani dotazu nad XML daty pomoci xpath a vypisuje je
 *   na stdout, nebo do souboru. Zaroven soubory zavira.
 * - Pouziva pole hodnot z funkce queryParse a promenne z 
 */
function xmlFilter($INPUTFILE, $OUTPUTFILE, $queryExploded, $rootElement, $nFlag)
{
  // nastaveni hlavicky a root elementu
  if($nFlag == true) $finalResult = "";
  else $finalResult = '<?xml version="1.0" encoding="utf-8"?>';
  if($rootElement) $finalResult .= "<$rootElement>";

  // "rozbaleni" promennych
  list($select, $limit, $from, $where, $contains, $notFlag, $limitFlag) = parseQuery($queryExploded); 

  // odstraneni bilych znaku ze zacatku a konce promennych (typicky problem FROM, pro jistotu u vsech)
  $select = trim($select);
  $from = trim($from);
  $limit = trim($limit);
  $where = trim($where);
  $contains = trim($contains);

  // pokud nebyla zadana hodnota FROM, vypisuji do souboru v zavislosti na --n a --root, a ukoncuji skript
  if($from == '')
  {
    if($OUTPUTFILE)
    {
      if($nFlag == false && $rootElement) fwrite($OUTPUTFILE, '<?xml version="1.0" encoding="utf-8"?><$rootElement></$rootElement>');
      else if($nFlag == false && !$rootElement) fwrite($OUTPUTFILE, '<?xml version="1.0" encoding="utf-8"?>');
      else if($nFlag == true && $rootElement) fwrite($OUTPUTFILE, '<$rootElement></$rootElement>');
      else fwrite($OUTPUTFILE, "");
      fclose($OUTPUTFILE);
    }
    else
    {
      $stdout = fopen('php://stdout', 'w');
      if($nFlag == false && $rootElement) fwrite($stdout, '<?xml version="1.0" encoding="utf-8"?><$rootElement></$rootElement>');
      else if($nFlag == false && !$rootElement) fwrite($stdout, '<?xml version="1.0" encoding="utf-8"?>');
      else if($nFlag == true && $rootElement) fwrite($stdout, '<$rootElement></$rootElement>');
      else fwrite($stdout, "");
    }
    exit(1);
  }

  // analyza FROM a vytvoreni vyrazu pro xpath
  $fromType = (strpos("$from", "."));
  if($fromType === false) $fromType = -1;
  switch($fromType)
  {
    case 0:
      $from = str_replace(".", "//*[@", "$from");
      $from .= "][1]"; // a pridani ] na konec 
      break;
    case -1:
      if($from == "ROOT") $from = "*"; // pokud je FROM ROOT, nahradim jej * pro xpath
      else $from = "($from)[1]";
      break;
    default:
      $from = str_replace(".", "[@", "$from"); // nahrazeni . za [@
      $from .= "]"; // a pridani ] na konec
    break;
  }

  // byl zadan vstupni soubor, generuji xpath a zapisuji vysledek
  if($INPUTFILE)
  {
    $string = ""; // promenna pro ulozeni zpracovaneho souboru do retezce

    while (($buffer = fgets($INPUTFILE, 4096)) !== false) $string .= $buffer;

    $xml = new SimpleXMLElement($string);
    $result = $xml->xpath("$from/.//$select");

    while(list( , $node) = each($result))
    {
      if($limitFlag == true)
      {
        if($limit == 0) break;
        $limit--;
      }
      $finalResult .= $node->asXML();
    }

    if($rootElement) $finalResult .= "</$rootElement>";
    fclose($INPUTFILE);
  }
  else // neni inputfile, tak bereme vstup z stdin
  {
    $stdin = fopen('php://stdin', 'r');
    $line = trim(fgets(STDIN)); // reads one line from STDIN

    $string = ""; // promenna pro ulozeni zpracovaneho souboru do retezce

    while ($line = trim(fgets(STDIN)) !== false) $string .= $line;

    $xml = new SimpleXMLElement($string);
    $result = $xml->xpath("($from)[1]/.//$select");

    while(list( , $node) = each($result))
    {
      if($limitFlag == true)
      {
        if($limit == 0) break;
        $limit--;
      }
      $finalResult .= $node->asXML();
    }
    if($rootElement) $finalResult .= "</$rootElement>";
  }

  // ukladam vystup do souboru, zaviram
  if($OUTPUTFILE) 
  {
    fwrite($OUTPUTFILE, "$finalResult\n");
    fclose($OUTPUTFILE);
  }
  else // output file neni, uvazuji stdout
  {
    $stdout = fopen('php://stdout', 'w');
    fwrite($stdout, "$finalResult\n");
  }
}

/*
 * - Zpracovani parametru a kontrola spravnosti zadani
 * - generovani pole z dotazu pro funkci queryParse, a zavolani teto funkce
 */
// priznaky "zapnuti" parametru
$argCounter = $argc;
$inputFlag = false;
$outputFlag = false;
$queryFlag = false;
$qfFlag = false;
$nFlag = false;
$rootFlag = false;

// promenne pro uchovani hodnot pro pozdejsi pouziti ve XML filtru
$INPUTFILE = false;
$OUTPUTFILE = false;
$queryExploded = false;
$rootElement = false;

while($argc != 1)
{
  $argc--; // v argc je hodnota ze zacatku spusteni skriptu, sam se neprepocita..
  $param = array_pop($argv);
  $dashCounter = substr("$param", 1, 1); // ukrojim druhy parametr, je to - ?
  if ($dashCounter == "-") $shortParam = substr("$param", 2, strlen($param)); // ukrojim dva znaky parametru
  else $shortParam = substr("$param", 1, strlen($param));
  $parameter = substr("$shortParam", 0, 2); // na zaklade prvnich 2 znaku parametru rozhodnu, o jaky se jedna, a pozdeji zkontroluji jeho zbytek

  switch ($parameter) 
  {
    case "he": // HELP
      if($argCounter == 2 && ("help" == substr("$shortParam", 0, 4)) && (strlen($shortParam) == 4)) getHelp();
      else printError(1);
      break;

    case "in": // INPUT
      if($inputFlag == false && ("input=" == substr("$shortParam", 0, 6))) // nebyl uz parametr zadan a je zadan spravne?
      {
        $filename = substr("$shortParam", 0, strlen($shortParam));
        parse_str($filename);
        $filename = $input; // vlozeni hodnoty input do promenne filename, pro prehlednejsi praci dal
        $inputFlag = true;

        if(fopen("$filename", "r") == FALSE) printError(4);
        else $INPUTFILE = fopen("$filename", "r");     
      }
      else printError(1);
      break;
    
    case "ou": // OUTPUT
      if($outputFlag == false && ("output=" == substr("$shortParam", 0, 7)))
      {
        $filename = substr("$shortParam", 0, strlen($shortParam));
        parse_str($filename);
        $filename = $output;
        $OUTPUTFILE = fopen("$filename", "w");
        $outputFlag = true;  
      }
      break;

    case "qu": // QUERY
      if($queryFlag == false && $qfFlag == false && ("query=" == substr("$shortParam", 0, 6)))
      {
        $query = substr("$shortParam", 0, strlen($shortParam)); 
        parse_str($query);
        $queryExploded = explode(' ', $query);
        $queryFlag = true;
      }
      else printError(1);
      break;

    case "qf": // QUERY FROM FILE
      if($qfFlag == false && $queryFlag == false)
      {
        $filename = substr("$shortParam", 0, strlen($shortParam));
        parse_str($filename);
        $filename = $qf;

        if(fopen("$filename", "r") == FALSE) printError(4);
        else
        { // soubor si rovnou zpracuji a ulozim do $queryExploded, takze s tim ve vysledku zachazim stejne, jako kdybych to nacital z parametru
          $FILE = fopen("$filename", "r");
          while (($buffer = fgets($FILE, 4096)) !== false) $queryExploded = explode(' ', $buffer);
          fclose($FILE);
        }
        $qfFlag = true;
      }
      else printError(1);
      break;

    case "n": // NO GENERATE
      if($nFlag == false) $nFlag = true;
      else printError(1);
      break;

    case "ro": // ROOT 
      if($rootFlag == false && ("root=" == substr("$shortParam", 0, 5)))
      {
        $element = substr("$shortParam", 0, strlen($shortParam));
        parse_str($element);
        $rootElement = $root;
        $rootFlag = true;
      }
      else printError(1);
      break;

    default:
      printError(1);
  }
  if($qfFlag = false && $queryFlag = false) printError(1);
}

// XML FILTER - ridici funkce skriptu, prebira hodnoty parametru a ukazatele na zvolene soubory, spousti pomocne funkce a generuje vystup
xmlFilter($INPUTFILE, $OUTPUTFILE, $queryExploded, $rootElement, $nFlag);

exit(0);
