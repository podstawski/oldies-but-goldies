<?
	global $MODULES,$PROC,$KAMELEON;

	if (!module_select($MODULES->crm->files->proc_master)) return;

	if ( !$editmode && !$action_progress && $KAMELEON[username] != $PROC[p_author] )
	{
		echo label("You are not an author of the process");
		return;
	}


	_RevertDate($PROC[p_d_create]);
	_RevertDate($PROC[p_d_start]);
	_RevertDate($PROC[p_d_deadline]);
	_RevertDate($PROC[p_d_end]);

	if ($PROC[p_customer])
	{
		module_select($MODULES->crm->files->customer_master,"c_id=".$PROC[p_customer]);
		global $CUSTOMER;
		$PROC=array_merge($PROC,$CUSTOMER);
	}

	if ($PROC[p_subcustomer])
	{
		module_select($MODULES->crm->files->customer_slave,"c_id=".$PROC[p_subcustomer]);
		global $PERSON;
		foreach(array_keys($PERSON) AS $v) if (strlen($PERSON[$v]) && $v!="c_id") $PROC[$v]=$PERSON[$v];
	}


	$PROC[ph_title]=label("Initiate");
	$query="SELECT ph_title FROM crm_proc_hist WHERE ph_id=".$PROC[p_state];
	
	if ($PROC[p_state])
	{
		parse_str(ado_query2url($query));
		$PROC[ph_title]=$ph_title;
	}

	$query="SELECT count(*) AS completed_state_count
			FROM crm_proc_hist
			WHERE ph_server=$SERVER_ID
			AND ph_proc=$PROC[p_id]
			AND ph_d_end IS NOT NULL";

	parse_str(ado_query2url($query));


	$PROC[state_completed_disable]=$completed_state_count ? "" : "style=\"display:none\"";


	$PROC[function_loop_begin]="process_hist_init";
	$PROC[function_loop_item]="process_hist_item";
	$PROC[function_loop_end]="process_hist_end";


	if (!function_exists("process_hist_init"))
	{
		function process_hist_init(&$iter_obj)
		{
			global $MODULES,$adodb,$SERVER_ID,$PROC;

			$iter_obj->i=0;
			$iter_obj->count=0;

			$query="SELECT * 
				FROM crm_proc_hist
				WHERE ph_server=$SERVER_ID
				AND ph_proc=$PROC[p_id]
				AND ph_d_end IS NOT NULL
				ORDER BY ph_d_end,ph_id";

			$res=$adodb->Execute($query);
			if ($res)
			{
				$iter_obj->count=$res->RecordCount();
				$iter_obj->result=$res;
			}
		}
	}

	if (!function_exists("process_hist_item"))
	{
		function process_hist_item(&$iter_obj)
		{
			global $page;

			$PH=$iter_obj->result->FetchRow($iter_obj->i);
			$iter_obj->i++;
			_RevertDate($PH[ph_d_create]);
			_RevertDate($PH[ph_d_start]);
			_RevertDate($PH[ph_d_deadline]);
			_RevertDate($PH[ph_d_end]);

			$PH[ph_desc]=ereg_replace("page=[0-9]+&pagecanchange=1","page=$page",$PH[ph_desc]);
			return ($PH);
		}
	}

	if (!function_exists("process_hist_end"))
	{
		function process_hist_end(&$iter_obj)
		{
			return ($iter_obj->count <= $iter_obj->i);
		}
	}


	$query="SELECT ph_state,ps_page_id
		 FROM crm_proc,crm_proc_hist,crm_proc_state
		 WHERE p_server=$SERVER_ID AND p_id=$PROC[p_id]
		 AND ph_id=p_state AND ph_state=ps_id";

	parse_str(ado_query2url($query));
	$ps_page_id+=0;

	$query="SELECT ps_id,ps_title FROM
			crm_proc_state,webtd
			WHERE webtd.server=$SERVER_ID AND ps_server=$SERVER_ID
			AND webtd.next=ps_page_id
			AND webtd.page_id=$ps_page_id
			GROUP BY ps_title,ps_id";

	$res=$adodb->Execute($query);

	$icons="";
	for ($i=0;$res && $i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		$var="";

		$var->label=$ps_title;
		$var->var[action]="CrmAddProcState";
		$var->var[ps_id]=$ps_id;

		$icons["state_$ps_id"]=$var;

	}
	$toolbar="";
	if (!is_array($icons) ) 
	{
		$var="";
		$var->label=label("Terminate");
		$var->var[action]="CrmTermProc";
		$icons["state_term"]=$var;
		
	}
	$toolbar->icon=$icons;
	$PROC[toolbar]=crm_toolbar($toolbar,$self);
	



	_display_view($MODULES->crm->files->proc_master);


?>
