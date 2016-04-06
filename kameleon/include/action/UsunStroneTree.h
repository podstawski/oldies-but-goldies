<?
	$action="";

	if ($page_id==0) 
	{
		$error=label('You mast not delete root');
		return;
	}
	
	if (!$kameleon->checkRight('delete','page',$page_id))
	{
		$error=$norights;
		return;
	}	


	$query="SELECT prev FROM webpage 
		WHERE id=$page_id AND ver=$ver AND lang='$lang' AND server=$SERVER_ID";
	parse_str(ado_query2url($query));


	$ext=strlen($SERVER->file_ext)?$SERVER->file_ext:$KAMELEON_EXT;
	eval("\$pp=\"$PATH_PAGES_PREFIX\";");


	$query="
			INSERT INTO webpagetrash (server,ver,lang,page_id,file_name,nd_issue,status)
			SELECT $SERVER_ID,$ver,'$lang',$page_id,'$pp'||file_name,".time().",'N'
			FROM webpage WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID
			AND (id=$page_id OR tree ~ ':$page_id:') AND file_name<>'';

			INSERT INTO webpagetrash (server,ver,lang,page_id,file_name,nd_issue,status)
			SELECT $SERVER_ID,$ver,'$lang',$page_id,'$pp$PATH_PAGES/'||cast(id AS text)||'.$ext',".time().",'N'
			FROM webpage WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID
			AND (id=$page_id OR tree ~ ':$page_id:') AND (file_name='' OR file_name IS NULL);		

			DELETE FROM webpage WHERE (id=$page_id OR tree ~ ':$page_id:') AND ver=$ver AND lang='$lang' AND server=$SERVER_ID;
		
			DELETE FROM webtd WHERE page_id>0 AND ver=$ver AND lang='$lang' AND server=$SERVER_ID
			AND page_id NOT IN (SELECT id FROM webpage WHERE id=page_id AND webpage.ver=$ver AND webpage.lang='$lang' AND webpage.server=$SERVER_ID);
		 ";



	//echo nl2br($query);return;

	if (!$MAY_PROOF) return;


	if (!strlen($error)) 
			if ($adodb->Execute($query)) 
			{
				logquery($query) ;
				$page=$prev;
			}
?>
