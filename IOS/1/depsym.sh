#!/bin/bash
# author: Roman Halik, xhalik01

createGraph=false

while getopts :gr:d: opt
do
  case "$opt" in
    g) createGraph=true
       ;;

    r) if [ $createGraph = true ]
         then
         shift
         shift
           echo "digraph GSYM {"
              vystup2=`nm "$OPTARG" | grep "[TBCDG]" | awk '{print $3}'`
              count=$(echo $vystup2 | wc -w)
              vystup21=$(echo $vystup2 | sed 's/ /:/g')

              for (( a=1 ; $a-1-$count ; a=$a+1 ))
                do V1=$( echo $vystup21 | cut -f$a -d: )
                nm "$@" | egrep ":| $V1" | tac | awk  '/.*[U].*/,/:/{print}' | grep ":" | sed "s/[:]/ -> "$OPTARG" [label="\"$V1"\"];/" | sed 's/\./D/g' | sed 's/[a-z][A-Z]-/_/g' | sed 's/+/P/'

             done
          echo "}"
        else
        shift
          vystup2=`nm "$OPTARG" | grep "[TBCDG]" | awk '{print $3}'`
          count=$(echo $vystup2 | wc -w)
          vystup21=$(echo $vystup2 | sed 's/ /:/g')

          for (( a=1 ; $a-1-$count ; a=$a+1 ))
            do V1=$( echo $vystup21 | cut -f$a -d: )
            nm "$@" | egrep ":| $V1" | tac | awk  '/.*[U].*/,/:/{print}' | grep ":" | sed "s/[:]/ -> "$OPTARG" ($V1)/"
          done
      fi
        exit
       ;;

    d)if [ $createGraph = true ]
         then
           shift
           shift
           echo "digraph GSYM {"
             vystup3=`nm "$OPTARG" | grep "[U]" | awk '{print $2}'`
             count=$(echo $vystup3 | wc -w)
             vystup31=$(echo $vystup3 | sed 's/ /:/g')

             for (( a=1 ; $a-1-$count ; a=$a+1 ))
               do V2=$( echo $vystup31 | cut -f$a -d: )

               result=`nm "$@" | egrep ":| $V2" | tac | awk  '/.*[TBCDG].*/,/:/{print}' | grep ":"`
            echo  "$OPTARG -> $result [label="\"$V2"\"];" | grep ":" | sed "s/[:]//" | sed 's/\./D/g' | sed 's/[a-z][A-Z]-/_/g' | sed 's/+/P/g'
          done
        echo "}"
      else
      shift
        vystup3=`nm "$OPTARG" | grep "[U]" | awk '{print $2}'`
        count=$(echo $vystup3 | wc -w)
        vystup31=$(echo $vystup3 | sed 's/ /:/g')

        for (( a=1 ; $a-1-$count ; a=$a+1 ))
          do V2=$( echo $vystup31 | cut -f$a -d: )

          result=`nm "$@" | egrep ":| $V2" | tac | awk  '/.*[TBCDG].*/,/:/{print}' | grep ":"`
          echo  "$OPTARG -> $result ($V2)" | grep ":" | sed "s/[:]//"
        done
      fi
        exit
       ;;

    *)
       exit
       ;;
  esac
done

((OPTIND--))
shift $OPTIND



 if [ $createGraph = true ]
         then

           echo "digraph GSYM {"

           moduly=$(nm *.o | grep ":" )
           poc_mod=$(echo $moduly | wc -w)

           for (( b=1 ; $b-1-$poc_mod ; b=$b+1 ))
                do P=$( echo $moduly | sed 's/ //g' | cut -f$b -d: )


              vystup2=`nm "$P" | grep "[TBCDG]" | awk '{print $3}'`
              count=$(echo $vystup2 | wc -w)
              vystup21=$(echo $vystup2 | sed 's/ /:/g')

              for (( a=1 ; $a-1-$count ; a=$a+1 ))
               do V1=$( echo $vystup21 | cut -f$a -d: )
              nm *.o | egrep ":| $V1" | tac | awk  '/.*[U].*/,/:/{print}' | grep ":" | sed "s/[:]/ -> "$P" [label="\"$V1"\"];/" | sed 's/\./D/g' | sed 's/[a-z][A-Z]-/_/g' | sed 's/+/P/'

              done

              vystup3=`nm "$P" | grep "[U]" | awk '{print $2}'`
              count=$(echo $vystup3 | wc -w)
              vystup31=$(echo $vystup3 | sed 's/ /:/g')

              for (( a=1 ; $a-1-$count ; a=$a+1 ))
               do V2=$( echo $vystup31 | cut -f$a -d: )

               result=`nm *.o | egrep ":| $V2" | tac | awk  '/.*[TBCDG].*/,/:/{print}' | grep ":"`
              echo "$P -> $result [label="\"$V2"\"];" | grep ":" | sed "s/[:]//" | sed 's/\./D/g' | sed 's/[a-z][A-Z]-/_/g' | sed 's/+/P/g'

             done

           done

          echo "}"

       else

      moduly=$(nm *.o | grep ":" )
           poc_mod=$(echo $moduly | wc -w)

           for (( b=1 ; $b-1-$poc_mod ; b=$b+1 ))
                do P=$( echo $moduly | sed 's/ //g' | cut -f$b -d: )


              vystup2=`nm "$P" | grep "[TBCDG]" | awk '{print $3}'`
              count=$(echo $vystup2 | wc -w)
              vystup21=$(echo $vystup2 | sed 's/ /:/g')

              for (( a=1 ; $a-1-$count ; a=$a+1 ))
               do V1=$( echo $vystup21 | cut -f$a -d: )
              nm *.o | egrep ":| $V1" | tac | awk  '/.*[U].*/,/:/{print}' | grep ":" | sed "s/[:]/ -> "$P" ($V1)/"

              done

              vystup3=`nm "$P" | grep "[U]" | awk '{print $2}'`
              count=$(echo $vystup3 | wc -w)
              vystup31=$(echo $vystup3 | sed 's/ /:/g')

              for (( a=1 ; $a-1-$count ; a=$a+1 ))
               do V2=$( echo $vystup31 | cut -f$a -d: )

               result=`nm *.o | egrep ":| $V2" | tac | awk  '/.*[TBCDG].*/,/:/{print}' | grep ":"`
              echo "$P -> $result ($V2)" | grep ":" | sed "s/[:]//"

             done

           done

      fi
