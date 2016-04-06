<?

	if (0==strlen(trim($prev))) $prev="NULL";
	else $prev+=0;
	
	if (!$kameleon->checkRight('write','page',$page))
	{
		$error=$norights;
		return;
	}


	$query="	UPDATE webpage SET prev=$prev WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID AND id=$page;
				UPDATE webpage SET tree='' WHERE (id=$page OR tree ~ ':$page:') AND ver=$ver AND lang='$lang' AND server=$SERVER_ID";

	//echo nl2br($query);return;

	
	if (!strlen($error)) if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		webver_page($page,$action);
	}
