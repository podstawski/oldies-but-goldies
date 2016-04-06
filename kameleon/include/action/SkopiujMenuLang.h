<?
	$action="";
        
	if (!$kameleon->checkRight('insert','menu'))
	{
		$error=$norights;
		return;
	}        
        

	$query="SELECT count(*) FROM weblink WHERE server=$SERVER_ID
		  AND ver=$ver AND menu_id=$menu AND lang='$lang'"; 
	parse_str(ado_query2url($query));
	if ($count) return;


	$now=time();
	$query=kameleon_copy_query('weblink',
				array('server'=>$SERVER_ID,'lang'=>"'$lang'",'nd_update'=>$now,'nd_create'=>$now),
				array('ver'=>$ver,'menu_id'=>$menu,'lang'=>"'$src'",'server'=>$SERVER_ID));

	/*

	$query="INSERT INTO weblink (server,page_id,menu_id,ver,lang,img,imga,alt,page_target,href,
					pri,fgcolor,type,class,variables,name,hidden,alt_title,accesslevel)
		SELECT $SERVER_ID,page_id,menu_id,ver,'$lang',img,imga,alt,page_target,href,
					pri,fgcolor,type,class,variables,name,hidden,alt_title,accesslevel
		FROM weblink WHERE 
			ver=$ver AND menu_id=$menu AND lang='$src' 
			AND server=$SERVER_ID;
		";
	*/

	//echo nl2br($query);return;

	if ($adodb->Execute($query)) logquery($query) ;
?>
