<?
	$action="";
	
	if (strlen($page))
	{
		$pole="page_id";
		$wart=$page;

	}
	if (strlen($menu))
	{
		$pole="menu_id";
		$wart=$menu;
	}
	
	if (!$kameleon->checkRight('insert','menu',$menu))
	{
		$error=$norights;
		return;
	}	
	

	$query="SELECT max(pri) AS maxpri FROM weblink 
			WHERE $pole=$wart AND lang='$lang' AND ver=$ver AND server=$SERVER_ID";
	parse_str(ado_query2url($query));
	$maxpri+=0;	
	$query="SELECT type,class,name,menu_sid FROM weblink 
			WHERE $pole=$wart AND lang='$lang' AND ver=$ver AND pri=$maxpri AND server=$SERVER_ID";
	parse_str(ado_query2url($query));

	$maxpri+=1;
	$type+=0;

	if ($navigationhidden>0 && !$type) $type=$navigationhidden;

	$insert_menu_sid=$value_menu_sid="";

	if ($menu_sid)
	{
		$insert_menu_sid=",menu_sid";
		$value_menu_sid=",$menu_sid";
	}
	
	if ($link_name)
	{
		$insert_menu_sid.=",alt";
		$value_menu_sid.=",'".$link_name."'";
	}

	$now=time();
	$query="INSERT INTO weblink  
			 (server,$pole,ver,lang,pri,type,class,name$insert_menu_sid,nd_create,nd_update)
			  VALUES
			 ($SERVER_ID,$wart,$ver,'$lang',$maxpri,$type,'$class','$name'$value_menu_sid,$now,$now)";
	
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;


?>