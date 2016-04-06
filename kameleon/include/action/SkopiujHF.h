<?
	$action="";

	if (strlen($page_id))
	{
		$pages=explode(":",$page_id);
		$pagesrc=0+$pages[0];
		$page_id_src=0+$pages[1];
		$pagedest=0+$pages[2];	
	}

	if (!$kameleon->checkRight('write','page',$pagesrc))
	{
		$error=$norights;
		return;
	}


	$query="SELECT type AS typesrc FROM webpage WHERE server=$SERVER_ID
		 AND id=$pagesrc AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($query));
	$typesrc+=0;

	$query="SELECT type AS typedest,id AS iddest FROM webpage WHERE server=$SERVER_ID
		 AND id=$pagedest AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($query));
	$typedest+=0;

	if ($typesrc==$typedest) $error=label("Areas at both pages are the same");
	if (!strlen($iddest)) $error=label("Destination page does not exist");

	$page_id_dest=($page_id_src%$MULTI_HF_STEP)-$MULTI_HF_STEP*$typedest;

	if (!strlen($error))
	{
		$query="SELECT count(*) AS c FROM webtd WHERE server=$SERVER_ID
			 AND page_id=$page_id_dest AND lang='$lang' AND ver=$ver";
		parse_str(ado_query2url($query));
		if ($c) $error=label("Destinatin area has already modules");
	}
	

	if (strlen($error)) return;


	$query=kameleon_copy_query('webtd',
				array('server'=>$SERVER_ID,'page_id'=>$page_id,
						'plain'=>$_plain,'nd_create'=>time(),'nd_update'=>time(),
						'autor'=>"'$PHP_AUTH_USER'",
						'uniqueid'=>'','autor_update'=>''),
				array('ver'=>$ver,'page_id'=>$page_id_src,'lang'=>"'$lang'",'server'=>$SERVER_ID));

	$query.=";
		UPDATE webpage SET d_update=".time()." $mark_page_as_unproved_if_required
                 WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND type=$typedest;
			";


/*
	$query="INSERT INTO webtd (server,page_id,ver,lang,pri,img,plain,html,menu_id,class, 
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,api,
			costxt,hidden,staticinclude,accesslevel)
		SELECT $SERVER_ID,$page_id_dest,ver,lang,pri,img,plain,html,menu_id,class,
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,api,
			costxt,hidden,staticinclude,accesslevel
		FROM webtd WHERE ver=$ver AND page_id=$page_id_src 
			AND lang='$lang' AND server=$SERVER_ID;


		UPDATE webpage SET d_update=".time()." $mark_page_as_unproved_if_required
                 WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND type=$typedest;
		";
*/

	//echo nl2br($query);return;

	if ($adodb->Execute($query)) 
	{
		logquery($query);
		$page=$pagedest;
	}

?>
