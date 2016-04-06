<?php
	global $HOST;
	global $kameleon;

	define ('C_PROJ_NAZWA_SYSMSG',true);

	define('C_PROJ_CONNECT_DBTYPE',"postgres7");
	define('C_PROJ_CONNECT_PERSISTANT',0);
	define('C_PROJ_CONNECT_HOST',"localhost:5432");
	define('C_PROJ_CONNECT_USER',"pgfakrosite");
	define('C_PROJ_CONNECT_PASSWORD',"k1q8vj2");
	define('C_PROJ_CONNECT_DBNAME',"fakro_sklep_ru");
	define('C_UNZIP',"/usr/local/bin/unzip");
	$C_SENDMAIL_PATH='/var/www/html/fakro/kameleon01/sendmail_sklep_fakro_ru';

	$lc_type="ru_RU.ISO8859-5";

	define('C_NAVI_PAGES',6);
	setlocale(LC_CTYPE,$lc_type);
	$REPOZYTORIUM="$UFILES/.rep";
?>