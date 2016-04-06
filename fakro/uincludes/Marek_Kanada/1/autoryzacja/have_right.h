<?
	global $_AUTH_RESULT,$AUTH;

	$_AUTH_RESULT = haveRight($pid,$AUTH[id],'',$ofekdb) ? " " : $err;
?>