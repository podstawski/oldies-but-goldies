<?
global $idb,$params,$CFG;

#mysql
$CFG['user'] = "fakro_pl";
$CFG['host'] = "localhost";
$CFG['pass'] = "zk4q7x";
$CFG['db'] = "fakro_pl";

#poczty
$params["host"] = "mail.fakro.com.pl";		// adres serwera SMTP
$params["port"] = "25";						// port serwera SMTP (zazwyczaj: 25)
$params["auth"] = true;						// czy serwer wymaga autoryzacji (zazwyczaj: true)
$params["username"] = "robotfakro";			// login konta (ewentualnie adres e-mail konta)
$params["password"] = "2wsxcde3";			// hasło konta

include_once($INCLUDE_PATH."/lib/idb_mysql.php");

$idb = new idatabase($CFG['host'],$CFG['user'],$CFG['pass'],$CFG['db']);
?>