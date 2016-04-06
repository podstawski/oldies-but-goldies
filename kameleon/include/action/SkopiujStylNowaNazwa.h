<?
	$action="";
	
	if (!$kameleon->checkRight('write','class') )
	{
		$error=$norights;
		return;
	}	

	$nazwa=strtolower(trim($nazwa));
	if (!strlen($nazwa)) return;

	if ($nazwa[0]=="<") $nazwa=substr($nazwa,1,strlen($nazwa)-2);
	else $nazwa=".$nazwa";

	if ($nazwa==$src) $error=label("Source and destination classes are the same.");

	
	$query="INSERT INTO class
		 (server,nazwa,pole,wart,ver)
		  SELECT $SERVER_ID,'$nazwa',pole,wart,ver
		  FROM class 
		  WHERE server=$SERVER_ID AND ver=$ver AND nazwa='$src'";
	
	//echo nl2br($query);return;

	if (!strlen($error)) if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		$exploreclass=$nazwa;
	}

?>
