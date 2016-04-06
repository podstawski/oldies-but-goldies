<?
	$action="";

	$id+=0;	
	$query="DELETE FROM label WHERE id=$id";
	
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;

?>