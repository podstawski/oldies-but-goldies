<?
	$form=$MODULES->crm->files->task_exe->action->form;
	eval("\$form_fields = \$$form ;");


	$form_fields[t_time]=ereg_replace(",",".",$form_fields[t_time]);
	$form_fields[t_time]=ereg_replace("[^0-9\.]","",$form_fields[t_time]);

	$costxt="";
	while( list($key_key,$key_val) = each($form_fields) )
	{
		$costxt.=" <$key_key>";
		$costxt.=addslashes(htmlspecialchars(stripslashes($key_val)));
		$costxt.="</$key_key>\n";		
	}
	$costxt="<xml>\n$costxt</xml>";

	$cos=0+$form_fields[t_excuse];
	
	$query="SELECT sid FROM webtd WHERE server=$SERVER_ID
		AND page_id=$page_id AND ver=$ver AND lang='$lang'
		AND pri=$pri ";
	parse_str(ado_query2url($query));


	$MODULE_PATH="modules/@".$MODULES->crm->name;
	if (file_exists("$MODULE_PATH/.pre.h")) 
	{
		include_once("$MODULE_PATH/.pre.h");
		eval("\$cookieufpath = \"$CONST_TASK_UFILES\";");
		@rmdir("$UFILES/$cookieufpath");
	}

?>