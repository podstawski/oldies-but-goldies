<?
	global $MODULES,$KAMELEON;
	global $PHP_AUTH_USER,$DEFAULT_TD_LEVEL,$pri,$page_id,$page;

	global $HTTP_GET_VARS,$PROC_HIST,$STATE,$PROC;

	if (!$HTTP_GET_VARS[ps_id]) return;

	

	push($page,$page_id,$HTTP_GET_VARS,$WEBTD);

	module_select($MODULES->crm->files->state,"ps_id=$HTTP_GET_VARS[ps_id]");
	module_select($MODULES->crm->files->proc_master);
	

	$_html="@" . $MODULES->crm->name . "/" . $MODULES->crm->files->proc_state->file;
	$HTTP_GET_VARS="";
	$HTTP_GET_VARS["html"]=$_html;
	$HTTP_GET_VARS["title"]=$STATE[ps_title];
	include("$INCLUDE_PATH/action/CrmAddTD.h");;


	$PROC_HIST="";

	global $sid;
	$sid=$WEBTD[sid];
	$PROC_HIST[ph_proc]=$PROC[p_id];
	$PROC_HIST[ph_state]=$STATE[ps_id];
	$PROC_HIST[ph_title]=$STATE[ps_title];
	$PROC_HIST[ph_author]=$KAMELEON[username];
	$PROC_HIST[ph_executive]=$KAMELEON[username];
	$PROC_HIST[ph_d_create]="(CURRENT_DATE)";
	$PROC_HIST[ph_d_start]="(CURRENT_DATE)";
	if (strlen($STATE[ps_time])) 
	{
		$STATE[ps_time]+=0;
		$PROC_HIST[ph_d_deadline]="(CURRENT_DATE+$STATE[ps_time])";
	}

	module_update($MODULES->crm->files->proc_state);


	$after_action_reload="edit.php?page$page&page_id=$page&pri=$WEBTD[pri]";
	pop(&$page,&$page_id,&$HTTP_GET_VARS,&$WEBTD);

