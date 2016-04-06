<?
	$action="";

	if (!isset($SetGroup)) return;
	$group=$SetGroup;

	$query="UPDATE groups SET
			groupname='$newname'
		  WHERE id='$grupa'";
		
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;

	$SetGroup="";
	unset($SetGroup);
?>
