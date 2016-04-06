<?
	module_select($MODULES->crm->files->task_master);

	if ($TASK[t_executive]!=$KAMELEON[username])
	{	echo "<script> 
			alert('".label("You are not the executive")."');
			document.edytujtd.module.selectedIndex=0;
			ZapiszZmiany();
			</script>";
		return;
	}
	

	$obj=xml2obj($costxt);
	while( is_Object($obj) && list($key,$val)=each($obj->xml) )
	{
		$EXE[$key]=$val;
	}

	if (!is_Object($obj))
	{
		$EXE[t_time]="0";
	}

	$EXE[page]=$page;
	$EXE[page_id]=$page_id;


	eval("\$cookieufpath = \"$CONST_TASK_UFILES\";");
	$EXE[cookieufpath]=$cookieufpath;
	mkdir_p("$UFILES/$cookieufpath");

	$EXE[t_excuse_checked]=$cos?"checked":"";
	$query="SELECT CURRENT_DATE>t_d_deadline AS toolate
			FROM crm_task WHERE t_id=".obj_id_on_page($page);

	parse_str(ado_query2url($query));

	$EXE[t_excuse_disabled]=($toolate=="t")?"":"disabled";

	_display_form($MODULES->crm->files->task_exe);	
?>