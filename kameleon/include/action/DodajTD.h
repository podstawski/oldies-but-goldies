<?
	if (!strlen($page_id)) $page_id=$page;
	$page_id+=0;

	if (!$kameleon->checkRight('insert','box') || !$kameleon->checkRight('write','page',$page_id))
	{
		$error=$norights;
		return;
	}


	$query="SELECT max(pri) AS maxpri FROM webtd
		WHERE server=$SERVER_ID AND page_id=$page_id
		AND lang='$lang' AND ver=$ver";

	parse_str(ado_query2url($query));

	$maxpri+=1;
	$pri=$maxpri;

	$new_module=label("New module",$lang);

	if ($pri==1 && page_id>=0)
	{
		$query="SELECT title AS new_module FROM webpage 
				WHERE server=$SERVER_ID AND ver=$ver 
				AND lang='$lang' AND id=$page_id";
		parse_str(ado_query2url($query));


	}
	if (page_id<0) $new_module="";
	$new_module=addslashes(stripslashes($new_module));

	$_level='';
	$query="SELECT level AS _level,type AS _type 
				FROM webtd 
				WHERE server=$SERVER_ID AND ver=$ver 
				AND lang='$lang' AND page_id=$page_id
				ORDER BY sid DESC LIMIT 1";
	parse_str(ado_query2url($query));


	if (strlen($_level) && !strlen($DEFAULT_TD_LEVEL) ) $DEFAULT_TD_LEVEL=$_level+0;
	$_type+=0;

	if (!strlen($DEFAULT_TD_LEVEL) ) $DEFAULT_TD_LEVEL=2;




	$query="INSERT INTO webtd  
			 (page_id,ver,lang,pri,level,server,title,nd_create,autor,type)
			  VALUES
			 ($page_id,$ver,'$lang',$maxpri,$DEFAULT_TD_LEVEL,
			  $SERVER_ID,'$new_module',".time().",'$PHP_AUTH_USER',$_type);";


	$update_and=($page_id>=0)?"AND id=$page_id":"";

	$query.="
		UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
                 WHERE server=$SERVER_ID AND ver=$ver 
		 AND lang='$lang' $update_and;";


	
	//echo nl2br($query);return;

	if (!strlen($error)) if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		webver_td($page_id,$maxpri,$action);

		if (strlen($mark_page_as_unproved_if_required))
		{
			$sql="SELECT max(sid) AS tdsid FROM webtd WHERE autor='$PHP_AUTH_USER'";
			parse_str(ado_query2url($sql));

			$query="UPDATE webpage SET unproof_sids=unproof_sids||'$tdsid:'
					 WHERE server=$SERVER_ID AND ver=$ver 
					 AND lang='$lang' $update_and;";
			$adodb->Execute($query);

		}
	}
	


?>
