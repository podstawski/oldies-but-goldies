

KAMELEON_DIR=/www/kameleon
PHP_PATH=/usr/local/bin


DIR=`dirname $0`

if [ "x$DIR" = "x." ]
then
	DIR=`pwd`
fi
DIR=`dirname $DIR`

PHP=`whereis -b php 2>/dev/null | awk '{print $2}'`

while [ ! -x $PHP ]
do
	echo -n "Path th php: "
	read php
	PHP="$php/php"
done

PHP=`dirname $PHP`

TMP=/tmp/.kameleon_cron.tmp
crontab -l | grep -v index_portal > $TMP
echo "10 0 * * * $DIR/tools/index_portal.sh KAMELEON_DIR=$DIR PHP_PATH=$PHP" >> $TMP
crontab $TMP
rm -f $TMP
