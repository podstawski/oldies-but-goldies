<?
	$al=0;
	$sql="SELECT accesslevel AS al FROM webtd WHERE sid=$sid ";
	parse_str(ado_query2url($sql));
	$al+=0;
	
	

	include_once('include/search.h');
	
	if ($al > $kameleon->current_server->accesslevel)
	{
		$error=label("Insufficient rights");
		return;
	}

	if (!$kameleon->checkRight('write','box',$sid) || !$kameleon->checkRight('write','page',$page_id))
	{
		$error=$norights;
		return;
	}


	if ($menu_id==-1)
	{
		include ("include/menu_max.h");
	}
	
	$menu_id+=0;
	if (!$menu_id) $menu_id="NULL";


	$level+=0;
	if (!$level) $level="NULL";
	$type+=0;
	if (!$type) $type="NULL";
	if (strlen($more)) $more+=0;
	else $more="NULL";
	if (strlen($next)) $next+=0;
	else $next="NULL";
	$cos+=0;
	if (!$cos) $cos="NULL";
	$size+=0;
	if (!$size) $size="NULL";
	$staticinclude+=0;

	$xmlset='';
	
	
	if ($_REQUEST['restore_plain'])
	{
		$filename=$_REQUEST['restore_plain'];
		
		if (file_exists("$save_and_restore_dir/$filename"))
		{
			$plain=addslashes(file_get_contents("$save_and_restore_dir/$filename"));
			
			$plain=str_replace("uimages/$CONST_EXPORT_SERVER_TOKEN/$CONST_EXPORT_VER_TOKEN","uimages/$SERVER_ID/$ver",$plain);
			$plain=str_replace("ufiles/$CONST_EXPORT_SERVER_TOKEN-att","ufiles/$SERVER_ID-att",$plain);
		}
	}	
	
	
 	$plain=trim($plain);

	$plain=eregi_replace(' class="[^"]*FCK[^"]*"','',$plain);

	if ($C_TRANSLATE_HTML_TO_XHTML) include ("include/action/html2xhtml.h");
	include ("include/action/catEmptyTags.h");


	$HOST_PATH="$HTTP_HOST/".dirname($SCRIPT_NAME)."/";
	$HOST_PATH=ereg_replace("[/]+","/",$HOST_PATH);
	$HOST_PATH=ereg_replace("/\./","/",$HOST_PATH);


	$HOST_PATH1="http[s]*://$HOST_PATH";
	$HOST_PATH2="http[s]*://[^@]+@$HOST_PATH";
   	$plain=eregi_replace($HOST_PATH1,"",$plain);
   	$plain=eregi_replace($HOST_PATH2,"",$plain);
	$plain=str_replace('FCKeditor/editor/','',$plain);
	$plain=str_replace(dirname($SCRIPT_NAME)."/$UFILES","$UFILES",$plain);
	$plain=str_replace("/$UFILES","$UFILES",$plain);
	
	$plain=eregi_replace("(<div[^>]*>)&nbsp;(</div>)","\\1\\2",$plain);

	$dn=dirname($SCRIPT_NAME);
	if ($dn=="/") $dn="";
	$plain=ereg_replace("$dn/($EREG_REPLACE_KAMELEON_UIMAGES)","\\1",$plain);

	$plain=eregi_replace("tdedit\.php\?[^#>]*#","kameleon:inside_link($page)#",$plain);

	if ($fetch_images_during_save) include ("include/action/fetch_images_during_save.h");




	if (strlen($bgcolor) && $bgcolor[0]=="#") $bgcolor=substr($bgcolor,1);
	$update_and=($page_id>=0)?"AND id=$page_id":"";

	if ($more==-1)
	{
		push($page_id);
		include("include/page_max.h");
		$more=$page_id;
		$page_id=pop();
	}

	if ($next==-1)
	{
		push($page_id);
		include("include/page_max.h");
		$next=$page_id;
		$page_id=pop();
		if ($next==$more) $next=$more+1;
	}

	if (strlen($module))
	{
		$bn=basename($module);
		$dn=dirname($module);
		$path="modules/$dn/.update/$bn";
		if (file_exists("$path") ) 
		{
			include("$path");
		}
		$html=$module;

		if (file_exists("modules/$dn/action.h") )
		{
			push($action);
			push($INCLUDE_PATH);
			$action=ereg_replace("\.h","_","${bn}update");
			$INCLUDE_PATH="modules/$dn";
			if (file_exists("modules/$dn/action/$action.h")) include ("modules/$dn/action.h");
			$INCLUDE_PATH=pop();
			$action=pop();
		}

	}

	if (isset($_REQUEST[xml])) 
	{
		$xml=addslashes(stripslashes($xml));	
		$xmlset="xml='$xml',";
	}
	$mod_action=addslashes(stripslashes($mod_action));


	$mod_valid="";

	if (isset($nd_valid_from))
	{
		$mod_valid=",nd_valid_from=";
		$mod_valid.= strlen($nd_valid_from)? "'".FormatujDateSQL($nd_valid_from)."'" : "null";
		$mod_valid.=",nd_valid_to=";
		$mod_valid.= strlen($nd_valid_to)? "'".FormatujDateSQL($nd_valid_to)."'" : "null";
	}


	$swfstyle=substr(strtolower($bgimg),strlen($bgimg)-4) == '.swf' ? 1 : 0;

	if ($swfstyle && !$width && $size=='NULL' && function_exists('getimagesize') && file_exists("$UIMAGES/$bgimg") )
	{
		$a=getimagesize ("$UIMAGES/$bgimg");
		if ($a[0]) $width=$a[0];
		if ($a[1]) $size=$a[1];
	}

	if ($swfstyle && is_array($_swf))
	{
		reset($SWF_OBJECT_PARAMS);
		$xml=array();
		while (list($name,$values)=each($SWF_OBJECT_PARAMS)) 
		{
			if (!strlen($_swf[$name]) && (strtolower($values)=='true|false' || strtolower($values)=='false|true') ) $_swf[$name]='false';

			$xml[]="_swf[$name]=".urlencode($_swf[$name]);

		}

		$xml=implode('&',$xml);
		$xmlset="xml='$xml',";
	}


	$ob=$obtd[1]+$obtd[2];

	$accesslevel+=0;

	if ($accesslevel > $kameleon->current_server->accesslevel)
	{
		$accesslevel=$kameleon->current_server->accesslevel;
	}

	if (strlen($mark_page_as_unproved_if_required))
	{
		$mark_page_as_unproved_if_required.=",unproof_sids=unproof_sids||'$sid:'";
	}

	$nohtml=polishtolower(wordsFromHtml(stripslashes("$title $plain")));

	$d_xml=base64_encode(serialize($_d_xml));
	
	$web20='';
	
	if (strlen($_REQUEST['web20']))
	{
		$w20=array('module'=>$_REQUEST['web20']);
		$w20['options'][$_REQUEST['web20']]=$_REQUEST['_web20'];
		$web20=base64_encode(serialize($w20));
	}
	
	
	$tdjs=trim(addslashes(stripslashes($_REQUEST['tdjs'])));

	$query="UPDATE webtd SET 
			img='$img',
			bgimg='$bgimg',
			swfstyle=$swfstyle,
			plain='$plain',
			title='$title',
			nohtml='$nohtml',
			bgcolor='$bgcolor',
			html='$html',
			class='$class',
			ob=$ob,
			align='$align',
			valign='$valign',
			width='$width',
			menu_id=$menu_id,
			d_xml='$d_xml',web20='$web20',js='$tdjs',
			type=$type, level=$level,
			more=$more,next=$next,
			cos=$cos,size=$size,
			accesslevel=$accesslevel,
			api='$api',costxt='$costxt',mod_action='$mod_action',
			staticinclude=$staticinclude ,$xmlset
			nd_update=".time().",autor_update='$PHP_AUTH_USER' $mod_valid
		 WHERE ver=$ver AND lang='$lang' AND sid=$sid AND server=$SERVER_ID
		 AND page_id=$page_id;
		UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
		 WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' $update_and;";
	

	if (!strlen($error)) 
	{
		if ($adodb->Execute($query)) 
		{
			logquery($query) ;
			webver_td($page_id,$pri,$action);
		}

	}


	if (is_object($auth_acl) && is_array($_REQUEST['prawa']))
	{ 
		$ak=array_keys($_REQUEST['prawa']);	
		if ( acl_hasRight($ak[0],PAGE_GRANT_RIGHT,TD_RESOURCE)) $auth_acl->matrix($_REQUEST['prawa'][$ak[0]]);
		if ($auth_acl->needSave() ) $auth_acl->save();
	}
	
	if ($_REQUEST['save_plain'])
	{
		if (!function_exists('str_to_url') ) include_once( strstr(strtolower($CHARSET),'utf') ? "include/str_to_url_utf.h" : "include/str_to_url_iso.h" );

		$filename=strtolower(str_to_url(ereg_replace('[ \/]','-',$title?$title:'notitle'))).".html";
		if (!file_exists($save_and_restore_dir)) mkdir($save_and_restore_dir);
		
		$plain2=preg_replace("#uimages/$SERVER_ID/[0-9]+#","uimages/$CONST_EXPORT_SERVER_TOKEN/$CONST_EXPORT_VER_TOKEN",$plain);
		$plain2=preg_replace("#ufiles/$SERVER_ID-att#","ufiles/$CONST_EXPORT_SERVER_TOKEN-att",$plain2);
		
		file_put_contents("$save_and_restore_dir/$filename",stripslashes($plain2));
	}
