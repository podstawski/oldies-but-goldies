#!/bin/sh


file=/tmp/lang-en.php

wget "https://docs.google.com/a/gammanet.pl/document/d/1Y-TYn8gbcfswsIND_5KLHunhef4-ZsmX6jdFYsyb-vU/export?format=txt&id=1Y-TYn8gbcfswsIND_5KLHunhef4-ZsmX6jdFYsyb-vU&token=AC4w5VhJIAZpq4FSBq1isKqs7dEK1ZJLQg%3A1350367483000" -O - 2>/dev/null | sed "s/’/'/" | sed "s/‘/'/" > $file

chmod 666 $file


count=`php -r "echo count(include('$file'));" | sed 's/[^0-9]*//g'`
if [ ! $count ]
then
	count=0
fi
if [ $count -gt 100 ]
then
	mv $file `dirname $0`/../application/language/en/lang.php
	echo "Plik en/lang.php pomyślnie wgrany"
else
	echo "Pussywhipped wykochał plik z tłumaczeniem"
fi


