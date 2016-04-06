<?
	$action="";

	if (!$server) return;

	$query="UPDATE servers SET
			groupid=$CONST_TRASH
		  WHERE id=$server";
		
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) 
	{
		logquery($query) ;

	}
?>