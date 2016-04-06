<?
	session_start();
	$AUTH = array();
	$AUTH[id] = -1;
	setcookie( session_name() ,"",0,"/");
	session_destroy();
	if (is_object($WM)) $WM->session = "";
	unset($SKLEP_SESSION);
?>
