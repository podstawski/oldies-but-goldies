<?
	module_select($MODULES->crm->files->task_master);

	if ( $KAMELEON[username] != $TASK[t_author])
	{
		echo label("You are not an author of the TASKess");
		return;
	}

	if (!strlen($TASK[t_d_start])) $TASK[t_d_start]=$TASK[t_d_create];

	_RevertDate($TASK[t_d_create]);
	_RevertDate($TASK[t_d_start]);
	_RevertDate($TASK[t_d_deadline]);
	_RevertDate($TASK[t_d_end]);



	$limit="";
	if ($KAMELEON[username] != $TASK[t_author] ) $limit=$TASK[t_executive];

	$crm_users=crm_users($TASK[t_executive],$limit);
	$TASK[t_executive_options]=$crm_users;

	
	_display_form($MODULES->crm->files->task_master);
?>
