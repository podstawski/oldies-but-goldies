<?
	if (!$_REQUEST['paste']) 
	{
		$error=label("Nothing found in kameleon cliboard");
		return;
	}


	$paste_menu_too=$_REQUEST['paste']>0?false:true;

	$sql="SELECT server AS ct0,lang AS ct1,ver AS ct2 ,page_id AS ct3 ,pri AS ct4,menu_id FROM webtd WHERE sid=".abs($_REQUEST['paste']);
	parse_str(ado_query2url($sql));

	if ($paste_menu_too && $menu_id)
	{
		$paste_menu_where_array=array('ver'=>$ct2,'lang'=>"'$ct1'",'server'=>$ct0,'menu_id'=>$menu_id);

	}

	if (!$ct0)
	{
		$error=label("Nothing found in kameleon cliboard");
		return;
	}
	$clibtd="$ct0:$ct1:$ct2:$ct3:$ct4";

	$ct=explode(":",$clibtd);
	
	$src=$ct;
	

	$UIMAGES_SRC='uimages/'.$src[0].'/'.$src[2];
	$UFILES_SRC='ufiles/'.$src[0].'-att';


	if (!$kameleon->checkRight('insert','box') || !$kameleon->checkRight('write','page',$page_id))
	{
		$error=$norights;
		return;
	}

	
	
	$query="SELECT count(*) AS c FROM webpage 
			WHERE server=$SERVER_ID
		 	AND id=$page AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($query));
	if (!$c) $error=label("Destination page does not exist");

	if (strlen($error)) return;
	

	$query="SELECT max(pri) as maxpri FROM webtd
			WHERE page_id=$page_id AND lang='$lang' 
			AND ver=$ver AND server=$SERVER_ID";
	parse_str(ado_query2url($query));
	$maxpri+=1;


	$_level = (strlen($_level)) ? $_level+0 : "level";

	$_plain="replace(replace(plain,'$UIMAGES_SRC/','$UIMAGES/'),'$UFILES_SRC/','$UFILES/')";
	if (!$adodb->session[system_parameters][sql_replace]) $_plain='plain';
	

	$query=kameleon_copy_query('webtd',
				array('server'=>$SERVER_ID,'page_id'=>$page_id,'ver'=>$ver,'lang'=>"'$lang'",
						'pri'=>$maxpri,'plain'=>$_plain,'level'=>$_level,'nd_create'=>time(),'nd_update'=>time(),
						'autor'=>"'$PHP_AUTH_USER'",
						'uniqueid'=>'','autor_update'=>''),
				array('ver'=>$src[2],'page_id'=>$src[3],'lang'=>"'$src[1]'",'pri'=>$src[4],'server'=>$src[0]));


	if ($paste_menu_too && $menu_id)
	{
		$maxmenu='';
		$sql="SELECT max(menu_id) AS maxmenu FROM weblink WHERE server=$SERVER_ID AND lang='$lang' AND ver=$ver";
		parse_str(ado_query2url($sql));
		$maxmenu++;

		$query=kameleon_copy_query('webtd',
				array('server'=>$SERVER_ID,'page_id'=>$page_id,'ver'=>$ver,'lang'=>"'$lang'",
						'pri'=>$maxpri,'plain'=>$_plain,'level'=>$_level,'nd_create'=>time(),'nd_update'=>time(),
						'autor'=>"'$PHP_AUTH_USER'",
						'uniqueid'=>'','autor_update'=>'','menu_id'=>$maxmenu),
				array('ver'=>$src[2],'page_id'=>$src[3],'lang'=>"'$src[1]'",'pri'=>$src[4],'server'=>$src[0]));



		$query.=";\n".kameleon_copy_query('weblink',
					array('server'=>$SERVER_ID,'lang'=>"'$lang'",'ver'=>$ver,'menu_id'=>$maxmenu),
					$paste_menu_where_array);
					
	}

	$query.=";
		UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
                 WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND id=$page;
			";

	//echo nl2br($query);return;

	if ($adodb->Execute($query)) 
	{
		logquery($query) ;

		if ($paste_menu_too && $maxmenu)
		{
			copy_menu_from($maxmenu,$paste_menu_where_array);
			return;
		}


		webver_td($page_id,$maxpri,$action);
	}

