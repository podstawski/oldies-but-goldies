<?
	$action="";

	if (!strlen($menu_id)) return;

	$m=explode(":",$menu_id);
	$menu=$m[0];
	$name=addslashes(stripslashes($m[1]));
        
	if ( !$kameleon->checkRight('write','menu',$menu))
	{
		$error=$norights;
		return;
	}        

	$query="UPDATE weblink SET name='$name'
	 	 WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID
	 	 AND menu_id=$menu";

	//echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;

