#echo "do bazy - mxyzptlk"
echo "Ks9#m1z"


ssh wwwgammanet@isp6.i24.pl "cd /var/www/html/site/kameleon/web/tools; sh UPG.sh"
scp fakro_update.sh ../tools/iso2utf.php wwwgammanet@isp6.i24.pl:/var/www/html/site/kameleon/web/tools
scp fakro_kameleon_sendmail.php wwwgammanet@isp6.i24.pl:/var/www/html/site/kameleon/web/remote/kameleon_sendmail.php



echo -n "zalogowac sie tam ? "

read co

if [ "$co" = "t" ]
then
	echo cd /var/www/html/site/kameleon/web
	ssh wwwgammanet@isp6.i24.pl 
fi

