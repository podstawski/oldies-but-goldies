<?
	global $MODULES,$PROC_HIST,$HTTP_GET_VARS;

	if (!$HTTP_GET_VARS[t_proc_state]) return;

	module_select($MODULES->crm->files->proc_state,"ph_server=$SERVER_ID AND ph_id=$HTTP_GET_VARS[t_proc_state]");

	if (!$PROC_HIST[ph_id]) return;


	$total_sql="UPDATE crm_proc
				SET p_state=$PROC_HIST[ph_id]
				WHERE p_server=$SERVER_ID AND p_id=$PROC_HIST[ph_proc]; \n";

	$query="SELECT plain AS ph_desc 
			FROM webtd 
			WHERE sid=$PROC_HIST[ph_page_id]
			AND server=$SERVER_ID";

	parse_str(ado_query2url($query));

	$total_sql.="DELETE FROM webtd 
	 			 WHERE sid=$PROC_HIST[ph_page_id]
				 AND server=$SERVER_ID;\n";

	$total_sql.="UPDATE crm_proc_hist
				 SET ph_d_end=CURRENT_DATE
				 WHERE ph_server=$SERVER_ID 
				 AND ph_id=$PROC_HIST[ph_id]; \n";


	$query="SELECT ps_page_id AS previous_state_page_id
				FROM crm_proc,crm_proc_hist,crm_proc_state
				WHERE p_server=$SERVER_ID AND p_id=$PROC_HIST[ph_proc]
				AND ph_id=p_state
				AND ph_state=ps_id";
	parse_str(ado_query2url($query));



		
	$adodb->BeginTrans();



	if ($adodb->Execute($total_sql))
	{
		logquery($total_sql);
		$total_sql="";
	}
	else
	{
		//$adodb->debug=1;$adodb->Execute($total_sql);$adodb->debug=0;
		$error=label("DB failure");
		$adodb->RollbackTrans();
		return;
	}


	$query="SELECT ps_page_id AS current_state_page_id,
				ph_id AS current_state_id
				FROM crm_proc,crm_proc_hist,crm_proc_state
				WHERE p_server=$SERVER_ID AND p_id=$PROC_HIST[ph_proc]
				AND ph_id=p_state
				AND ph_state=ps_id";
	parse_str(ado_query2url($query));



	push($WEBTD);

	ob_start();
	$WEBTD->sid=$PROC_HIST[ph_page_id];
	include("$INCLUDE_PATH/".$MODULES->crm->files->proc_state->file);


	if ( $previous_state_page_id && $current_state_page_id )
	{
		global $PROC,$CUSTOMER,$PERSON;
		module_select($MODULES->crm->files->proc_master,"p_server=$SERVER_ID AND p_id=$PROC_HIST[ph_proc]");
		module_select($MODULES->crm->files->customer_master,"c_server=$SERVER_ID AND c_id=$PROC[p_customer]");
		if ($PROC[p_subcustomer]) 
		{
			module_select($MODULES->crm->files->customer_slave,"c_server=$SERVER_ID AND c_id=$PROC[p_subcustomer]");
			foreach(array_keys($PERSON) AS $v) if (strlen($PERSON[$v]) && $v!="c_id") $CUSTOMER[$v]=$PERSON[$v];
		}

			
		$auto_module_att="";
		$query="SELECT t_id FROM crm_task WHERE t_proc_state = $current_state_id";
		$_tids = ado_ObjectArray($adodb,$query);
		for ($_i=0; is_array($_tids) && $_i < count($_tids); $_i++)
		{
			$tid=$_tids[$_i]->t_id;
			if (strlen($auto_module_att) ) $auto_module_att.=":";
			$auto_module_att.="$UFILES/.task/.$tid";
		}

		$query="SELECT * FROM webtd
				WHERE server = $SERVER_ID 
				AND lang = '$lang' AND ver = $ver 
				AND page_id = $previous_state_page_id 
				AND next = $current_state_page_id
				AND html LIKE '@%'";

		$_webtd = ado_ObjectArray($adodb,$query);
		for ($_webtd_i=0; is_array($_webtd) && $_webtd_i < count($_webtd); $_webtd_i++)
		{
			$WEBTD = $_webtd[$_webtd_i];
			if (!file_exists("modules/".$WEBTD->html)) continue;
			$auto_module_action = 1;
			include("modules/".$WEBTD->html);
		}
						
	}

	$ph_desc.=ob_get_contents();
	ob_end_clean();


	

	$WEBTD=pop();




	$ph_desc=addslashes(stripslashes($ph_desc));

	$total_sql="UPDATE crm_proc_hist
				 SET ph_desc='$ph_desc'
				 WHERE ph_server=$SERVER_ID 
				 AND ph_id=$PROC_HIST[ph_id]; \n";

	
	if ($adodb->Execute($total_sql))
	{
		logquery($total_sql);
		$total_sql="";
	}
	else
	{
		$adodb->RollbackTrans();
		$error=label("DB failure");
		return;
	}

	$adodb->CommitTrans();

	if ($PROC_HIST[ph_state])
	{
		global $STATE;
		module_select($MODULES->crm->files->state,"ps_id=$PROC_HIST[ph_state]");
		if ($STATE[ps_complete]) $action="CrmTermProc";
	}
	
	

?>