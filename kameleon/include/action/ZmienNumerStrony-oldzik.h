<?
	$action="";
	
	
	
	
	if (isset($page_id))
	{
		$pages=explode(":",$page_id);
	}

	$query="SELECT title,id FROM webpage
		 WHERE id=$pages[1] AND ver=$ver 
		 AND server=$SERVER_ID
		LIMIT 1";

	parse_str(ado_query2url($query));
	

	if (!$kameleon->checkRight('write','page',$pages[0]))
	{
		$error=$norights;
		return;
	}
	
	

	$l_page=label("Page");
	$l_exists=label("exists");

	if (strlen($id)) 
		$error="$l_page $pages[1] $l_exists ($title) !";

	$query="UPDATE webpage SET id=$pages[1]
		WHERE ver=$ver AND id=$pages[0] AND server=$SERVER_ID AND lang='$lang';

		UPDATE weblink SET page_id=$pages[1]
		WHERE ver=$ver AND page_id=$pages[0] AND server=$SERVER_ID AND lang='$lang';

		UPDATE weblink SET page_target=$pages[1]
		WHERE ver=$ver AND page_target=$pages[0] AND server=$SERVER_ID AND lang='$lang';

		UPDATE webtd SET page_id=$pages[1]
		WHERE ver=$ver AND page_id=$pages[0] AND server=$SERVER_ID AND lang='$lang';";

	
	//echo nl2br($query);return;
	$page=$pages[1];

	if (!strlen($error)) 
		if ($adodb->Execute($query)) 
			logquery($query) ;

	

