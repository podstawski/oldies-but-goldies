<?
	$wf_id+=0;
	$al=0;
	$sql="SELECT wf_accesslevel AS al FROM webfile WHERE wf_id=$wf_id ";
	parse_str(ado_query2url($sql));

	
	if ($al > $kameleon->current_server->accesslevel)
	{
		$error=label("Insufficient rights");
		return;
	}

	if (!$wf_id) return;


	if (!strlen($wf_page)) $wf_page='NULL';
	else $wf_page+=0;


	$wf_accesslevel+=0;

	if ($wf_accesslevel > $kameleon->current_server->accesslevel)
	{
		$wf_accesslevel=$kameleon->current_server->accesslevel;
	}

	$query="UPDATE webfile SET wf_accesslevel=$wf_accesslevel, wf_page=$wf_page WHERE wf_id=$wf_id AND wf_server=$SERVER_ID";
	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		webver_td($page_id,$pri,$action);
	}
?>