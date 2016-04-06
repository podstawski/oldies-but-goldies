<?
	$action="";

	
	$server+=0;
	$query="DELETE FROM rights WHERE username='$login' AND server=$server";
	
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;


?>