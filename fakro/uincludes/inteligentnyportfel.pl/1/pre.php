<?
	if (!$WEBTD->sid)
	{
		foreach ( array_keys($_REQUEST) AS $k ) eval("\$$k=\$_REQUEST[\"$k\"];");
		foreach ( array_keys($_SERVER) AS $k ) eval("\$$k=\$_SERVER[\"$k\"];");
	}


	include("$INCLUDE_PATH/fun.php");
	define('ADODB_FETCH_CASE', 2);
	define ('ADODB_DIR',"$INCLUDE_PATH/adodb/");
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
	include_once(ADODB_DIR."adodb.inc.php");
	include_once(ADODB_DIR."kameleon-ado-fun.h");
	include_once($INCLUDE_PATH."/autoryzacja/sysfun.h");


	$whereami='';

	if (strstr(strtolower(getenv('HOST')),'webkameleon')) $whereami='gammanet';
	if (strstr($kameleon->current_server->nazwa,'test')) $whereami.='_test';

	switch ($whereami)
	{ 
		case 'gammanet':
		case 'gammanet_test':
			define('C_DB_CONNECT_DBTYPE',"postgres7");
			define('C_DB_CONNECT_PERSISTANT',0);
			define('C_DB_CONNECT_HOST',"sql.gammanet.pl:5473");
			define('C_DB_CONNECT_USER',"fakro");
			define('C_DB_CONNECT_PASSWORD',"akd8o9");
			define('C_DB_CONNECT_DBNAME',"fakropldb");
			break;

		default:
			define('C_DB_CONNECT_DBTYPE',"postgres7");
			define('C_DB_CONNECT_PERSISTANT',0);
			define('C_DB_CONNECT_HOST',"localhost:5432");
			define('C_DB_CONNECT_USER',"pgfakrosite");
			define('C_DB_CONNECT_PASSWORD',"k1q8vj2");
			define('C_DB_CONNECT_DBNAME',"fakro_site");
			$CONST_SENDMAIL_PATH="/var/www/html/fakro/kameleon01/sendmail_inteligentnyportfel";
			break;
	}

	if (!is_object($fakrodb) && strlen(C_DB_CONNECT_DBTYPE) )
	{
		$fakrodb = &ADONewConnection(C_DB_CONNECT_DBTYPE);
		$fakrodb->Connect(C_DB_CONNECT_HOST, C_DB_CONNECT_USER, C_DB_CONNECT_PASSWORD, C_DB_CONNECT_DBNAME);
		$db = $fakrodb->_connectionID; 

	}

	$SKLEP_INCLUDE_PATH=$INCLUDE_PATH;
	$next_char = $KAMELEON_MODE?"&":"?";
?>
