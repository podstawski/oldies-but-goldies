<?
	$action="";

	if (!$kameleon->checkRight('write','class'))
	{
		$error=$norights;
		return;
	}

	if (!$srcver) return;
	
	$query="INSERT INTO class
		 (server,nazwa,pole,wart,ver)
		  SELECT $SERVER_ID,nazwa,pole,wart,$ver
		  FROM class WHERE server=$SERVER_ID AND ver=$srcver";
	
	//echo nl2br($query);return;

	if ($adodb->Execute($query)) logquery($query) ;

