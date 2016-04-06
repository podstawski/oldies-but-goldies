#!/bin/sh

cd `dirname $0`

log=../../logs/sharedapps_reminder.log


date >> $log
php contacts.php reminder >>$log
