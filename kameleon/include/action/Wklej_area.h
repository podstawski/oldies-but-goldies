<?
	$action="";
	
	if (!$kameleon->checkRight('insert','box'))
	{
		$error=$norights;
		return;
	}	

	$ca=explode(':',$_REQUEST['paste']);

	if (4!=count($ca)) $error=label("Nothing found in kameleon cliboard");
	if (strlen($error)) return;
	$src=$ca;


	$UIMAGES_SRC='uimages/'.$src[0].'/'.$src[2];
	$UFILES_SRC='ufiles/'.$src[0].'-att';

	$_plain="replace(replace(plain,'$UIMAGES_SRC/','$UIMAGES/'),'$UFILES_SRC/','$UFILES/')";
	if (!$adodb->session[system_parameters][sql_replace]) $_plain='plain';


	$query="SELECT count(*) AS c FROM webtd WHERE server=$SERVER_ID
			 AND page_id=$page_id AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($query));
	if ($c) $error=label("The area has already modules");

	$query="SELECT count(*) AS c FROM webpage WHERE server=$SERVER_ID
			 AND id=$page AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($query));
	if (!$c) $error=label("Page does not exist");

	if (strlen($error)) return;

	$query="SELECT type AS typedest FROM webpage WHERE server=$SERVER_ID
			 AND id=$page AND lang='$lang' AND ver=$ver";
	parse_str(ado_query2url($query));	
	$typedest+=0;	
	


	$query=kameleon_copy_query('webtd',
				array('server'=>$SERVER_ID,'page_id'=>$page_id,'ver'=>$ver,'lang'=>"'$lang'",
						'plain'=>$_plain,'nd_create'=>time(),'nd_update'=>time(),
						'autor'=>"'$PHP_AUTH_USER'",
						'uniqueid'=>'','autor_update'=>''),
				array('ver'=>$src[2],'page_id'=>$src[3],'lang'=>"'$src[1]'",'server'=>$src[0]));

	$query.=";
		UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
                WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' AND type=$typedest;
			";

	/*

	$query="INSERT INTO webtd (server,page_id,ver,lang,pri,img,plain,html,menu_id,class, 
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,api,
			costxt,hidden,staticinclude,nd_create,nd_update,autor,swfstyle,nd_valid_from,nd_valid_to,ob,accesslevel)
		SELECT $SERVER_ID,$page_id,$ver,'$lang',pri,img,$_plain,html,menu_id,class,
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,api,
			costxt,hidden,staticinclude,".time().",".time().",'$PHP_AUTH_USER',swfstyle,nd_valid_from,nd_valid_to,ob,accesslevel 
		FROM webtd WHERE ver=$src[2] AND page_id=$src[3] 
			AND lang='$src[1]' AND server=$src[0];
			
		UPDATE webpage SET nd_update=".time()." $mark_page_as_unproved_if_required
                WHERE server=$SERVER_ID AND ver=$ver AND lang='$lang' 
				AND type=$typedest;
		";
	*/

	//echo nl2br($query);return;

	if ($adodb->Execute($query)) 
	{
		logquery($query);
	}

