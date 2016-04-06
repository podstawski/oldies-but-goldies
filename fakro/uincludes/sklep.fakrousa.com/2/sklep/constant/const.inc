<?php
	global $HOST;
	global $kameleon;

	$whereami='';

	if (strstr(strtolower(getenv('HOST')),'webkameleon')) $whereami='gammanet';
	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro06')) $whereami.='_de';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakro.de')) $whereami.='_de';

	define ('C_PROJ_NAZWA_SYSMSG',true);

	switch ($whereami)
	{ 
		case 'gammanet':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"sql.gammanet.pl:5473");
			define('C_PROJ_CONNECT_USER',"fakro");
			define('C_PROJ_CONNECT_PASSWORD',"akd8o9");
			define('C_PROJ_CONNECT_DBNAME',"fakrodb");
			define('C_UNZIP',"/usr/local/bin/unzip");
			break;
		case 'gammanet_de':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"sql.gammanet.pl:5473");
			define('C_PROJ_CONNECT_USER',"fakro");
			define('C_PROJ_CONNECT_PASSWORD',"akd8o9");
			define('C_PROJ_CONNECT_DBNAME',"fakrode");
			define('C_UNZIP',"/usr/local/bin/unzip");
			break;	
		default:
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"localhost:5432");
			define('C_PROJ_CONNECT_USER',"pgfakrosite");
			define('C_PROJ_CONNECT_PASSWORD',"k1q8vj2");
			define('C_PROJ_CONNECT_DBNAME',"fakro_sklep_pl");
			define('C_UNZIP',"/usr/local/bin/unzip");
			$C_SENDMAIL_PATH='/var/www/html/fakro/kameleon01/sendmail_sklep';
			break;			
	}




	define('C_NAVI_PAGES',6);
	setlocale(LC_CTYPE,"pl_PL.ISO8859-2");
	$REPOZYTORIUM="$UFILES/.rep";
?>