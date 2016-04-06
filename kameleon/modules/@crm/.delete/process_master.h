<?
	$query="DELETE FROM crm_proc WHERE p_page_id=$page_id AND p_server=$SERVER_ID";

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