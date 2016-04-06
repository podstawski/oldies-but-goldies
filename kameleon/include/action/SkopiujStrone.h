<?

	$query="SELECT count(*) AS c FROM webpage
                WHERE ver=$ver AND id=$page
                AND server=$SERVER_ID AND lang='$lang'";
        parse_str(ado_query2url($query));

        if ($c) $error=label("Page exists");
        if ($c) return;

	if ( !$kameleon->checkRight('write','page',$page))
	{
		$error=$norights;
		return;
	}


	if ($page==0) $td_page_war="page_id<=0";
	else $td_page_war="page_id=$page";


	$query=kameleon_copy_query('webpage',
				array('server'=>$SERVER_ID,'ver'=>$ver,
						'nd_create'=>time(),'nd_update'=>time(),
						'file_name'=>''),
				array('ver'=>$src,'id'=>$page,'lang'=>"'$lang'",'server'=>$SERVER_ID));
	
	$query.=";\n".kameleon_copy_query('webtd',
				array('server'=>$SERVER_ID,'ver'=>$ver,
						'nd_create'=>time(),'nd_update'=>time(),
						'autor'=>"'$PHP_AUTH_USER'",
						'uniqueid'=>'','autor_update'=>''),
				array('ver'=>$src,'lang'=>"'$lang'",'server'=>$SERVER_ID))." AND $td_page_war";

	$query.=";\n
		UPDATE webpage SET prev=0 WHERE prev=-1  AND server=$SERVER_ID 
			AND id>0 AND server=$SERVER_ID ;		
	";




	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		webver_page($page,$action);
	}
