#!/bin/sh

cd `dirname $0`

LOCK=/tmp/sharedapps-cron.lock

if [ -f $LOCK ]
then
        exit
fi

touch $LOCK

php find-disabled.php

echo -n `date` >> ../../logs/sharedapps.log

labels=`php sharedmail.php`

for label in $labels 
do
	if [ "`expr $label + 0`" = "$label" ]
	then
		date >> ../../logs/sharedmail-$label.log
		php sharedmail.php $label 1 >> ../../logs/sharedmail-$label.log &
	fi
done

contacts=`php sharedcontacts.php`

for contact in $contacts
do
	if [ "`expr $contact + 0`" = "$contact" ]
	then
		date >> ../../logs/sharedcontact-$contact.log
		php sharedcontacts.php $contact 1 >> ../../logs/sharedcontact-$contact.log &
	fi
done

wait


echo  " ... `date`" >> ../../logs/sharedapps.log

rm -f $LOCK
