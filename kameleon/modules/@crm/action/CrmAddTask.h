<?
	global $MODULES;
	global $PHP_AUTH_USER,$DEFAULT_TD_LEVEL,$pri,$page_id,$page;

	global $HTTP_GET_VARS,$TASK;


	push($page);
	push($page_id);
	push($HTTP_GET_VARS);

	if ($HTTP_GET_VARS[t_proc_state])
	{
		$query="SELECT ph_executive AS t_author,
				ph_executive AS t_executive,
				ph_d_deadline AS t_d_deadline,
				ph_title AS t_title,
				ph_d_start AS t_d_start,
				ph_proc AS t_proc,
				ph_d_start>CURRENT_DATE AS t_future,
				ph_d_deadline<CURRENT_DATE AS t_late
			FROM crm_proc_hist
			WHERE ph_server=$SERVER_ID
			AND ph_id=$HTTP_GET_VARS[t_proc_state]";


		$res=$adodb->Execute($query);
		if ($res) $TASK=$res->FetchRow();

		$query="SELECT p_customer,p_subcustomer 
			FROM crm_proc WHERE p_server=$SERVER_ID 
			AND p_id=$TASK[t_proc]";

		if ($res) $res=$adodb->Execute($query);

		if ($res) 
		{
			$PROC=$res->FetchRow();
			$TASK[t_customer]=$PROC[p_customer];
		}


	}
	
	
	$TASK[t_proc_state]=$HTTP_GET_VARS[t_proc_state];

	if ($TASK[t_future]!="t") $TASK[t_d_start]="(CURRENT_DATE)";
	$TASK[t_d_create]="(CURRENT_DATE)";
	if ($TASK[t_late]=="t") $TASK[t_d_deadline]="(CURRENT_DATE)";

	
	$page_id=-1;
	$referer=$page;
	include("include/action/DodajStrone.h");
	$page=$page_id;
	$table="page";
	$title=toText($TASK[t_title]);
	include("include/action/ReTitle.h");
	module_update($MODULES->crm->files->task_master);
	$_html="@" . $MODULES->crm->name . "/" . $MODULES->crm->files->task_master->file;
	$HTTP_GET_VARS="";
	$HTTP_GET_VARS["html"]=$_html;
	$HTTP_GET_VARS["title"]=$TASK[t_title];
	include("$INCLUDE_PATH/action/CrmAddTD.h");;


	$HTTP_GET_VARS=pop();
	$page_id=pop();
	$page=pop();
?>