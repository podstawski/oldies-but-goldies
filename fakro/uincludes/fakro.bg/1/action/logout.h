<?
	session_start();
	session_unset();
	unset($_SESSION);
	$AUTH = array();
	setcookie( session_name() ,"",0,"/");
	session_destroy();

?>