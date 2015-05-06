#!/bin/bash
# author: Roman Halik, xhalik01

createGraph=false
includePLT=false

while getopts gpr:d: opt
do
  case "$opt" in
    g) createGraph=true #zapne volbu grafu
    ;;

    p) includePLT=true #zapne volbu plt
        ;;

    r) if [ $createGraph = true ] #hledani requires
         then
           echo "digraph CG {"  
           if [ $includePLT = true ]
             then
               objdump -d -j .text "${@: -1}" | grep -e 'callq' -e '>:' | grep "<*>" | sed 's/[^<*>]*//' | grep -v + | sed 's/[>]$*//' | sed 's/^[<]*//' | grep -v [0-9] | tac | sed -n '/^'$OPTARG'$/,/:$/p' | grep ":" | sed 's/[:]/ -> '$OPTARG'/' | sed 's/@plt/_PLT/'
             else
                 objdump -d -j .text "${@: -1}" | grep -e 'callq' -e '>:' | grep "<*>" | sed 's/[^<*>]*//' | grep -v + | sed 's/[>]$*//' | sed 's/^[<]*//' | grep -v [0-9] | tac | sed -n '/^'$OPTARG'$/,/:$/p' | grep ":" | sed 's/[:]/ -> '$OPTARG'/' | grep -v "@plt"
             fi
           echo "}"                    
        else
           if [ $includePLT = true ]
             then
               objdump -d -j .text "${@: -1}" | grep -e 'callq' -e '>:'  | grep "<*>" | sed 's/[^<*>]*//' | grep -v + |  sed 's/[>]$*//' | sed 's/^[<]*//' | grep -v [0-9] | tac | sed -n '/^'$OPTARG'$/,/:$/p' | grep ":" | sed 's/[:]/ -> '$OPTARG'/'
             else
               objdump -d -j .text "${@: -1}" | grep -e 'callq' -e '>:'  | grep "<*>" | sed 's/[^<*>]*//' | grep -v + |  sed 's/[>]$*//' | sed 's/^[<]*//' | grep -v [0-9] | tac | sed -n '/^'$OPTARG'$/,/:$/p' | grep ":" | sed 's/[:]/ -> '$OPTARG'/' | grep -v "@plt"
           fi
        fi
        exit
       ;;

    d) if [ $createGraph = true ] #hledani dependencies
         then
           echo "digraph CG {"
           if [ $includePLT = true ]
             then
               objdump -d -j .text "${@: -1}" | grep "<*>" | sed 's/[^<*>]*//' | grep -v + | sed 's/[>]$*//' | sed -n '/'$OPTARG':/,/:/p' | grep -v ":" | grep -v "__" | sort -u | sed 's/^[<]*/'$OPTARG' -> /' | sed 's/@plt/_PLT/'
             else
               objdump -d -j .text "${@: -1}" | grep "<*>" | sed 's/[^<*>]*//' | grep -v + | sed 's/[>]$*//' | sed -n '/'$OPTARG':/,/:/p' | grep -v ":" | grep -v "__" | grep -v "@" | sort -u | sed 's/^[<]*/'$OPTARG' -> /'
             fi
           echo "}"
        else
           if [ $includePLT = true ]
             then
               objdump -d -j .text "${@: -1}" | grep "<*>" | sed 's/[^<*>]*//' | grep -v + | sed 's/[>]$*//' | sed -n '/'$OPTARG':/,/:/p' | grep -v ":" | grep -v "__" |sort -u | sed 's/^[<]*/'$OPTARG' -> /'
             else
              objdump -d -j .text "${@: -1}" | grep "<*>" | sed 's/[^<*>]*//' | grep -v + | sed 's/[>]$*//' | sed -n '/'$OPTARG':/,/:/p' | grep -v ":" |grep -v "__" | grep -v "@" | sort -u | sed 's/^[<]*/'$OPTARG' -> /'
           fi
        fi
        exit
       ;;

    *)
       ;;
  esac
done

((OPTIND--))
shift $OPTIND

# jestlize nebyla zvolen -r ani -d, hledam vsechny zavislosti
 if [ $createGraph = true ]
         then
           echo "digraph CG {"  
           if [ $includePLT = true ]
             then
               objdump -d -j .text "${@: -1}" | grep -e 'callq' -e '>:' | sed -e 's/.*<//' -e 's/>//' | grep -v [0..9] | awk '{ if ($0 ~ /.*:$/){ name=$0; sub(":","", name);} else print name " -> " $0";"}' | sort -u | sed 's/@plt/_PLT/'
             else
               objdump -d -j .text "${@: -1}" | grep -e 'callq' -e '>:' | sed -e 's/.*<//' -e 's/>//' | grep -v [0..9] | awk '{ if ($0 ~ /.*:$/){ name=$0; sub(":","", name);} else print name " -> " $0";"}' | sort -u | grep -v @plt
             fi
           echo "}"                    
        else
           if [ $includePLT = true ]
             then
               objdump -d -j .text "${@: -1}" | grep -e 'callq' -e '>:' | sed -e 's/.*<//' -e 's/>//' | grep -v [0..9] | awk '{ if ($0 ~ /.*:$/){ name=$0; sub(":","", name);} else print name " -> " $0}' | sort -u   
             else             
               objdump -d -j .text "${@: -1}" | grep -e 'callq' -e '>:' | sed -e 's/.*<//' -e 's/>//' | grep -v [0..9] | awk '{ if ($0 ~ /.*:$/){ name=$0; sub(":","", name);} else print name " -> " $0}' | sort -u | grep -v @plt
           fi                
        fi
