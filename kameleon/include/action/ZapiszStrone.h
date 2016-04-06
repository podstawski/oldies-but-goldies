<?
	global $kameleon,$C_SHOW_PAGE_FILENAME;

	if (!$kameleon->checkRight('write','page',$page))
	{
		$error=$norights;
		return;
	}

	$keywords=addslashes(stripslashes($keywords));
	$description=addslashes(stripslashes($description));
	$title=addslashes(stripslashes($title));
	$title_short=addslashes(stripslashes($title_short));
	if ($debug_mode) $adodb->debug=1;
	$type+=0;
	if (!$type) $type="NULL";
	$next+=0;
	if (!$next) $next="NULL";

	if (0==strlen(trim($prev))) $prev="NULL";
	else $prev+=0;

	if ($page==0 && $prev==0) $prev="NULL";
	
	$submenu_id+=0;
	if (!$submenu_id) $submenu_id="NULL";
	$menu_id+=0;

	$pagekey;

	if (strlen($fgcolor) && $fgcolor[0]=="#") $fgcolor=substr($fgcolor,1);
	if (strlen($bgcolor) && $bgcolor[0]=="#") $bgcolor=substr($bgcolor,1);
	if (strlen($tfgcolor) && $tfgcolor[0]=="#") $tfgcolor=substr($tfgcolor,1);
	if (strlen($tbgcolor) && $tbgcolor[0]=="#") $tbgcolor=substr($tbgcolor,1);


	$query="";


	if ($page==0) $prev=-1;

	$sql="SELECT file_name AS fn FROM webpage WHERE id=$page AND ver=$ver AND lang='$lang' AND server=$SERVER_ID";
	parse_str(ado_query2url($sql));

	if (!strlen($file_name) && $C_SHOW_PAGE_FILENAME==1 )
	{
		$_title=strlen($title_short)?$title_short:$title;
		include(dirname(__FILE__).'/ZapiszStroneNazwa.h');
		if (strlen($error))
		{
			$file_name='';
			$error='';
		}
		
	}

	

	if (strlen($file_name))
	{
		$sql="SELECT id AS innastrona FROM webpage WHERE id<>$page AND ver=$ver AND lang='$lang' AND server=$SERVER_ID 
				AND (hidden=0 OR hidden IS NULL) AND file_name='$file_name' AND file_name IS NOT NULL AND file_name<>'' LIMIT 1";
		parse_str(ado_query2url($sql));

		if (strlen($innastrona))
		{
			$error=label('File name exists')." [$innastrona]";
			return;
		}
	}

	if ($next==-1)
	{
		$___page=$page_id;
		include("include/page_max.h");
		$next=$page_id;
		$page_id=$___page;
	}

	$d_xml=base64_encode(serialize($_d_xml));

	$query="UPDATE webpage 
		 SET 	title='$title',
			title_short='$title_short',
			description='$description',
			keywords='$keywords',
			class='$class',
			bgcolor='$bgcolor',
			fgcolor='$fgcolor',
			tbgcolor='$tbgcolor',
			tfgcolor='$tfgcolor',
			file_name='$file_name',
			type=$type,
			prev=$prev,
			next=$next,
			d_xml='$d_xml',
			background='$background',
			nd_update=".time().",
			pagekey='$pagekey',
			submenu_id=$submenu_id,menu_id=$menu_id
			$mark_page_as_unproved_if_required
		 WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID
		 AND id=$page;\n";
	

	if ($fn!=$file_name)
	{
		$ext=strlen($SERVER->file_ext)?$SERVER->file_ext:$KAMELEON_EXT;
		if (!strlen($fn)) $fn=$DEFAULT_PATH_PAGES."/$page.$ext";

		eval("\$fn=\"$PATH_PAGES_PREFIX$fn\";");
		$query.="INSERT INTO webpagetrash (server,ver,lang,page_id,file_name,nd_issue,status)
		 VALUES ($SERVER_ID,$ver,'$lang',$page,'$fn',".time().",'N');\n";
	}

	if ($page!=$newid)
	{
		$id='';
		$q="SELECT title,id FROM webpage WHERE id=$newid AND ver=$ver AND lang='$lang' AND server=$SERVER_ID LIMIT 1";
		parse_str(ado_query2url($q));

		$l_page=label("Page");
		$l_exists=label("exists");
		if (strlen($id)) 
			$error="$l_page $pages[1] $l_exists ($title) !";
	
		$query.=";
			 UPDATE webpage SET id=$newid
			 WHERE ver=$ver AND id=$page AND server=$SERVER_ID;
			 UPDATE weblink SET page_id=$newid
			 WHERE ver=$ver AND page_id=$page AND server=$SERVER_ID;
			 UPDATE weblink SET page_target=$newid
			 WHERE ver=$ver AND page_target=$page AND server=$SERVER_ID;
			 UPDATE webtd SET page_id=$newid
			 WHERE ver=$ver AND page_id=$page AND server=$SERVER_ID;
			 
			 UPDATE webpage SET next=$newid
			 WHERE next=$page AND ver=$ver AND lang='$lang' AND server=$SERVER_ID;
			 UPDATE webpage SET prev=$newid
			 WHERE prev=$page AND ver=$ver AND lang='$lang' AND server=$SERVER_ID;
			 UPDATE webtd SET next=$newid
			 WHERE next=$page AND ver=$ver AND lang='$lang' AND server=$SERVER_ID;
			 UPDATE webtd SET more=$newid
			 WHERE more=$page AND ver=$ver AND lang='$lang' AND server=$SERVER_ID;
			";

	}


	if ($prev_prev != $prev && $prev!='NULL' && $prev>=1)
	{
		$query.=";
				UPDATE webpage SET tree='' WHERE (id=$page OR tree ~ ':$page:') AND ver=$ver AND lang='$lang' AND server=$SERVER_ID
				";
	}


	//echo nl2br($query);return;
	if (!strlen($error)) if ($adodb->Execute($query)) 
	{
		if ($page!=$newid) $page=$newid;
		logquery($query) ;
		webver_page($page,$action);
	}

	if (is_object($auth_acl) && is_array($_REQUEST['prawa']))
	{ 
		$ak=array_keys($_REQUEST['prawa']);	
		if ( acl_hasRight($ak[0],PAGE_GRANT_RIGHT,PAGE_RESOURCE)) $auth_acl->matrix($_REQUEST['prawa'][$ak[0]]);
		if ($auth_acl->needSave() ) $auth_acl->save();
	}


	if (!$ACL_RIGHTS || !is_array($ACL)) return;


	$query="DELETE FROM kameleon_acl 
		WHERE ka_server=$SERVER_ID 
		AND ka_oid=$ACL[resource_id] 
		AND ka_resource_name='$ACL[resource_name]'";
	if ($adodb->Execute($query)) logquery($query) ;

	$_user="";
	while (list($acl_key,$acl_val)=each($ACL))
	{
		if (strstr($acl_key,"resource_")) continue;

		$acl=explode(":",$acl_key);
		if ($_user!=$acl_val && count($acl)==1 )
		{
			if (strlen($_user)) 
			{
				$query=kameleon_acl_update_add($ACL[resource_id],$ACL[resource_name],$_user,$_rights);	
				if ($adodb->Execute($query)) logquery($query) ;
			}
			$_user=$acl_val;
			$_rights="";
		}
		$_rights.=$acl[1];
		
	}
	if (strlen($_user)) 
	{
		$query=kameleon_acl_update_add($ACL[resource_id],$ACL[resource_name],$_user,$_rights);
		if ($adodb->Execute($query)) logquery($query) ;
	}


