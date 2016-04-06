<?
	$action="";

	if (!$server) 
	{
		$query="SELECT id AS server FROM servers WHERE nazwa='$ServerName'";
		parse_str(ado_query2url($query));
	}

	if (!$server) return;

	$query="SELECT id AS srcid FROM servers WHERE nazwa='$ServerSrc'";
	parse_str(ado_query2url($query));

	if (!$srcid) return;

	
	$query="SELECT count(*) AS c1 FROM webpage WHERE server=$server";
	parse_str(ado_query2url($query));
	$query="SELECT count(*) AS c2 FROM weblink WHERE server=$server";
	parse_str(ado_query2url($query));
	$query="SELECT count(*) AS c3 FROM webtd WHERE server=$server";
	parse_str(ado_query2url($query));

	if ($c1+$c2+$c3) $error="$ServerName ".label("is not empty !. Delete all content from this server an try again.");
	if ($c1+$c2+$c3) return;

	
	$cmd="cp -R ../uimages/$srcid/* ../uimages/$server";
	if (!is_link("../uimages"))  system($cmd);



	$query=kameleon_copy_query('webpage', array('server'=>$server,'nd_create'=>time(),'nd_update'=>time()), array('server'=>$srcid));
	
	$query.=";\n".kameleon_copy_query('webtd',
					array('server'=>$server,'nd_create'=>time(),'nd_update'=>time(),'uniqueid'=>'','autor_update'=>''),
					array('server'=>$srcid));

	$query.=";\n".kameleon_copy_query('weblink', array('server'=>$server,'menu_sid'=>''), array('server'=>$srcid));

	$query.=";\n".kameleon_copy_query('class', array('server'=>$server), array('server'=>$srcid));


	//echo nl2br($query);return;
	if ($adodb->Execute($query)) 
	{
		logquery($query) ;

		$query="SELECT plain,page_id,pri,ver,lang FROM webtd
			WHERE server=$server";
		$res=$adodb->Execute($query);
		for ($i=0;$i<$res->RecordCount();$i++)
		{
			parse_str(ado_ExplodeName($res,$i));
			$plain=stripslashes($plain);

			$plain=ereg_replace("images/$srcid/","images/$server/",$plain);

			$plain=addslashes($plain);

			$query="UPDATE webtd SET plain='$plain' 
					WHERE page_id=$page_id AND pri=$pri AND ver=$ver AND lang='$lang'
					AND server=$server\n";
			$adodb->Execute($query);
		}

	}


