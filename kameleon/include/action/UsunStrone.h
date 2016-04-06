<?
	$action="";
	if ($debug_mode) $adodb->debug=1;
	if ($page_id==0) $td_page_war="page_id<=0";
	else $td_page_war="page_id=$page_id";

	$td_page_war="page_id=$page_id";
	
	if ( !$kameleon->checkRight('delete','page',$page_id))
	{
		$error=$norights;
		return;
	}	


	$query="SELECT file_name AS fn,prev FROM webpage 
		WHERE id=$page_id AND ver=$ver AND lang='$lang' AND server=$SERVER_ID";
	parse_str(ado_query2url($query));

	$ext=strlen($SERVER->file_ext)?$SERVER->file_ext:$KAMELEON_EXT;
	if (!strlen($fn)) $fn=$DEFAULT_PATH_PAGES."/$page_id.$ext";

	eval("\$fn=\"$PATH_PAGES_PREFIX$fn\";");

	$prev+=0;

	$query="UPDATE webpage SET prev=$prev WHERE prev=$page_id AND ver=$ver AND lang='$lang' AND server=$SERVER_ID;
		 DELETE FROM webpage WHERE id=$page_id AND ver=$ver AND lang='$lang' AND server=$SERVER_ID;
		 DELETE FROM weblink WHERE page_id=$page_id AND ver=$ver AND lang='$lang' AND server=$SERVER_ID;
		 DELETE FROM webtd WHERE $td_page_war AND ver=$ver AND lang='$lang' AND server=$SERVER_ID;
		 INSERT INTO webpagetrash (server,ver,lang,page_id,file_name,nd_issue,status)
		 VALUES ($SERVER_ID,$ver,'$lang',$page_id,'$fn',".time().",'N')";

	$q="SELECT count(*) AS how_many_modules FROM webtd
		WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver
		AND page_id=$page_id AND html LIKE '@%'";
	parse_str(ado_query2url($q));
	
	if ($how_many_modules && !$force_allow) 
		$error=label("You must not delete any page containing attached modules");


	if (!$MAY_PROOF) return;

	//echo nl2br($query);return;


	if (!strlen($error)) 
	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		$page=$prev;
	}
