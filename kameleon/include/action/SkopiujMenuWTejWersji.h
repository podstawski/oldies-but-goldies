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
				array('server'=>$SERVER_ID,'menu_id'=>$menu,'nd_update'=>$now,'nd_create'=>$now),
				array('ver'=>$ver,'menu_id'=>$menusrc,'lang'=>"'$lang'",'server'=>$SERVER_ID));



	if ($adodb->Execute($query)) logquery($query) ;

