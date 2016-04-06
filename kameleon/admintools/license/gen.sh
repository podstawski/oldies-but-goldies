#!/bin/sh
exe_path=${0%/*}
echo $exe_path
LICENCE_FILE=$exe_path/all_licence.txt
GEN="$exe_path/gen.php $*"

LIC="`$GEN`"
if [ ! "$LIC" ]
then
exit
fi
echo $LIC
echo "Czy zapisac do pliku licencji? [t/n]"
read tst
case $tst in
[tT])
echo "$LIC" >> $LICENCE_FILE
;;
*)
exit
;;
esac
