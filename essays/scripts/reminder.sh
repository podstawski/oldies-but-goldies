#!/bin/sh

cd `dirname $0`

log=../../logs/essays_reminder.log


date >> $log
php contacts.php reminder >>$log
