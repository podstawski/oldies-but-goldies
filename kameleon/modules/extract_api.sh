common=".api/action.h .api/winiso.h adodb kameleon .pre.h action.inc action_exists.php api.inc api.js api.php fun.inc pre.inc wait.gif"
ankiety=".api/ankieta.h .api/action/AnkietaGlos.h .ankieta.h .update/ankieta.h ankieta.*"

all="$ankiety"

plik=$1
if [ ! "$plik" ]
then
	plik="all"
fi
eval "co=\$$plik"
plik="api-$plik.tgz"

pw=`dirname $0`	
if [ "$pw" = "." ]
then
	pw=`pwd`
fi

cd /www/kameleon1e.gammanet.pl/modules/@api


tar --dereference -czf /www/out/modules/kameleon-module-enc_$plik $common $co

cd $pw
mkdir -p  tmp/modules/@api
php extract_api.php /www/kameleon1e.gammanet.pl/modules/@api/.api.xml $1 >tmp/modules/@api/.api.xml
cd tmp/modules/@api
tar -xzf /www/out/modules/kameleon-module-enc_$plik
cd $pw
cd tmp
tar -czf /www/out/modules/kameleon-module-enc_$plik modules
cd ..
rm -r tmp
cp /www/out/modules/kameleon-module-enc_$plik .
