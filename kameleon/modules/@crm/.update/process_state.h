<?
	include_once("modules/@crm/crmfun.h");


	$query="SELECT sid AS _sid FROM webtd WHERE server=$SERVER_ID
		AND page_id=$page_id AND ver=$ver AND lang='$lang'
		AND pri=$pri ";
	parse_str(ado_query2url($query));
	
	global $sid;
	$sid=$_sid;

	module_select($MODULES->crm->files->proc_master);

	$PROC_HIST[ph_proc]=process_id_on_page($page_id);

	if (!strlen($PROC_HIST[ph_executive])) 
	{
		if ( $KAMELEON[username] != $PROC[p_author] )
		{
			$error=label("You are not an author of the process");
			return;
		}
		//$PROC_HIST[ph_proc]=$PROC[p_id];
		$PROC_HIST[ph_executive]=$KAMELEON[username];
		$PROC_HIST[ph_author]=$KAMELEON[username];
		
	}
	if (!strlen($PROC_HIST[ph_d_create])) $PROC_HIST[ph_d_create]="(CURRENT_DATE)";

	_RevertDate($PROC_HIST[ph_d_create]);
	_RevertDate($PROC_HIST[ph_d_start]);
	_RevertDate($PROC_HIST[ph_d_deadline]);
	_RevertDate($PROC_HIST[ph_d_end]);

	
	if ( module_update($MODULES->crm->files->proc_state) )
	{
		$title=toText($PROC_HIST[ph_title]);
		module_select($MODULES->crm->files->proc_state);
	}


	if (strlen($PROC_HIST[ph_d_deadline]) && strlen($PROC_HIST[ph_d_start]) )
	{
		$query="SELECT count(*) AS c FROM crm_task 
			 WHERE t_server=$SERVER_ID 
			 AND t_proc_state=$PROC_HIST[ph_id]";
		parse_str(ado_query2url($query));
		if (!$c && $PROC_HIST[ph_id])
		{
			$HTTP_GET_VARS[t_proc_state]=$PROC_HIST[ph_id];
			$action="CrmAddTask";
		}
	}

?>