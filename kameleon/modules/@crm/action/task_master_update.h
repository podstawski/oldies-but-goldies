<?
	global $TASK,$PROC,$PROC_HIST,$CUSTOMER;
	global $HTTP_HOST;

	module_select($MODULES->crm->files->task_master);

	if ($TASK[t_d_create] != date("Y-m-d"))
	{
		$action=""; return;
	}


	if ($TASK[t_customer]) 
		module_select($MODULES->crm->files->customer_master,"c_id=$TASK[t_customer]");

	if ($TASK[t_proc]) 
		module_select($MODULES->crm->files->proc_master,"p_id=$TASK[t_proc]");

	if ($PROC[p_subcustomer]) 
	{
		module_select($MODULES->crm->files->customer_slave,"c_server=$SERVER_ID AND c_id=$PROC[p_subcustomer]");
		foreach(array_keys($PERSON) AS $v) if (strlen($PERSON[$v]) && $v!="c_id") $CUSTOMER[$v]=$PERSON[$v];
	}

	
	if ($TASK[t_proc_state]) 
		module_select($MODULES->crm->files->proc_state,"ph_id=$TASK[t_proc_state]");


	_RevertDate($TASK[t_d_create]);
	_RevertDate($TASK[t_d_end]);
	_RevertDate($TASK[t_d_deadline]);
	_RevertDate($TASK[t_d_start]);
	_RevertDate($PROC[p_d_create]);
	_RevertDate($PROC[p_d_end]);
	_RevertDate($PROC[p_d_deadline]);
	_RevertDate($PROC[p_d_start]);
	_RevertDate($PROC_HIST[ph_d_create]);
	_RevertDate($PROC_HIST[ph_d_end]);
	_RevertDate($PROC_HIST[ph_d_deadline]);
	_RevertDate($PROC_HIST[ph_d_start]);

	$http_host=$HTTP_HOST;
?>