<?
	global $MODULES,$TASK,$KAMELEON;
	global $action_progress;

	if (!module_select($MODULES->crm->files->task_master)) return;

	if ( !$action_progress && $KAMELEON[username] != $TASK[t_author] && $KAMELEON[username] != $TASK[t_executive])
	{
		echo label("You are neither an author nor the executive of the task");
		return;
	}

	_RevertDate($TASK[t_d_create]);
	_RevertDate($TASK[t_d_start]);
	_RevertDate($TASK[t_d_deadline]);
	_RevertDate($TASK[t_d_end]);



	if ($TASK[t_customer])
	{
		module_select($MODULES->crm->files->customer_master,"c_id=".$TASK[t_customer]);
		global $CUSTOMER;
		$TASK=array_merge($TASK,$CUSTOMER);
	}


	_display_view($MODULES->crm->files->task_master);
?>
