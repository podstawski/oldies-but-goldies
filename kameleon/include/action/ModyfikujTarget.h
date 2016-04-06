<?
	
	if (isset($page_id))
	{
		$pages=explode(":",$page_id);
		$pole="page_id";
	}

	if (isset($menu_id))
	{
		$pages=explode(":",$menu_id);
		$pole="menu_id";
	}
	$pages[0]+=0;
	$pages[1]+=0;

	if (!strlen(trim($pages[2]))) $pages[2]="NULL";	

	if (strlen(trim($pages[3]))) 
	{
		$_p=$pages[3];
		$pages[3]=$pages[2];
		$pages[2]=$_p;
	}

	$query="SELECT alt FROM weblink	 
			WHERE server=$SERVER_ID 
			AND ver=$ver AND lang='$lang'
			AND $pole=$pages[0]
			AND sid=$pages[1] ";
	parse_str(ado_query2url($query));

	if (!strlen($alt))
	{
		$query="SELECT title AS alt FROM webpage 
				WHERE id=$pages[2]
				AND server=$SERVER_ID 
				AND ver=$ver AND lang='$lang'";
		parse_str(ado_query2url($query));
	}

	$alt=addslashes(stripslashes($alt));


	$query="UPDATE weblink 
			SET page_target=$pages[2], lang_target='$pages[3]', alt='$alt' 
			WHERE server=$SERVER_ID 
			AND ver=$ver AND lang='$lang'
			AND $pole=$pages[0]
			AND sid=$pages[1] ";
		
	
	//echo nl2br($query);return;
	if (!strlen($error)) 
	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
	}

	//$page=$pages[0];
	$menu=$pages[0];
	
	webver_link($menu,$action);


