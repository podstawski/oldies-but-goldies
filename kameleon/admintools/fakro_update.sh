cd `dirname $0`

export PGPASSWORD=mxyzptlk


for db in fakro_kameleon01 fakro_intranet fakro_site fakro_sklep_co_uk  fakro_sklep_de fakro_sklep_fr fakro_sklep_fr_tmp fakro_sklep_nl fakro_sklep_pl fakro_sklep_ru fakro_sklep_usacom
do
	#pg_dump -U postgres $db | sed  's/SQL_ASCII/LATIN2/g' >/tmp/$db.sql
	/opt/PostgreSQL/8.4/bin/dropdb -U postgres -p 5433 $db
	/opt/PostgreSQL/8.4/bin/createdb -E UTF8 -p 5433 $db -U postgres
	pg_dump -U postgres $db |sed  's/SQL_ASCII/LATIN2/g' | /opt/PostgreSQL/8.4/bin/psql -U postgres -d $db -p 5433
done




echo "--UPDATE pg_ts_cfg SET locale = 'pl_PL.UTF-8' WHERE ts_name = 'default';" |  psql -U postgres -d fakro_kameleon01


cd ../tools
php iso2utf.php fakro_kameleon01@:5433 ~fts

echo "
	update webpage set lang='pl' where lang='i';
	update weblink set lang='pl' where lang='i';
	update webtd set lang='pl' where lang='i';
	update webtd set lang='en' where lang='e';
	update weblink set lang='en' where lang='e';
	update webpage set lang='en' where lang='e';
	update servers set lang='pl' where lang='i';
	update servers set lang='en' where lang='e';

	update fts set fts_lang='pl' where fts_lang='i';
	update fts set fts_lang='en' where fts_lang='e';

	UPDATE servers SET ftp_dir='utf' WHERE ftp_dir='' OR ftp_dir IS NULL;
	
	" |  psql -U postgres -d fakro_kameleon01 -p 5433


