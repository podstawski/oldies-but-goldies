<?
	include_once('include/webver.h');

	$sid=0;
	$sql="SELECT sid FROM webpage WHERE id=$page AND server=$SERVER_ID AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($sql));
	
	if ($sid) 
	{
		$error=webver_ftpPageResore($sid,$action);
	}


?>