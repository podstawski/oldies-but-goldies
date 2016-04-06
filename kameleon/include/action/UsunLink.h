<?

	if (strlen($page_id))
	{
		$pages=explode(":",$page_id);
		$pageand="page_id=$pages[0]";
		$page=$pages[0];

	}
	if (strlen($menu_id))
	{
		$pages=explode(":",$menu_id);		
		$pageand="menu_id=$pages[0]";
		$menu=$pages[0];
	}

	if ($pages[1]) $ANDPRI="AND pri=$pages[1]";
	
	$query="DELETE FROM weblink  
		 WHERE ver=$ver AND lang='$lang'
		 AND server=$SERVER_ID
		 AND $pageand
		 $ANDPRI ";
	
	//echo nl2br($query);return;

	if ($adodb->Execute($query)) logquery($query) ;
	if (strlen($menu_id)) webver_link($pages[0],$action);


?>
