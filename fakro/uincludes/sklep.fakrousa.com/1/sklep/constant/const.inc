<?php
	global $HOST;
	global $kameleon;

	$whereami='';

	if (strstr(strtolower(getenv('HOST')),'webkameleon')) $whereami='gammanet';
	else $whereami='sklep';

	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro06') || strstr(strtolower($_SERVER[HTTP_HOST]),'.de') ) $whereami.='_de';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakro.de')) $whereami.='_de';
	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro07') || strstr(strtolower($_SERVER[HTTP_HOST]),'.nl') ) $whereami.='_nl';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakro.nl')) $whereami.='_nl';
	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro08') || strstr(strtolower($_SERVER[HTTP_HOST]),'.fr') ) $whereami.='_fr';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakro.fr')) $whereami.='_fr';
	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro09') || strstr(strtolower($_SERVER[HTTP_HOST]),'.ru') ) $whereami.='_ru';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakro.ru')) $whereami.='_ru';
	if (strstr(strtolower($_SERVER[HTTP_HOST]),'fakro10') || strstr(strtolower($_SERVER[HTTP_HOST]),'.com') ) $whereami.='_usacom';
	if (strstr($kameleon->current_server->nazwa,'sklep.fakrousa.com')) $whereami.='_usacom';
    
	define ('C_PROJ_NAZWA_SYSMSG',true);

	#echo strtolower($_SERVER[HTTP_HOST]);
	#echo getenv('HOST');
	#echo "ALA:$whereami";
	

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
		case 'gammanet_ru':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"sql.gammanet.pl:5473");
			define('C_PROJ_CONNECT_USER',"fakro");
			define('C_PROJ_CONNECT_PASSWORD',"akd8o9");
			define('C_PROJ_CONNECT_DBNAME',"fakroru");
			define('C_UNZIP',"/usr/local/bin/unzip");
			break;	

		case 'sklep_ru':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"localhost:5432");
			define('C_PROJ_CONNECT_USER',"pgfakrosite");
			define('C_PROJ_CONNECT_PASSWORD',"k1q8vj2");
			define('C_PROJ_CONNECT_DBNAME',"fakro_sklep_ru");
			define('C_UNZIP',"/usr/local/bin/unzip");
			$C_SENDMAIL_PATH='/var/www/html/fakro/kameleon01/sendmail';
			break;	

		case 'sklep_nl':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"localhost:5432");
			define('C_PROJ_CONNECT_USER',"pgfakrosite");
			define('C_PROJ_CONNECT_PASSWORD',"k1q8vj2");
			define('C_PROJ_CONNECT_DBNAME',"fakro_sklep_nl");
			define('C_UNZIP',"/usr/local/bin/unzip");
			$C_SENDMAIL_PATH='/var/www/html/fakro/kameleon01/sendmail';
			break;	

		case 'sklep_fr':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"localhost:5432");
			define('C_PROJ_CONNECT_USER',"pgfakrosite");
			define('C_PROJ_CONNECT_PASSWORD',"k1q8vj2");
			define('C_PROJ_CONNECT_DBNAME',"fakro_sklep_fr");
			define('C_UNZIP',"/usr/local/bin/unzip");
			$C_SENDMAIL_PATH='/var/www/html/fakro/kameleon01/sendmail';
			break;	


		case 'sklep_de':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"localhost:5432");
			define('C_PROJ_CONNECT_USER',"pgfakrosite");
			define('C_PROJ_CONNECT_PASSWORD',"k1q8vj2");
			define('C_PROJ_CONNECT_DBNAME',"fakro_sklep_de");
			define('C_UNZIP',"/usr/local/bin/unzip");
			$C_SENDMAIL_PATH='/var/www/html/fakro/kameleon01/sendmail';
			break;	
		
		case 'sklep_usacom':
			define('C_PROJ_CONNECT_DBTYPE',"postgres7");
			define('C_PROJ_CONNECT_PERSISTANT',0);
			define('C_PROJ_CONNECT_HOST',"localhost:5432");
			define('C_PROJ_CONNECT_USER',"pgfakrosite");
			define('C_PROJ_CONNECT_PASSWORD',"k1q8vj2");
			define('C_PROJ_CONNECT_DBNAME',"fakro_sklep_usacom");
			define('C_UNZIP',"/usr/local/bin/unzip");
			$C_SENDMAIL_PATH='/var/www/html/site/kameleon/web/sendmail_kameleon';
			break;	
		
		default:
			echo "BLAD: const.inc";
			break;
			
	}

	#echo $C_SENDMAIL_PATH;
	#echo C_PROJ_CONNECT_DBNAME;
	
	switch ($lang)
	{
		case 'i':
			$lc_type="pl_PL.ISO8859-2";
			break;

		case 'ru':
			$lc_type="ru_RU.ISO8859-5";
			break;	
			
		case 'de':
			$lc_type="de_DE.ISO8859-1";
			break;	

		case 'fr':
			$lc_type="fr_FR.ISO8859-1";
			break;	

		case 'nl':
			$lc_type="nl_NL.ISO8859-1";
			break;	
		
		case 'e':
			$lc_type="en_GB.ISO8859-1";
			break;
			
		default:
			$lc_type="en_GB.ISO8859-1";
			break;
	}

	define('C_NAVI_PAGES',6);
	setlocale(LC_CTYPE,$lc_type);
	$REPOZYTORIUM="$UFILES/.rep";
?>