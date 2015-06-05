#!/usr/bin/env python3

#CST:xhalik01

import getopt, sys, os, re

keywordsList = ['auto','break','case','const','continue','char','default','do','double','else','enum','extern','float','for','goto','if','inline','int','long','register','restrict','return','short','signed','sizeof','static','struct','switch','typedef','union','unsigned','void','volatile','while','_Bool','_Complex','_Imaginary']
opRegex = ('((?:\+|-|\*|\/|%|<<|>>|<|>|!|&|\||\^)=|\|\||&&|<<|>>|--|\+\+|->|==|(?:\%|\&|\+|\-|\=|\/|\||\.[_a-zA-Z\s]|\*|\:q:|<|>|\!|~|\^))')

def main():
    # Zpracovani parametru prikazove radky
    try:
        opts, args = getopt.getopt(sys.argv[1:], "koiw:cp", ["help", "helX", "input=", "inpuX=", "output=", "outpuX=", "nosubdir", "nosubdiX"])
    except getopt.GetoptError:
        printErr(1)

    # promenne pro zpracovani parametru prikazove radky a priznaky zapnuti prepinacu
    output = None
    koiwc = False
    inputFlag = False
    outputFlag = False
    nosubdirFlag = False
    kFlag = False
    oFlag = False
    iFlag = False
    wFlag = False
    cFlag = False
    pFlag = False
    input = ""
    content = ""
    filename = ""
    count = 0
    pattern = ""
    result = []

    # osetreni parametru, ktere nezacinaji '-'
    if args:
        printErr(1)

    # parsovani parametru
    for opt, arg in opts:
        if opt == "--help":
            if len(sys.argv) != 2:
                printErr(1)
            printHelp()
        elif opt == "--input":
            if inputFlag == True:
                printErr(1)
            input = arg
            inputFlag = True
        elif opt == "--output":
            if outputFlag == True:
                printErr(1)
            output = arg
            outputFlag = True
        elif opt == "--nosubdir":
            if nosubdirFlag == True:
                printErr(1)
            nosubdirFlag = True
        elif opt == "-k":
            if koiwc == True or kFlag == True:
                printErr(1)
            koiwc = True
            kFlag = True
        elif opt == "-o":
            if koiwc == True or oFlag == True:
                printErr(1)
            koiwc = True
            oFlag = True
        elif opt == "-i":
            if koiwc == True or iFlag == True:
                printErr(1)
            koiwc = True
            iFlag = True
        elif opt == "-w":
            if koiwc == True or wFlag == True or arg[0:1] != '=':
                printErr(1)
            pattern = arg[1:len(arg)] # oriznuti '='
            koiwc = True
            wFlag = True
        elif opt == "-c":
            if koiwc == True or cFlag == True:
                printErr(1)
            koiwc = True
            cFlag = True
        elif opt == "-p":
            if pFlag == True:
                printErr(1)
            pFlag = True
        else:
            printErr(1)

    # uzivatel nezadal zadny z prepinacu "koiwc"
    if not(koiwc):
        printErr(1)

    # volani obsluznych funkci pro analyzu souboru
    # zjistime, jake soubory mame zpracovavat
    filenames = source(input, nosubdirFlag, inputFlag)
    # zpracovani souboru, vkladani vysledku do seznamu, scitani poctu vyskytu
    for filename in filenames:
        result.append(contentAnalyze(inputAnalyze(filename), filename, kFlag, oFlag, iFlag, wFlag, cFlag, pFlag, pattern))
   
    # serazeni vysledku
    result.sort()

    # vypocet sumy jednotlivych radku (nalezu)
    totalCount = 0
    for count in result:
        totalCount += count[1] 

    # pripojeni sumy k vypisu
    result.append(("CELKEM:", totalCount))

    # zjisteni delky nejdelsiho radku
    # zjistuje se nejvetsi delka filename a count oddelebe, a tyto dve delky se pote sectou sectou
    # tim je docileno spravneho odsazeni i pripade nejdelsiho jmena souboru a zaroven nejkratsiho cisla
    filenameLenght = 0
    countLenght = 0
    maxLenght = 0
    tmpLenght = 0
    resultStr = ''

    for filename, count in result:
        if len(filename) > filenameLenght:
            filenameLenght = len(filename)
        if len(str(count)) > countLenght:
            countLenght = len(str(count))

    maxLenght = filenameLenght + countLenght

    # vypsani vysledku do retezce s odpovidajicimi mezerami pro kazdy radek
    # + 1 je kvuli odsazeni pomyslnych sloupcu tabulky od sebe
    for filename, count in result:
        gap = maxLenght - len(filename) - len(str(count))
        resultStr += (filename + ((gap + 1) * ' ') + str(count) + '\n')
    
    # vypsani vysledku do souboru nebo na stdout
    if outputFlag:
        try:
            with open(output, 'w', encoding = 'iso-8859-2') as outputFile:
                outputFile.write(resultStr)
        except:
            printErr(3)
    else:
        sys.stdout.write(resultStr)
    return


