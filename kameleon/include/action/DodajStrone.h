<?

	if (!$kameleon->checkRight('insert','page') )
	{
		$error=$norights;
		return;
	}
	
	include_once('include/webver.h');
	// Jezeli -1 (nowy)
	if ($page_id<0)
	{
		include("include/page_max.h");
		$page=$page_id;
	}
	if (strlen($ref_menu)) include("include/ref_menu.h");


	// Jezeli strona istnieje, to won
	$query="SELECT count(*) AS c FROM webpage
		WHERE ver=$ver AND id=$page_id 
		AND server=$SERVER_ID AND lang='$lang'";
	parse_str(ado_query2url($query));

	if ($c) $error=label("Page exists");
	if (strlen($error)) return;
	
	$id=""; $tree="";
	$referer+=0;
	$query="SELECT id,tree FROM webpage 
		 WHERE ver=$ver AND server=$SERVER_ID
		 AND lang='$lang'AND id=$referer";
	parse_str(ado_query2url($query));
	if (!strlen($id)) $referer=0;

	if ($referer) 
	{
		$tree.="$referer:";
		$warunek="id=$referer";
	}
	else 
	{
		$tree=":0:";
		$warunek="id<$page_id";
	}


	$newpage=label("New page",$lang);
	$title="$newpage $page_id";

	$query="SELECT title AS reftitle FROM webtd
			 WHERE ver=$ver AND server=$SERVER_ID
			 AND lang='$lang' AND more=$page_id LIMIT 1 ";
	parse_str(ado_query2url($query));

	if (strlen($reftitle)) $title=$reftitle;


	if ($page_id>0)
	{
	   $sql = array();
	   $sql['postgres']="INSERT INTO webpage
			(server,id,ver,lang,title,
				bgcolor,fgcolor,tbgcolor,tfgcolor,
				class,background,type,prev,submenu_id,menu_id,nd_create,tree,accesslevel)
			SELECT $SERVER_ID,$page_id,$ver,'$lang',
				'$title',
				bgcolor,fgcolor,tbgcolor,tfgcolor,
				class,background,type,$referer,submenu_id,menu_id,".time().",'$tree',accesslevel
			FROM webpage WHERE ver=$ver AND $warunek AND lang='$lang' AND server=$SERVER_ID 
			ORDER BY id DESC LIMIT 1";

	   $sql['mssql']="INSERT INTO webpage
			(server,id,ver,lang,title,
				bgcolor,fgcolor,tbgcolor,tfgcolor,
				class,background,type,prev,submenu_id,menu_id,nd_create,tree,accesslevel)
			SELECT TOP 1 $SERVER_ID,$page_id,$ver,'$lang',
				'$title',
				bgcolor,fgcolor,tbgcolor,tfgcolor,
				class,background,type,$referer,submenu_id,menu_id,".time().",'$tree',accesslevel
			FROM webpage WHERE ver=$ver AND $warunek AND lang='$lang' AND server=$SERVER_ID 
			ORDER BY id DESC";

		$sql = getProperQuery($sql);
	}
	else
	   $sql="INSERT INTO webpage 
		(server,id,ver,lang,title,nd_create,prev)
		VALUES
		($SERVER_ID,$page_id,$ver,'$lang','$newpage $page',".time().",-1)";

	$sql.="; $ref_menu_query";

	//echo nl2br($sql);return;
	if ($debug_mode) $adodb->debug=1;
	if ($adodb->Execute($sql)) 
	{
		logquery($sql) ;
		webver_page($page_id,$action);
	}
