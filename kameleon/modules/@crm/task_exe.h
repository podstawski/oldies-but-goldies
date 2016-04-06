<?
	global $MODULES,$EXE;

	while( list($key,$val)=each($WEBTD) )
	{
		$EXE[$key]=$val;
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
	
	_RevertDate($EXE[nd_create]);
	_RevertDate($EXE[nd_update]);
	$EXE[t_create]=substr($EXE[t_create],0,5);
	$EXE[t_update]=substr($EXE[t_update],0,5);
	$EXE[t_attach]="";

	
	global $sid;
	$sid=$WEBTD->sid;

	eval("\$dir=\"$CONST_TASK_UFILES\";");

	
	if (strlen($dir) && file_exists("$UFILES/$dir"))
	{
		$att=explode_path("$UFILES/$dir");
		for ($i=0; is_array($att) && $i<count($att) ; $i++ )
		{
			if (strlen($EXE[t_attach])) $EXE[t_attach].=", ";
			$EXE[t_attach].="<a href=\"$att[$i]\">";
			$EXE[t_attach].=basename($att[$i]);;
			$EXE[t_attach].="</a>";
		}
	}

	_display_view($MODULES->crm->files->task_exe);	


?>