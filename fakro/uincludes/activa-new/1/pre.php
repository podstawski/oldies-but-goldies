<?
if($KAMELEON_MODE==0) {
	session_start();
	}
	
	if (!isset($WEBTD->sid) OR !$WEBTD->sid)
	{
		foreach ( array_keys($_REQUEST) AS $k ) eval("\$$k=\$_REQUEST[\"$k\"];");
		foreach ( array_keys($_SERVER) AS $k ) eval("\$$k=\$_SERVER[\"$k\"];");
	}


	include("$INCLUDE_PATH/fun.php");
	define('ADODB_FETCH_CASE', 2);
	define ('ADODB_DIR',"$INCLUDE_PATH/adodb/");
	$ADODB_FETCH_MODE = (defined('ADODB_FETCH_ASSOC') ? ADODB_FETCH_ASSOC : null);
	include_once(ADODB_DIR."adodb.inc.php");
	include_once(ADODB_DIR."kameleon-ado-fun.h");
	include_once($INCLUDE_PATH."/autoryzacja/sysfun.h");


	$whereami='';

	if (strstr(strtolower(getenv('HOST')),'webkameleon')) $whereami='gammanet';
	if (isset($kameleon) AND strstr($kameleon->current_server->nazwa,'test')) $whereami.='_test';

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
			$CONST_SENDMAIL_PATH="/var/www/html/fakro/kameleon01/sendmail";
			break;
	}

	if (!isset($fakrodb) OR (is_object($fakrodb) && strlen(C_DB_CONNECT_DBTYPE)))
	{
		#$fakrodb = &ADONewConnection(C_DB_CONNECT_DBTYPE);
		#$fakrodb->Connect(C_DB_CONNECT_HOST, C_DB_CONNECT_USER, C_DB_CONNECT_PASSWORD, C_DB_CONNECT_DBNAME);
		#$db = $fakrodb->_connectionID; 
	}

global $API_KEY_GOOGLE_MAPS;

if($KAMELEON_MODE==0) {
	// key: www.active-life.eu
	$API_KEY_GOOGLE_MAPS = "ABQIAAAA7OSC64oLNVnWRIabrKnBZRRwDWFmHqjZlJGSArsKxahq9VE7bRRZeq4vtidYH6C-wlvEfCIqbtHOjw";
	$API_KEY_GOOGLE_MAPS = "ABQIAAAAcWWjEhot6Zv9AvPGkdKdAxRjJPhINuEFgpa93YmialgNpCjnlRQn74ci3oj1qb4OEPhwn13mPcwLdw";
	}else{
	
	if($_SERVER['HTTP_HOST'] == 'kameleon') {
		// key: kameleon
		$API_KEY_GOOGLE_MAPS = "ABQIAAAA7OSC64oLNVnWRIabrKnBZRTc7ThDh8dymwEzDob3sDF0ZklZGxStjBQLGe20_NKXJs0IzHePwl75Mw";
		}else{
		// key: kameleon01.fakro.pl
		$API_KEY_GOOGLE_MAPS = "ABQIAAAA7OSC64oLNVnWRIabrKnBZRTvvno4W6z5umspeDGprMgiZVlFJhTXkpQa65Y8I0kUPGD1UaqSBhTyBQ";
		}
	}

$SKLEP_INCLUDE_PATH=$INCLUDE_PATH;
$next_char = $KAMELEON_MODE?"&":"?";

global $params;

#ustawienia do poczty
$params["host"] = "mail.fakro.com.pl";  // adres serwera SMTP
$params["port"] = "25";                 // port serwera SMTP (zazwyczaj: 25)
$params["auth"] = true;                 // czy serwer wymaga autoryzacji (zazwyczaj: true)
$params["username"] = "robotfakro";     // login konta (ewentualnie adres e-mail konta)
$params["password"] = "2wsxcde3";       // haslo konta

?>
