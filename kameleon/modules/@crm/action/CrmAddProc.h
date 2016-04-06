<?
	global $page_id,$page,$pri;
	global $MODULES;
	global $PHP_AUTH_USER,$DEFAULT_TD_LEVEL;
	global $PROC,$HTTP_GET_VARS;



	push($page);
	push($page_id);
	push($HTTP_GET_VARS);


	//<query_if_there_is_process_list_module_for_this_customer>

	$_html="@" . $MODULES->crm->name . "/" . $MODULES->crm->files->proc_list->file;
	$query="SELECT count(*) AS c FROM webtd 
		WHERE server=$SERVER_ID AND page_id=$page
		AND ver=$ver AND lang='$lang'
		AND html='$_html'";
	parse_str(ado_query2url($query));

	if (!$c)
	{
		$page_id=$page;
		$HTTP_GET_VARS="";
		$HTTP_GET_VARS["html"]=$_html;
		$HTTP_GET_VARS["title"]=kameleon_global($MODULES->crm->files->proc_list->label);
		include("$INCLUDE_PATH/action/CrmAddTD.h");
	}
	$HTTP_GET_VARS=pop();
	push($HTTP_GET_VARS);	

	//</query_if_there_is_process_list_module_for_this_customer>


	

	if (isset($HTTP_GET_VARS[p_title]) )
	{
		$PROC[p_title]=$HTTP_GET_VARS[p_title];
		$PROC[p_type]=$HTTP_GET_VARS[p_type];
	}
	else
	{
		$query="SELECT count(*) AS c FROM crm_proc WHERE 
			 p_server=$SERVER_ID AND p_customer=$PROC[p_customer] ";
		parse_str(ado_query2url($query));
		$PROC[p_title]=label("New process")." ".($c+1);
	}


	//<find_init_state_for_this_process_type>
	if (strlen($PROC[p_type]))
	{
		global $STATE;

		$query="SELECT ps_id FROM crm_proc_state 
				WHERE ps_server=$SERVER_ID AND ps_xml ~ '$PROC[p_type]'";

		$res=$adodb->Execute($query);
		for ($i=0;is_object($res) && $i<$res->RecordCount(); $i++)
		{
			$STATE=$res->FetchRow($i);
			module_select($MODULES->crm->files->state,"ps_id=$STATE[ps_id]");
			if (strstr($STATE[ps_proc_init],$PROC[p_type]))
			{
				$PROC[p_state]=$STATE[ps_id];
				break;
			}
		}
	}
	//</find_init_state_for_this_process_type>


	$PROC[p_customer]=customer_id_on_page($page);
	$PROC[p_author]=$PHP_AUTH_USER;
	$PROC[p_d_create]="(CURRENT_DATE)";

	//<add_process_page_and_process_master>

	$page_id=-1;
	$referer=$page;
	include("include/action/DodajStrone.h");
	$page=$page_id;

	$_html="@" . $MODULES->crm->name . "/" . $MODULES->crm->files->proc_master->file;
	$HTTP_GET_VARS="";
	$HTTP_GET_VARS["html"]=$_html;
	$HTTP_GET_VARS["title"]=$PROC[p_title];

	include("$INCLUDE_PATH/action/CrmAddTD.h");

	//</add_process_page_and_process_master>


	// <init_state_for_process_found> 
	if ($PROC[p_state]) 
	{
		
		global $sid,$PROC_HIST;

		$sid=time()+$PROC[p_customer];
		$PROC_HIST[ph_d_create]="(CURRENT_DATE)";
		$PROC_HIST[ph_d_start]="(CURRENT_DATE)";
		$PROC_HIST[ph_d_end]="(CURRENT_DATE)";
		$PROC_HIST[ph_title]=$STATE[ps_title];
		$PROC_HIST[ph_state]=$STATE[ps_id];

		module_update($MODULES->crm->files->proc_state);
		module_select($MODULES->crm->files->proc_state);
		$PROC[p_state] = $PROC_HIST[ph_id];
	}
	// </init_state_for_process_found>


	module_update($MODULES->crm->files->proc_master);
	module_select($MODULES->crm->files->proc_master);

	$HTTP_GET_VARS=pop();
	$page_id=pop();
	$page=pop();
?>