# return na vypis errorove hlasky podle zadaneho errcode
def printErr(errorCode):
    if errorCode == 1:
        print("Spatne zadane parametry. Pro napovedu napiste --help.", file = sys.stderr)
        sys.exit(1)
    elif errorCode == 2:
        print("Neexistujici vstupni soubor, nebo chyba otevreni zadaneho souboru pro cteni.", file = sys.stderr)
        sys.exit(2)
    elif errorCode == 3:
        print("Chyba pri pokusu o otevreni vystupniho souboru pro zapis.", file = sys.stderr)
        sys.exit(3)
    elif errorCode == 4:
        print("Chybny format vstupniho souboru.", file = sys.stderr)
        sys.exit(4)
    elif errorCode == 21:
        print("Nektery z analyzovanych souboru nelze cist.", file = sys.stderr)
        sys.exit(21)


# tisk napovedy
def printHelp():
    sys.stdout.write("\n                                  NAPOVEDA\n"
    "--------------------------------------------------------------------------------------------\n"
    "--help             - zobrazi napovedu\n"
    "--input=fileordir  - vstupni soubor nebo adresar se zdrojovym kodem v jazyce C\n"
    "--output=filename  - vystupni textovy soubor\n"
    "--nosubdir         - prohledavani pouze v zadanem adresari, ale ne uz v jeho podadresarich,\n"
    "                     nelze kombinovat se zadanim konkretniho souboru pomoci --input\n"
    "-k                 - vypise pocet vsech vyskytu klicovych slov, nelze s -o -i -w -c\n"
    "-o                 - vypise pocet vyskytu jednotlivych operatoru, nelze s -k -i -w -c\n"
    "-i                 - vypise pocet vyskytu identifikatoru, nelze s -k -o -w -c\n"
    "-w=pattern         - vypise pocet neprekryvajicich se retezcu pattern,  nelze s -k -o -i -c\n"
    "-c                 - vypise celkovy pocet znaku komentaru vcetne uvozujicih znaku komentaru,\n"
    "                     nelze s -k -o -i -w\n"
    "-p                 - vypis souboru bez uplne (absolutni) cesty k souboru\n"
    "--------------------------------------------------------------------------------------------\n")
    sys.exit(0)


# podle zadanych parametru nacte zdrojove soubory
# vraci list s cestami .c a .h souboru
def source(input, nosubdirFlag, inputFlag):
    # list nalezenych .c a .h souboru
    files = []

    # neni zadany input -> analyzujeme soubory aktualniho adresare
    if not inputFlag:
        input = os.getcwd()
        
    # input neni ani adresar, ani soubor
    if not os.path.isdir(input) and not os.path.isfile(input):
        printErr(2)

    # je zadan konekretni soubor
    if os.path.isfile(input):
        # zadani konkretniho souboru a soucasne prepinace "nosubdir" je zakazano
        if nosubdirFlag:
            printErr(1)
        files.append(os.path.abspath(input))
        return files

    # podle nosubdir flagu vyhledavame jen v adresari, nebo i v podadresarich
    if nosubdirFlag:
        files = [os.path.join(input, fname) for fname in os.listdir(input) if re.match('.+\.c$', fname) or re.match('.+\.h$', fname)]
    else:
        files = [os.path.join(dname, fname) for dname, dnames, fnames in os.walk(input) for fname in fnames if re.match('.+\.c$', fname) or re.match('.+\.h$', fname)]

    return files


# analyza vstupniho/vstupnich souboru
def inputAnalyze(input):
    try:
        with open(input, 'r', encoding='iso-8859-2') as inputFile:
            content = inputFile.read()
    except:
        if os.path.isfile(input):
            printErr(2)
        else:
            printErr(21)
    return content


# na zakladne flagu spousti jednotlive funkce na zpracovani vstupniho/vstupnich souboru
# vraci absolutni cestu a pocet
def contentAnalyze(content, filename, kFlag, oFlag, iFlag, wFlag, cFlag, pFlag, pattern):
    if kFlag:
        count = findKeywords(content)
    elif oFlag:
        count = findOperators(content)
    elif iFlag:
        count = findIdentificators(content)
    elif wFlag:
        count = findPattern(content, pattern)
    elif cFlag:
        count = findComments(content)
    
    # pokud je zapnuty prepinac p, vracime pouze jmeno souboru
    # jinak vracime celou cestu
    if pFlag:
        return os.path.basename(filename), count
    else:
        return os.path.abspath(filename), count


