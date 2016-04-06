<?


	if (!$kameleon->checkRight('insert','page',$page))
	{
		$error=$norights;
		return;
	}
	
	
	$page_id=$page;
	if ($page_id==-1)
	{
		include("include/page_max.h");
		$page=$page_id;
	}
	if (strlen($ref_menu)) include("include/ref_menu.h");

	
	if (!$_REQUEST['paste']) 
	{
		$error=label("Nothing found in kameleon cliboard");
		return;
	}

	$sql="SELECT server AS ct0,lang AS ct1,ver AS ct2 ,id AS ct3  FROM webpage WHERE sid=".$_REQUEST['paste'];
	parse_str(ado_query2url($sql));


	if (!$ct0)
	{
		$error=label("Nothing found in kameleon cliboard");
		return;
	}

	$clibpage="$ct0:$ct1:$ct2:$ct3";

	$cp=explode(":",$clibpage);
	if (4!=count($cp)) $error=label("Nothing found in kameleon cliboard");
	if (strlen($error)) return;
	$src=$cp;


	$UIMAGES_SRC='uimages/'.$src[0].'/'.$src[2];
	$UFILES_SRC='ufiles/'.$src[0].'-att';





	$query="SELECT count(*) AS c FROM webpage
			WHERE ver=$ver AND id=$page
			AND server=$SERVER_ID AND lang='$lang'";
	parse_str(ado_query2url($query));
	if ($c) $error=label("Page exists");
	
	if (strlen($error)) return;

	$_prev = $referer+0;

	$_menu_id='menu_id';
	if ($src[0]!=$SERVER_ID) $_menu_id='-1*menu_id';
	$_next='-1*next';
	$_more='-1*more';
	$_nextpage='-1*next';

	$_plain="replace(replace(plain,'$UIMAGES_SRC/','$UIMAGES/'),'$UFILES_SRC/','$UFILES/')";
	if (!$adodb->session[system_parameters][sql_replace]) $_plain='plain';


	$sql="SELECT menu_id,next,more FROM webtd WHERE ver=$src[2] AND page_id=$src[3] 
		AND lang='$src[1]' AND server=$src[0] AND (menu_id>0 OR next>0 OR more>0)";

	$res=$adodb->execute($sql);
	for ($i=0;$i<$res->recordCount();$i++)
	{
		parse_str(ado_explodeName($res,$i));
		if ($menu_id && !strlen($MENU_ID_TRANSLATION[$menu_id]) && $src[0]!=$SERVER_ID) $MENU_ID_TRANSLATION[$menu_id]='';
		if ($more && !strlen($PAGE_ID_TRANSLATION[$more]) ) $PAGE_ID_TRANSLATION[$more]='';
		if ($next && !strlen($PAGE_ID_TRANSLATION[$next]) ) $PAGE_ID_TRANSLATION[$next]='';
	
	}

	$next=0;
	$sql="SELECT next FROM webpage 
			WHERE ver=$src[2] AND id=$src[3] 
			AND lang='$src[1]' AND server=$src[0];";
	parse_str(ado_query2url($sql));
	if ($next && !strlen($PAGE_ID_TRANSLATION[$next]) ) $PAGE_ID_TRANSLATION[$next]='';


	if (!strlen($PAGE_ID_TRANSLATION[$src[3]])) $PAGE_ID_TRANSLATION[$src[3]]=$page;



	$query=kameleon_copy_query('webpage',
				array('server'=>$SERVER_ID,'id'=>$page,'ver'=>$ver,'lang'=>"'$lang'",
						'next'=>$_nextpage,'prev'=>$_prev,'nd_create'=>time(),'nd_update'=>time(),
						'file_name'=>''),
				array('ver'=>$src[2],'id'=>$src[3],'lang'=>"'$src[1]'",'server'=>$src[0]));
	
	$query.=";\n".kameleon_copy_query('webtd',
				array('server'=>$SERVER_ID,'page_id'=>$page,'ver'=>$ver,'lang'=>"'$lang'",
						'plain'=>$_plain,'more'=>$_more,'next'=>$_next,'nd_create'=>time(),'nd_update'=>time(),
						'autor'=>"'$PHP_AUTH_USER'",
						'uniqueid'=>'','autor_update'=>''),
				array('ver'=>$src[2],'page_id'=>$src[3],'lang'=>"'$src[1]'",'server'=>$src[0]));

	$query.=";

		UPDATE webpage SET prev=0 WHERE prev=-1 AND server=$SERVER_ID AND id>0 ;

		$ref_menu_query	
	";


	//echo nl2br($query);return;

	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		webver_page($page,$action);
	}

	$tree=kameleon_tree($page);
	$query="UPDATE webpage SET tree='$tree' WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND id=$page";
	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
	}
