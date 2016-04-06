<?php
	global $HOST;
	global $kameleon;

	$whereami='';

	if (strstr(strtolower(getenv('HOST')),'webkameleon')) $whereami='gammanet';
	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro06')) $whereami.='_de';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakro.de')) $whereami.='_de';
	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro07')) $whereami.='_nl';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakro.nl')) $whereami.='_nl';
	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro08')) $whereami.='_fr';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakro.fr')) $whereami.='_fr';
	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro09')) $whereami.='_ru';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakro.ru')) $whereami.='_ru';

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
		case 'gammanet_nl':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"sql.gammanet.pl:5473");
			define('C_PROJ_CONNECT_USER',"fakro");
			define('C_PROJ_CONNECT_PASSWORD',"akd8o9");
			define('C_PROJ_CONNECT_DBNAME',"fakronl");
			define('C_UNZIP',"/usr/local/bin/unzip");
			break;	
		case 'gammanet_fr':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"sql.gammanet.pl:5473");
			define('C_PROJ_CONNECT_USER',"fakro");
			define('C_PROJ_CONNECT_PASSWORD',"akd8o9");
			define('C_PROJ_CONNECT_DBNAME',"fakrofr");
			define('C_UNZIP',"/usr/local/bin/unzip");
			break;	
		default:
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"sql.gammanet.pl:5473");
			define('C_PROJ_CONNECT_USER',"fakro");
			define('C_PROJ_CONNECT_PASSWORD',"akd8o9");
			define('C_PROJ_CONNECT_DBNAME',"fakroru");
			define('C_UNZIP',"/usr/local/bin/unzip");
			break;	

	}







	define('C_NAVI_PAGES',6);
	setlocale(LC_CTYPE,"pl_PL.ISO8859-2");
	$REPOZYTORIUM="$UFILES/.rep";
?>