# vyhleda klicova slova
# vola pomocnou funkci koiSearch, ktera odstrani nevyhovujici retezce a literaly, a provede prohledani
def findKeywords(content):
    keywords = []
    allWords = koiSearch(r'\b[_a-zA-Z]{1}\w*\b', content)
    for item in allWords:
        if item in keywordsList:
            keywords.append(item)
    return len(keywords)


# vyhleda operatory
# operatorem se nemysli ukazatel
# vola pomocnou funkci koiSearch, ktera odstrani nevyhovujici retezce a literaly, a provede prohledani
def findOperators(content):
    content = re.sub(r'\b(?:char|int|float|double|short|long|void|const|_Bool|_Complex)(?:\s*,?\s*\(?\s*\*+\s*\(?\w*\)?)+', '', content, re.IGNORECASE)
    return len(koiSearch(opRegex, content))


# vyhleda identifikatory
# vola pomocnou funkci koiSearch, ktera odstrani nevyhovujici retezce a literaly, a provede prohledani
def findIdentificators(content):
    identificators = []
    allWords = koiSearch(r'\b[_a-zA-Z]{1}\w*\b', content)
    for item in allWords:
        if item not in keywordsList:
            identificators.append(item)
    return len(identificators)


# vyhleda zadany retezec (pattern)
def findPattern(content, pattern):
    return len(re.findall(pattern, content))


# vyhleda vsechny komentare ve zdrojovem textu
# implementuje rozsireni COM - vyhledava komentare i v makrech
def findComments(content):
    # odstraneni vsech retezcu literalu, kvuli konstrukcim typu "# macro // komentar"
    content = re.sub('"(\\.*|[^"]*)"', '""', content)
    content = re.sub('\'.\'', '\'\'', content)

    count = 0
    state = 1
    oneLine = False
    moreLine = False
    macro = False
    splitted = False

    for char in content:
        if state == 1:
            if char == '/':
                state = 2
            elif char == '#':
                macro = True
            elif char == '\n' and macro: # dosli sme na konec radku a nenasli sme v makru komentar
                macro = False
        elif state == 2:
            if char == '/':
                count += 2 # nasli sme komentar, pricteme znaky // resp /*
                oneLine = True
                state = 3
            elif char == '*':
                count += 2 # nasli sme komentar, pricteme znaky // resp /*
                moreLine = True
                state = 3
            else:
                state = 1 # nejedna se o komentar
        elif state == 3:
            if char == '\n' and oneLine: # konec radku -> konec jednoradkoveho komentare
                count += 1
                oneLine = False
                state = 1
            elif char == '*' and moreLine: # teoreticky konec, treba vysetrit
                count += 1
                state = 4
            elif char == '\\' and oneLine:
                splitted = True
                count += 1
                state = 6
            elif char == '\\' and macro and oneLine: # nasli sme znak pokracovani komentare na dalsim radku v makru
                state = 5
            else: # pocitame znaky v komentari
                count += 1
        elif state == 4:
            if char == '/': # konec viceradkoveho komentare
                count += 1
                moreLine = False
                state = 1
            else: # jeste neni konec viceradkoveho komentare
                count += 1
                state = 3
        elif state == 5:
            if char == '\n' and macro: # konec radku makra, uz jen spocteme znaky na dalsim radku
                macro = False
                count += 1
            elif char == '\n' and not macro:
                count += 1 # konec komentare v macru
                state = 1 # jdeme na zacatek
            else:
                count += 1 # pocitame znaky komentare macra
        elif state == 6:
            if char == '\n' and splitted:
                count +=1
                splitted = False
            elif char == '\n':
                count += 1
                state = 1
            else:
                count += 1
    return count


# odstrani nevyhovujici retezce -> konce radku, makra, komentare a retezce 
# pro prepinace k,o,i
def koiSearch(search, content):
    # odstraneni vsech komentaru, retezcu a maker
    regex = re.compile(r'//.*$|/\*(?:.|[\r\n])*?\*/|\".*\"|\'\\?.\'|#.*$', re.M)
    content = regex.sub('', content)

    # vyhledani zadanych elementu dle parametru
    return re.findall(search, content)


# volani funkce main, ktera provede analyzu zadanych parametru a spusti adekvatni obsluzne funkce
if __name__ == "__main__":
    main()