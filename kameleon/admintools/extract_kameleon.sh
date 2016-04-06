#!/bin/sh

export PATH=$PATH:/usr/local/pgsql/bin:/www/tools

kameleon_path="/www/kameleon1e.gammanet.pl"
TMP=/tmp/jhsdkf78asdhasjdbjhasd7f87asdtf8tasdf
label_file="$kameleon_path/changes/label.txt"
whereis_kameleon="-h sql.gammanet.pl -p 5473 "
#whereis_kameleon="-h bezatu -p 5432"
testdb=kameleon
PSQL=/www/tools/psql


echo "<? include(\"$kameleon_path/const.h\"); echo \$C_DB_CONNECT_USER;?> "|php -q | awk '{print $1}' >$TMP
export PGUSER=`cat $TMP`
echo "<? include(\"$kameleon_path/const.h\"); echo \$C_DB_CONNECT_PASSWORD;?> "|php -q | awk '{print $1}' >$TMP
export  PGPASSWORD=`cat $TMP`

if [ "0" = "1" ]
then
	echo -n "Dump bazy label ... "
	echo "COPY label TO stdout DELIMITERS ';' ;" \
		| $PSQL -q -t -d $testdb -U kameleon $whereis_kameleon >$label_file
	echo "ok"
fi



echo "<? include(\"$kameleon_path/include/const.h\"); echo \$KAMELEON_VERSION;?> "|php -q | awk '{
print $1}' >$TMP
ver=`cat $TMP`
rm -f $TMP


schema_file="$kameleon_path/changes/postgres-schema-$ver.sql"

echo -n "Dump bazy ..."
/www/tools/pg_dump $whereis_kameleon -i -s -x $testdb	\
	| grep -v " OWNER TO " \
	| grep -v "\-\- " \
	| grep -v "REVOKE " \
	| grep -v "GRANT " \
	| grep -v "COMMENT ON" \
	| /www/kameleon.gammanet.pl/admintools/remove_tsearch.php \
	> $schema_file
echo " ok"



uimages="uimages/0"
ufiles="ufiles/0"
szablony="szablony/0"

if [ "$1" ]
then
	server=$1
	szablon=`su - pgsql -c "/usr/local/pgsql/kameleon/dump_server.sh $server"`
	for i in `echo $server|awk  'BEGIN { RS=","} {printf $1 " "}'` 
	do
		uimages="$uimages uimages/$i"
		ufiles="$ufiles ufiles/$i-att"
	done


	for i in $szablon
	do
		szablony="$szablony szablony/$i"
	done
	ver="${ver}-plus_serv_${server}"
fi



cd $kameleon_path
#cp ../kameleon.gammanet.pl/kameleon.css .

if [ -f /tmp/kameleon_$server.sql ]
then
	mv /tmp/kameleon_$server.sql .
	sql="kameleon_$server.sql"
fi
if [ -f /tmp/kameleonapi_$server.sql ]
then
	mv /tmp/kameleonapi_$server.sql .
	sqlapi="kameleonapi_$server.sql"
fi

rm -f log/*

/www/tools/md5.sh

tar --exclude .passwd --exclude .htaccess --exclude .svn --dereference -czpf \
	/www/out/kameleon-enc_$ver.tgz \
	*.php admin include tools license adodb/* \
	img/* remote/* incuser/* jupload/* \
	szablony.def \
	$szablony $uimages $ufiles $sql $sqlapi uincludes/0\
	const.h.sample setup/* \
	jsencode/* changes/* log \
	FCKeditor ImageManager \
	api/api* api/include api/img/* api/images/* \
	loaders/* win/* \

rm -f /www/out/setup.tgz
ln /www/out/kameleon-enc_$ver.tgz /www/out/setup.tgz

#tar --exclude .passwd --exclude .htaccess --dereference -czpf \
#	/www/out/kameleon-enc_winphp_$ver.tgz \
#	*.php admin/*.php

for i in modules/@*
do
	module_name=`basename $i |awk '{print substr($1,2)}'`
	tar --exclude .svn --dereference -czpf \
		/www/out/modules/kameleon-module-enc_${ver}_$module_name.tgz \
		$i

	rm -f /www/out/modules/kameleon-module-enc_$module_name.tgz
	ln /www/out/modules/kameleon-module-enc_${ver}_$module_name.tgz \
		 /www/out/modules/kameleon-module-enc_$module_name.tgz
done

cp $label_file  /www/label.txt
rm -f kameleon_$server.sql 

cd /www/out/setup/bin
tar -xzf /www/out/kameleon-enc_$ver.tgz
cd ..
ioncube5 ../setup.src/install.php -o php/install.php

#rm -r ../setup.zip
#echo "v. $ver" > ver
#zip -Rq ../setup.zip "*"
new=0
echo -n "Przegrac na cms'a [y/n] "
read y
if [ "$y" = "y" ]
then
	echo "Prosze czekac ..."
	scp ../modules/kameleon-module-enc_api.tgz webadmin@cms:/www/kameleon/out/.api.tgz.new
	scp ../setup.tgz webadmin@cms:/www/kameleon/out/.setup.new
	ssh webadmin@cms "mv /www/kameleon/out/.setup.new /www/kameleon/out/setup.tgz"
	ssh webadmin@cms "mv /www/kameleon/out/.api.tgz.new /www/kameleon/out/.api.tgz"
	new=1
fi

echo -n "Przegrac na cms'a do katalogu .last [y/n] "
read y
if [ "$y" = "y" ]
then
	echo "Prosze czekac ..."
	if [ "$new" = "1" ]
	then
		ssh webadmin@cms "cp /www/kameleon/out/setup.tgz /www/kameleon/out/.last"
		ssh webadmin@cms "cp /www/kameleon/out/.api.tgz /www/kameleon/out/.last/api.tgz"
	else
		scp  ../modules/kameleon-module-enc_api.tgz  webadmin@cms:/www/kameleon/out/.last/api.tgz
		scp  ../setup.tgz  webadmin@cms:/www/kameleon/out/.last
	fi
fi

#cp ../setup.tgz /p/kameleon
#cp ../modules/kameleon-module-enc_api.tgz /p/kameleon/api.tgz
