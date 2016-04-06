<?


	$query="SELECT sid FROM webtd WHERE server=$SERVER_ID
		AND page_id=$page_id AND ver=$ver AND lang='$lang'
		AND pri=$pri ";
	parse_str(ado_query2url($query));

	module_select($MODULES->crm->files->proc_state);


	if(!$PROC_HIST[ph_id]) return;

	$query="SELECT count(*) AS c FROM crm_task WHERE t_proc_state=$PROC_HIST[ph_id]
			AND t_server=$SERVER_ID";

	parse_str(ado_query2url($query));

	if ($c)
	{
		$error=label("There are related tasks");
		return;
	}

	$query="DELETE FROM crm_proc_hist WHERE ph_server=$SERVER_ID AND ph_id=$PROC_HIST[ph_id]";

	if ($adodb->Execute($query) )
	{
		logquery($query);
	}
	else
	{
		$error=label("DB failure");
	}

?>