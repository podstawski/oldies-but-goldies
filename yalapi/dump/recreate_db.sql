
db=$1
if [ ! $db ]
then
	db=yalapi
fi

dropdb $db
createdb -E utf-8 $db
psql -d $db -f yala.sql

php ../backend/scripts/recreate_acl.php
