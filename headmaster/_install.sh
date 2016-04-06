#!/bin/sh -e

cd  `dirname $0`
pwd=`pwd`

echo "To jest instalacja gry Headmaster"

command -v psql >/dev/null 2>&1 || { echo >&2 "Wymagana instalacja programu psql."; exit 1; }
command -v php >/dev/null 2>&1 || { echo >&2 "Wymagana instalacja programu php."; exit 1; }

echo -n "Podaj katalog instalacji [$pwd]: "
read dir
if [ ! "$dir" ]
then
	dir=$pwd
fi

if [ ! -d $dir ]
then
	echo "Katalog $dir nie istnieje"
	exit
fi

echo -n "Podaj nazwe serwera PostgreSQL [localhost]: "
read server

if [ ! "$server" ]
then
	server=localhost
fi

echo -n "Podaj numer portu serwera PostgreSQL [5432]: "
read port

if [ ! "$port" ]
then
	port=5432
fi

user=""

while [ ! "$user" ]
do
	echo -n "Podaj nazwe uzytkownika PostgreSQL: "
	read user
	if [ ! "$user" ]
	then
		echo "Bez uzytkownika nic nie mozna dalej zrobic"
	fi
done

pass=""
while [ ! "$pass" ]
do
	echo -n "Podaj haslo uzytkownika PostgreSQL: "
	read pass
	if [ ! "$pass" ]
	then
		echo "Bez hasla uzytkownika nic nie mozna dalej zrobic"
	fi
done

export PGUSER=$user
export PGPASSWORD=$pass

db=`psql -l 2>/dev/null | wc | awk '{print $1}'`

if [ $db -eq 0 ]
then
	echo "Brak mozliwosci podlaczenia do bazy danych"
	exit
fi

echo -n "Podaj nazwe bazy [headmaster]: "
read dbase

if [ ! "$dbase" ]
then
	dbase=headmaster
fi

check=`echo "select count(tablename) from pg_tables;" | psql -d $dbase -q -t 2>/dev/null | head -n 1 | awk '{print $1}'`

if [ ! $check ]
then
	echo "Baza $dbase nie istnieje lub $user nie ma do niej dostepu."
	exit
fi

echo -n "Rozpakowuje pliki ... "

cd $dir
sed -e '1,/^TUZACZYNASIEGRAHM$/d' "$pwd/`basename $0`" | tar xzf -

echo ok

echo -n "Tworze local.ini ... "

chmod 755 application application/configs
echo "
db.host = $server
db.port = $port
db.username = $user
db.password = $pass
db.dbname = $dbase
" > application/configs/local.ini

echo ok

chmod 0777 application/cache
chmod 0777 application/logs
chmod 0777 application/configs
php library/Millionaire-quiz/scripts/migrate.php
rm -rf application/logs/* application/cache/*

exit
TUZACZYNASIEGRAHM
