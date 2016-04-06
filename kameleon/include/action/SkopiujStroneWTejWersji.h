<?

	$pagesrc+=0;

	$page_id=$page;
	$ref_menu_query="";
	if ($page_id==-1)
	{
		include("include/page_max.h");
		$page=$page_id;

	}
	
	if (!$kameleon->checkRight('write','page',$page))
	{
		$error=$norights;
		return;
	}	
	
	
	if (strlen($ref_menu)) include("include/ref_menu.h");

 	$query="SELECT count(*) AS c FROM webpage
                WHERE ver=$ver AND id=$page
                AND server=$SERVER_ID AND lang='$lang'";
        parse_str(ado_query2url($query));

        if ($c) $error=label("Page exists");
        if ($c) return;




	$query="INSERT INTO webpage (server,id,ver,lang,title,title_short,description,keywords, 
			bgcolor,fgcolor,tbgcolor,tfgcolor,class,background,type,
			next,prev,submenu_id,menu_id,hidden,nd_create,nd_update,noproof,accesslevel)
		SELECT $SERVER_ID,$page,ver,lang,title,title_short,description,keywords,
			bgcolor,fgcolor,tbgcolor,tfgcolor,class,background,type,
			next,prev,submenu_id,menu_id,hidden,".time().",".time().",noproof,accesslevel
		FROM webpage WHERE ver=$ver AND id=$pagesrc AND lang='$lang' AND server=$SERVER_ID;

		INSERT INTO webtd (server,page_id,ver,lang,pri,img,plain,html,menu_id,class, 
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,api,
			costxt,hidden,staticinclude,nd_create,nd_update,autor,swfstyle,nd_valid_from,nd_valid_to,ob,accesslevel)
		SELECT $SERVER_ID,$page,ver,lang,pri,img,plain,html,menu_id,class,
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,api,
			costxt,hidden,staticinclude,".time().",".time().",'$PHP_AUTH_USER',swfstyle,nd_valid_from,nd_valid_to,ob,accesslevel 
		FROM webtd WHERE ver=$ver AND page_id=$pagesrc AND lang='$lang' 
			AND server=$SERVER_ID;

		UPDATE webpage SET prev=0 WHERE prev=-1 AND server=$SERVER_ID 
			AND id>0 AND server=$SERVER_ID ;

		$ref_menu_query
		";






	//echo nl2br($query);return;

	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		webver_page($page,$action);
	}

	$query="";
