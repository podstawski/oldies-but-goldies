<?

	$_name=$MODULES->crm->name;
	$_exe=$MODULES->crm->files->task_exe->file;
	$html="@$_name/$_exe";

	$query="SELECT count(*) AS c FROM webtd WHERE page_id=$page_id AND server=$SERVER_ID
			AND ver=$ver AND lang='$lang'
			AND html='$html'";

	parse_str(ado_query2url($query));

	if ($c)
	{
		$error=label("This task has execution entries");
		return;
	}

	$query="DELETE FROM crm_task WHERE t_page_id=$page_id AND t_server=$SERVER_ID";

	if ($adodb->Execute($query))
	{
		logquery($query);
		$action="UsunStrone";
	}
	else
	{
		echo $query;
		$error=label("Module deletion failure");
	}

?>