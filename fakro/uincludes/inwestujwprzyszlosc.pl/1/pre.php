<?
if(!isset($KAMELEON_MODE)) {
	session_start();
}
	
global $params;

#ustawienia do poczty
$params = array();
$params["host"] = "mail.fakro.com.pl";  // adres serwera SMTP
$params["port"] = "25";                 // port serwera SMTP (zazwyczaj: 25)
$params["auth"] = true;                 // czy serwer wymaga autoryzacji (zazwyczaj: true)
$params["username"] = "robotfakro";     // login konta (ewentualnie adres e-mail konta)
$params["password"] = "2wsxcde3";       // haslo konta
?>
