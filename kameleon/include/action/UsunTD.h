<?
	if (strlen($page_id))
	{
		$pages=explode(":",$page_id);
		$page_id=$pages[0];
		$sid=$pages[1];
	}

	if (!$kameleon->checkRight('delete','box',$sid) || !$kameleon->checkRight('write','page',$page_id))
	{
		$error=$norights;
		return;
	}


	$al=0;
	$sql="SELECT accesslevel FROM webtd  
			WHERE ver=$ver AND lang='$lang'
			AND server=$SERVER_ID AND page_id=$page_id
			AND sid=".$sid;
	parse_str(ado_query2url($sql));
	$al+=0;

	
	if ($al > $kameleon->current_server->accesslevel)
	{
		$error=label("Insufficient rights");
		return;
	}



	$update_and=($page_id>=0)?"AND id=$page_id":"";
	
	$query="SELECT html FROM webtd 
		 WHERE ver=$ver AND lang='$lang'
		 AND server=$SERVER_ID AND page_id=$page_id
		 AND sid=".$sid.";";
	parse_str(ado_query2url($query));
	if ($html[0]=="@")
	{
		$bn=basename($html);
		$dn=dirname($html);
		$path="modules/$dn/.delete/$bn";
		if (file_exists("$path") ) include ("$path");
		if (file_exists("modules/$dn/action.h") )
		{
			push($action);
			push($INCLUDE_PATH);
			$action=ereg_replace("\.h","_","${bn}delete");
			$INCLUDE_PATH="modules/$dn";
			include ("modules/$dn/action.h");
			$INCLUDE_PATH=pop();
			$action=pop();
		}
	}

	$sql="SELECT title AS _t FROM webtd WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID AND page_id=$page_id AND sid=".$sid.";";
	parse_str(ado_query2url($sql));


	if ($MAY_PROOF)
	{
		$query="DELETE FROM webtd  
			 WHERE ver=$ver AND lang='$lang'
			 AND server=$SERVER_ID AND page_id=$page_id
			 AND sid=".$sid.";
			UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
					 WHERE server=$SERVER_ID AND ver=$ver AND 
				lang='$lang' $update_and; ";
	}
	else
	{
		$query="UPDATE webtd SET hidden=100
			 WHERE ver=$ver AND lang='$lang'
			 AND server=$SERVER_ID AND page_id=$page_id
			 AND sid=".$sid.";
			UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
					 WHERE server=$SERVER_ID AND ver=$ver AND 
				lang='$lang' $update_and; ";
	}
		
	//echo nl2br($query);return;

	if (strlen($error)) return;
	if ($adodb->Execute($query)) 
	{
		webver_page($page_id,$action,0,false,$_t);
		logquery($query) ;
	}



