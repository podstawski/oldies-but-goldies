<?

	$more_set="";
	
	if (!$kameleon->checkRight('write','menu',$menu) && !$kameleon->checkRight('write','box',$sid) )
	{
		$error=$norights;
		return;
	}	

	if ($sid>0)
	{
		if ($menu)
		{
			$table="weblink";
			$warunki="menu_id=$menu AND sid=$sid";
			$update_page_date="";
		
		}
		else
		{
			$sql="SELECT sid AS tdsid FROM webtd WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND page_id=$page AND sid=$sid";
			parse_str(ado_query2url($sql));

			if (strlen($mark_page_as_unproved_if_required))
				$mark_page_as_unproved_if_required.=",unproof_sids=unproof_sids||'$tdsid:'";


			$table="webtd";
			$warunki="page_id=$page_id AND sid=".$sid;
			$update_and=($page_id>=0)?"AND id=$page_id":"";
			$update_page_date="
				UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
                 	WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' $update_and;";
			$more_set=",autor_update='$PHP_AUTH_USER',nd_update=".time();
		}
	}
	else
	{
		$table="webpage";
		$warunki="id=$page";

		if ($alltree) $warunki="(id=$page OR tree ~ ':$page:')";


		$update_page_date="
			UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
                 	WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND $warunki;
			";


		$q="SELECT file_name AS fn,hidden AS h FROM webpage 
				WHERE id=$page AND ver=$ver AND lang='$lang' AND server=$SERVER_ID";
		parse_str(ado_query2url($q));

		if (!$h)
		{

			$ext=strlen($SERVER->file_ext)?$SERVER->file_ext:$KAMELEON_EXT;
			if (!strlen($fn)) $fn=$DEFAULT_PATH_PAGES."/$page.$ext";
			eval("\$fn=\"$PATH_PAGES_PREFIX$fn\";");
			eval("\$pp=\"$PATH_PAGES_PREFIX\";");
			
			if ($alltree)
				$update_page_date.="
						INSERT INTO webpagetrash (server,ver,lang,page_id,file_name,nd_issue,status)
						SELECT $SERVER_ID,$ver,'$lang',$page,'$pp'||file_name,".time().",'N'
						FROM webpage WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID
						AND (id=$page OR tree ~ ':$page:') AND file_name<>'';

						INSERT INTO webpagetrash (server,ver,lang,page_id,file_name,nd_issue,status)
						SELECT $SERVER_ID,$ver,'$lang',$page,'$pp$PATH_PAGES/'||cast(id AS text)||'.$ext',".time().",'N'
						FROM webpage WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID
						AND (id=$page OR tree ~ ':$page:') AND (file_name='' OR file_name IS NULL);
						";
			else
				$update_page_date.="
						INSERT INTO webpagetrash (server,ver,lang,page_id,file_name,nd_issue,status)
						VALUES ($SERVER_ID,$ver,'$lang',$page,'$fn',".time().",'N')	
						";


		}
			
	} 

	$warunki.=" AND lang='$lang' AND ver=$ver AND server=$SERVER_ID";
	

	$query="SELECT hidden AS h FROM $table WHERE $warunki LIMIT 1";
	parse_str(ado_query2url($query));

	$h=$h?0:1;

	$query="UPDATE $table SET hidden=$h $more_set WHERE $warunki 
			AND ver=$ver AND lang='$lang' AND server=$SERVER_ID; 
			$update_page_date";

	if ($table=="webpage" && $h)
		$query.=";\nUPDATE weblink SET hidden=1 WHERE page_target=$page 
					AND ver=$ver AND lang='$lang' AND server=$SERVER_ID; ";
	

	


	//echo nl2br($query);return;


	if ($adodb->Execute($query)) 
	{
		if ($table=='webtd') webver_td($page_id,$pri,$action);
		if ($table=='weblink') webver_link($menu,$action);
		if ($table=='webpage') webver_page($page,$action);
		logquery($query) ;
	}
?>