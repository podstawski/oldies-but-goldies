
PHP=/usr/local/php4/bin/php
cd ..

for file in `find ufiles -print`
do
	if [ -d $file ]
	then
		continue
	fi
	
	ile=`$PHP tools/checkfile.php $file`

	if [ ! $ile ]
	then
		continue
	fi

	if [ $ile -gt 0 ]
	then
		continue
	fi

	echo $file
done
