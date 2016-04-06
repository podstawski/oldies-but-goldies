<?

	if (strlen($page_id))
	{
		$pages=explode(":",$page_id);
		$page=$pages[0];
		$pri=$pages[1];
		$pagedest=$pages[2];	
	}
	
	if ( !$kameleon->checkRight('write','page',$page))
	{
		$error=$norights;
		return;
	}	
	

	if (!$pri ) return;

	$query="SELECT count(*) AS c FROM webpage WHERE server=$SERVER_ID
		 AND id=$page AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($query));
	if (!$c) $error="Brak strony $page w wersji $ver";
	$query="SELECT count(*) AS c FROM webpage WHERE server=$SERVER_ID
		 AND id=$pagedest AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($query));
	if (!$c) $error=label("Destination page does not exist");


	if (strlen($error)) return;

	$query="SELECT max(pri) as maxpri FROM webtd
		WHERE page_id=$pagedest AND lang='$lang' 
		AND ver=$ver AND server=$SERVER_ID";
	parse_str(ado_query2url($query));
	$maxpri+=1;




	$query=kameleon_copy_query('webtd',
				array('server'=>$SERVER_ID,'page_id'=>$pagedest,
						'pri'=>$maxpri,'plain'=>$_plain,'nd_create'=>time(),'nd_update'=>time(),
						'autor'=>"'$PHP_AUTH_USER'",
						'uniqueid'=>'','autor_update'=>''),
				array('ver'=>$ver,'page_id'=>$page,'lang'=>"'$lang'",'pri'=>$pri,'server'=>$SERVER_ID));

	$query.=";
		UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
                 WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND id=$pagedest;
			";

/*

	$query="INSERT INTO webtd (server,page_id,ver,lang,pri,img,plain,html,menu_id,class, 
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,api,
			costxt,hidden,staticinclude,nd_create,autor,xml)
		SELECT $SERVER_ID,$pagedest,ver,lang,$maxpri,img,plain,html,menu_id,class,
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,api,
			costxt,hidden,staticinclude,".time().",'$PHP_AUTH_USER',xml
		FROM webtd WHERE ver=$ver AND page_id=$page 
			AND lang='$lang' AND pri=$pri AND server=$SERVER_ID;
		UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
                 WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND id=$pagedest;
		";

*/

	//echo nl2br($query);return;

	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		webver_td($pagedest,$maxpri,$action);
	}

	$page=$pagedest;

