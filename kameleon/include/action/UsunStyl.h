<?
	$action="";
	
	if (!$kameleon->checkRight('delete','class') )
	{
		$error=$norights;
		return;
	}	
	
	$nazwa=strtolower(trim($nazwa));
	if (!strlen($nazwa)) return;


	$n=explode("::",$nazwa);
	
	if (strlen($n[1])) 
	{
		$additional="AND pole='$n[1]'";
		$exploreclass=$n[0];
	}

	
	
	$query="DELETE FROM class WHERE server=$SERVER_ID
		 AND nazwa='$n[0]' $additional AND ver=$ver";
	
	//echo nl2br($query);return;

	if ($adodb->Execute($query)) logquery($query) ;

?>
