<?php
	error_reporting(7);

	if (file_exists('../const.php')) include_once('../const.php'); else include_once('../const.h');
	define ('ADODB_DIR',"../adodb/");
	include ("../include/adodb.h");

	include ("../include/const.h");
	include_once ("../include/kameleon.h");
	include_once ("../include/fun.h");

	for ($i=1;$i<$argc;$i++)
	{
		parse_str($argv[$i]);
	}


	if (strlen($server) && !is_integer($server))
	{
		$query="SELECT id AS server FROM servers WHERE nazwa='$server'";
		parse_str(ado_query2url($query));
	}

	if (!$server) usage($argv[0]);
	if (!strlen($src)) usage($argv[0]);
	if (!strlen($dst)) usage($argv[0]);



	$query="SELECT count(*) AS c1 FROM webpage WHERE server=$server AND lang='$dst'";
	parse_str(ado_query2url($query));
	$query="SELECT count(*) AS c2 FROM weblink WHERE server=$server AND lang='$dst'";
	parse_str(ado_query2url($query));
	$query="SELECT count(*) AS c3 FROM webtd WHERE server=$server AND lang='$dst'";
	parse_str(ado_query2url($query));

	if ($c1+$c2+$c3) 
	{
		echo "W wersji jezykowej '$dst' sa wpisy \n";
		return;
	}

	$query="
		INSERT INTO webpage (server,id,ver,lang,title,description,keywords, 
			bgcolor,fgcolor,tbgcolor,tfgcolor,class,background,
			type,next,prev,submenu_id,menu_id,nd_create,file_name,hidden,tree,pagekey,
			nositemap,noproof,title_short,accesslevel)
		SELECT server,id,ver,'$dst',title,description,keywords,
			bgcolor,fgcolor,tbgcolor,tfgcolor,class,background,
			type,next,prev,submenu_id,menu_id,".time().",file_name,hidden,tree,pagekey,
			nositemap,noproof,title_short,accesslevel
		FROM webpage WHERE server=$server AND lang='$src';

		INSERT INTO webtd (server,page_id,ver,lang,pri,img,plain,html,menu_id,class, 
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,
			api,costxt,hidden,staticinclude,autor,nd_create,mod_action,xml,swfstyle,ob,nd_valid_from,nd_valid_to,accesslevel)
		SELECT server,page_id,ver,'$dst',pri,img,plain,html,menu_id,class,
			align,valign,bgcolor,width,type,level,title,more,next,size,cos,bgimg,
			api,costxt,hidden,staticinclude,autor,".time().",mod_action,xml,swfstyle,ob,nd_valid_from,nd_valid_to,accesslevel
		FROM webtd WHERE server=$server AND lang='$src';

		INSERT INTO weblink (server,page_id,menu_id,ver,lang,img,imga,alt,alt_title,page_target,href,
					pri,fgcolor,type,class,variables,name,hidden,target,lang_target,submenu_id,accesslevel,submenu_id)
		SELECT server,page_id,menu_id,ver,'$dst',img,imga,alt,alt_title,page_target,href,
					pri,fgcolor,type,class,variables,name,hidden,target,lang_target,submenu_id,accesslevel,submenu_id
		FROM weblink WHERE server=$server AND lang='$src';
		";


	$query=kameleon_copy_query('webpage',
				array(	'lang'=>"'$dst'",'nd_create'=>time(),'nd_update'=>time()),
				array( 'lang'=>"'$src'",'server'=>$server)
				);
	
	$query.=";\n".kameleon_copy_query('webtd',
				array('lang'=>"'$dst'",'nd_create'=>time(),'nd_update'=>time(),
						'uniqueid'=>''),
				array( 'lang'=>"'$src'",'server'=>$server)
				);

	$query.=";\n".kameleon_copy_query('weblink',
			array('lang'=>"'$dst'"),
			array( 'lang'=>"'$src'",'server'=>$server)
			);





	//$adodb->debug=1;
	$adodb->execute($query);

	function usage($me)
	{
		echo "$me server=xx src=yy dst=zz\n";
		exit ();
	}
