pwd=`pwd`

cd `dirname $0`
prg_dir=`pwd`

cd $pwd

PRG=$prg_dir/`basename $0`


for file in `find . -type f -print | sed 's/ /STRASZNASPACJA/g'`
do
	plik=`echo $file | sed 's/STRASZNASPACJA/ /g'`
	if [ "`basename $PRG`" = "`basename $plik`" ]
	then
		continue
	fi

	domena=`echo $plik | awk -F/ '{print $2}'`
	kraj=`echo $domena | awk -F. '{print $NF}'`
	liter=`echo $kraj | awk '{print length($1)}'`

	CHR=""
	if [ "$kraj" = "com" ]
	then
		CHR="ISO-8859-1"
	fi
	if [ "$liter" = "2" ]
	then
		case $kraj in
			"pl") CHR="ISO-8859-2";;
			"hu") CHR="ISO-8859-2";;
			"cz") CHR="ISO-8859-2";;
			"ro") CHR="ISO-8859-2";;
			"ru") CHR="ISO-8859-5";;
			"bg") CHR="ISO-8859-5";;
			"by") CHR="ISO-8859-5";;
			"bu") CHR="ISO-8859-5";;
			"de") CHR="ISO-8859-1";;
			*) CHR="ISO-8859-1";;
		esac
	fi

	if [ ! "$CHR" ]
	then
		CHR="ISO-8859-2";
	fi
	
	iconv -f $CHR -t UTF-8 < "$plik" > /tmp/tmp.plik

	mv /tmp/tmp.plik "$plik"
done
