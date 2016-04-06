<?php	
	global $C_SHOW_PAGE_FILENAME,$C_DIRECTORY_INDEX;


	if (!$C_SHOW_PAGE_FILENAME)
	{
		return;
	}

	if (!is_array($C_DIRECTORY_INDEX))
	{
		$error=label('No $C_DIRECTORY_INDEX array in your template');
		return;
	}

	if (!strlen($_title))
	{
		$_WEBPAGE=kameleon_page($page);
		$_title=strlen($_WEBPAGE[0]->title_short)?$_WEBPAGE[0]->title_short:$_WEBPAGE[0]->title;
	}




	if (!$kameleon->checkRight('write','page',$page))
	{
		$error=$norights;
		return;
	}

	$newpage=label("New page",$lang);
	if (substr($_title,0,strlen($newpage))==$newpage)
	{
		$error=label("Please change the title of this page");
		return;
	}


	if (!function_exists('str_to_url') ) include_once( strstr(strtolower($CHARSET),'utf') ? "include/str_to_url_utf.h" : "include/str_to_url_iso.h" );


	if ($page===0)
	{
		$may_rewrite=true;
		$file_name=$C_DIRECTORY_INDEX[0];
	}
	else
	{
		$f='';
		$may_rewrite=true;
		$tree=kameleon_tree($page);

		foreach (explode(':',$tree) AS $p)
		{
			if (!strlen($p)) continue;
			if (!$p) continue;

			

			$_WEBPAGE=kameleon_page($p+0);
			if ($_WEBPAGE[0]->nositemap || $_WEBPAGE[0]->hidden) continue;
			

			$_title2=strlen($_WEBPAGE[0]->title_short)?$_WEBPAGE[0]->title_short:$_WEBPAGE[0]->title;
			
			if (substr($_title2,0,strlen($newpage))==$newpage)
			{
				$error=label("Please change the title of the page").': '.$p;
				return;
			}
			
			$_title2=ereg_replace('^\.','',$_title2);
			$_title2=ereg_replace('\.$','',$_title2);

			$f.=str_to_url(ereg_replace('[ \/]','-',$_title2)).'/';

		}



		$_title=ereg_replace('^\.','',$_title);
		$_title=ereg_replace('\.$','',$_title);

		$f.=str_to_url(ereg_replace('[ \/]','-',$_title)).'/'.$C_DIRECTORY_INDEX[0];


		if ($may_rewrite) $file_name=strtolower($f);
		
	}
	

	
	if (!$may_rewrite) 
	{
		$file_name='';
		$error='';
		return;

	}



	$file_name=eregi_replace("[^0-9a-z\/\.\-]",'',$file_name);
	$file_name=ereg_replace("-+",'-',$file_name);





	$__fn__=$file_name;
	$__fn__=str_replace($C_DIRECTORY_INDEX[0],'',$__fn__);
	$__fn__=str_replace('/','',$__fn__);
	if (!strlen($__fn__) && $page>0)
	{
		$file_name='';
		return;
	}

	$sql_change="UPDATE webpage SET file_name='$file_name' WHERE id=$page AND ver=$ver AND lang='$lang' AND server=$SERVER_ID
					AND ( file_name IS NULL OR length(trim(file_name))<2) 
					AND '$file_name' NOT IN (SELECT file_name FROM webpage WHERE id<>$page AND ver=$ver AND lang='$lang' AND server=$SERVER_ID AND file_name IS NOT NULL) ";


	if (strstr(__FILE__,"$action."))
	{
		if ($adodb->Execute($sql_change)) logquery($sql_change) ;
	}
		