<?
	if (!module_select($MODULES->crm->files->proc_state))
	{
		$PROC_HIST[ph_proc]=$PROC[p_id];
		$PROC_HIST[ph_executive]=$KAMELEON[username];
		$PROC_HIST[ph_author]=$KAMELEON[username];
		$PROC_HIST[ph_d_create]="(CURRENT_DATE)";

		module_update($MODULES->crm->files->proc_state);
		module_select($MODULES->crm->files->proc_state);
		$PROC_HIST[ph_executive]=$KAMELEON[username];
	}


	if ( strlen($PROC_HIST[ph_author]) && 
		 $KAMELEON[username] != $PROC_HIST[ph_author] && $KAMELEON[username] != $PROC_HIST[ph_executive])
	{
		echo label("You are neither an author nor the executive of the process");
		return;
	}

	_RevertDate($PROC_HIST[ph_d_create]);
	_RevertDate($PROC_HIST[ph_d_start]);
	_RevertDate($PROC_HIST[ph_d_deadline]);
	_RevertDate($PROC_HIST[ph_d_end]);


	$PROC_HIST[ph_auto_terminate_state_checked]=$PROC_HIST[ph_auto_terminate_state]?"checked":"";
	$PROC_HIST[ph_auto_terminate_state_disabled]=($KAMELEON[username] != $PROC_HIST[ph_author])?"disabled":"";


	$query="SELECT username AS crm_user 
		 FROM rights WHERE server=$SERVER_ID";
	if ($KAMELEON[username] != $PROC_HIST[ph_author] )
	{
		$query.=" AND username='$PROC_HIST[ph_executive]'";
	}
	$query.=" ORDER BY username";

	$user_res=$adodb->Execute($query);
	$user_count=$user_res->RecordCount();
	$crm_users="";
	for ($u=0;$u<$user_count;$u++)
	{
		parse_str(ado_ExplodeName($user_res,$u));
		$sel=($crm_user==$PROC_HIST[ph_executive])?" selected":"";
		$crm_users.="<option$sel value=\"$crm_user\">$crm_user</option>";
	}

	$PROC_HIST[ph_executive_options]=$crm_users;

	$proc_states="\n\t<option value=\"\">".label("Choose from predefined")."</option>";
	$query="SELECT ps_title,ps_id
		FROM crm_proc_state
		WHERE ps_server=$SERVER_ID
		ORDER BY ps_title";
	$state_res=$adodb->Execute($query);
	$state_count=$state_res->RecordCount();

	for ($s=0;$s<$state_count;$s++)
	{
		parse_str(ado_ExplodeName($state_res,$s));
		$sel=($PROC_HIST[ph_state]==$ps_id)?" selected":"";
		$ps_title=stripslashes($ps_title);
		$proc_states.="\n\t<option$sel value=\"$ps_id\">$ps_title</option>";
	}
	$PROC_HIST[ph_state_options]=$proc_states;



	_display_form($MODULES->crm->files->proc_state);
?>