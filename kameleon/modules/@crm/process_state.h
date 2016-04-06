<?
	global $MODULES,$PROC_HIST,$sid,$KAMELEON;
	global $action_progress;

	push($PROC_HIST[edit]);
	$PROC_HIST="";
	$PROC_HIST[edit]=pop();
	
	if ($action_progress) $PROC_HIST[edit]="/";
	


	$sid=$WEBTD->sid;
	if (!module_select($MODULES->crm->files->proc_state)) return;

	if ( !$editmode && !$action_progress 
		&& $KAMELEON[username] != $PROC_HIST[ph_author] 
		&& $KAMELEON[username] != $PROC_HIST[ph_executive] )
	{
		echo label("You are neither an author nor the executive of the process");
		return;
	}


	$query="SELECT count(*) AS active_task_count FROM crm_task WHERE t_proc_state=$PROC_HIST[ph_id]
			AND t_server=$SERVER_ID
			AND t_d_end IS NULL";
	parse_str(ado_query2url($query));

	$query="SELECT count(*) AS completed_task_count FROM crm_task WHERE t_proc_state=$PROC_HIST[ph_id]
			AND t_server=$SERVER_ID
			AND t_d_end IS NOT NULL";
	parse_str(ado_query2url($query));


	
	$PROC_HIST[task_completed_disable]=$completed_task_count ? "" : "style=\"display:none\"";
	$PROC_HIST[task_active_disable]=$active_task_count ? "" : "style=\"display:none\"";	
	$PROC_HIST[completed_task_count]=$completed_task_count;
	$PROC_HIST[active_task_count]=$active_task_count;	



	$PROC_HIST[toolbar]="";
	$obj=$MODULES->crm->files->proc_state->toolbar;


	if (!strlen($PROC_HIST[ph_d_end]) && is_Object($obj)) 
	{
		if ($KAMELEON[username] != $PROC_HIST[ph_executive] || !$PROC_HIST[ph_state]) $hide[new_task]=1;

		if ($KAMELEON[username] != $PROC_HIST[ph_executive] 
			|| $KAMELEON[username] != $PROC_HIST[ph_author] 
			|| $active_task_count
			|| $PROC_HIST[ph_auto_terminate_state] ) $hide[state_completed]=1;

		$PROC_HIST[toolbar] = crm_toolbar($obj,$self,$hide);
	}

	_RevertDate($PROC_HIST[ph_d_create]);
	_RevertDate($PROC_HIST[ph_d_start]);
	_RevertDate($PROC_HIST[ph_d_deadline]);
	_RevertDate($PROC_HIST[ph_d_end]);


	$PROC_HIST[function_loop_begin]="process_task_init";
	$PROC_HIST[function_loop_item]="process_task_item";
	$PROC_HIST[function_loop_end]="process_task_end";



	if (!function_exists("process_task_init"))
	{
		function process_task_init(&$iter_obj)
		{
			global $MODULES,$sid,$adodb,$SERVER_ID;

			if (is_object($iter_obj))
			{
				$warunek_t_d_end="AND t_d_end IS NOT NULL";
				$order="t_d_end DESC";
			}
			else
			{
				$warunek_t_d_end="AND t_d_end IS NULL";
				$order="t_d_deadline";
			}

			$iter_obj->i=0;
			$iter_obj->count=0;


			$query="SELECT ph_id FROM crm_proc_hist
					WHERE ph_page_id=$sid AND
					ph_server=$SERVER_ID";
			
			parse_str(ado_query2url($query));

			if (!$ph_id) return;

			$query="SELECT * 
				FROM crm_task
				WHERE t_server=$SERVER_ID
				AND t_proc_state=$ph_id
				$warunek_t_d_end
				ORDER BY $order";

			$res=$adodb->Execute($query);
			if ($res)
			{
				$iter_obj->count=$res->RecordCount();
				$iter_obj->result=$res;
			}
		}
	}

	if (!function_exists("process_task_item"))
	{
		function process_task_item(&$iter_obj)
		{
			global $page;

			$TASK=$iter_obj->result->FetchRow($iter_obj->i);
			$iter_obj->i++;

			$TASK[t_href]=($TASK[t_page_id]) ? 
							kameleon_href("","",$TASK[t_page_id]) : 
							"javascript:CrmReport('task',$TASK[t_id])" ;

			return ($TASK);
		}
	}

	if (!function_exists("process_task_end"))
	{
		function process_task_end(&$iter_obj)
		{
			return ($iter_obj->count <= $iter_obj->i);
		}
	}


	$PROC_HIST[sid]=$WEBTD->sid;

	_display_view($MODULES->crm->files->proc_state);
?>
