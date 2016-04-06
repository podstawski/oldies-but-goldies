<?

	if (!$kameleon) global $kameleon;

	if ($submenu_id==-1)
	{
		push($menu_id);
		$menu_id=$submenu_id;
		include ("include/menu_max.h");
		$submenu_id=$menu_id;
		$menu_id=pop();
	}



	if (strlen($page_id))
	{
		$pole="page_id";
		$wart=$page_id;
		$page=$wart;

	}
	if (strlen($menu_id))
	{
		$pole="menu_id";
		$wart=$menu_id;
		$menu=$wart;
	}
	
	if ($menu_id && !$kameleon->checkRight('write','menu',$menu_id))
	{
		$error=$norights;
		return;
	}	
	
	
	$type+=0;
	if (!$type) $type="NULL";
	if (strlen($fgcolor) && $fgcolor[0]=="#") $fgcolor=substr($fgcolor,1);	

	if (!$submenu_id) $submenu_id="NULL";

	$now=time();

	$description=addslashes(stripslashes($description));

	$d_xml=base64_encode(serialize($_d_xml));

	if ($sid)
		$query="UPDATE weblink 
		 	SET alt='$alt',alt_title='$alt_title',
			description='$description',
			img='$img',
			imga='$imga',
			href='$href',
			variables='$variables', target='$target',
			fgcolor='$fgcolor',
			class='$class',
			submenu_id=$submenu_id,
			ufile_target='$ufile_target',
			type=$type,
			nd_update=$now,
			d_xml='$d_xml'
		 WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID
		 AND $pole=$wart AND sid=".$sid;
	else
		$query="UPDATE weblink 
		 	SET fgcolor='$fgcolor',
			class='$class',
			type=$type,
			nd_update=$now
		 WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID
		 AND $pole=$wart ";
	
	

	//echo nl2br($query);return;


	if ($adodb->Execute($query)) logquery($query) ;

	$sql="SELECT menu_sid FROM weblink WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID AND $pole=$wart LIMIT 1";
	parse_str(ado_query2url($sql));

	if (!$menu_sid) 
	{
		$sql="SELECT nextval('weblink_menu_id_seq'::text) AS menu_sid";
		parse_str(ado_query2url($sql));

		$query="UPDATE weblink SET menu_sid=$menu_sid
				WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID AND $pole=$wart";
		if ($adodb->Execute($query)) logquery($query) ;
	}


	webver_link($wart,$action,0,"$alt $alt_title");



	if (is_object($auth_acl) && is_array($_REQUEST['prawa']))
	{ 
		$ak=array_keys($_REQUEST['prawa']);	
		if ( acl_hasRight($ak[0],PAGE_GRANT_RIGHT,MENU_RESOURCE)) $auth_acl->matrix($_REQUEST['prawa'][$ak[0]]);
		if ($auth_acl->needSave() ) $auth_acl->save();
	}
