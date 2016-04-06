<?
	global $MODULES,$HTTP_GET_VARS,$TASK;
	global $page_id,$page,$KAMELEON;
	
	$action="";

	if (!module_select($MODULES->crm->files->task_master)) return;


	if ($TASK[t_executive]!=$KAMELEON[username])
	{
		$error=label("You are not the executive");
		return;
	}


	$exe_file="@".$MODULES->crm->name."/".$MODULES->crm->files->task_exe->file;

	$query="SELECT * FROM webtd 
			WHERE page_id=$TASK[t_page_id] AND server=$SERVER_ID
			AND ver=$ver AND lang='$lang'
			AND html='$exe_file'
			ORDER BY pri";

	push($WEBTD);
	$t_desc="";
	$t_excuse="";
	$t_totaltime=0;

	global $SZABLON_PATH,$TD_TYPY;
	ob_start();
	$_wtd=ado_ObjectArray($adodb,$query);
	for ($_wtdi=0;$_wtdi<count($_wtd);$_wtdi++)
	{
		$WEBTD=$_wtd[$_wtdi];
		$obj=xml2obj($WEBTD->costxt);
		if (is_Object($obj))
		{
			$t_totaltime+=$obj->xml->t_time;
			if ($obj->xml->t_excuse)
			{
				$t_excuse.=trim($WEBTD->plain);
				continue;
			}

		}
		include("include/parser_td.h");

	}
	$t_desc=ob_get_contents();
	ob_end_clean();	

	$WEBTD=pop();

	$query="SELECT CURRENT_DATE>t_d_deadline AS toolate
		FROM crm_task WHERE t_id=$TASK[t_id]";

	parse_str(ado_query2url($query));


	if (strtolower($toolate)=="t" && !strlen($t_excuse))
	{
		$error=label("Latency requires excuse");
		return;
	}

	$TASK="";
	module_select($MODULES->crm->files->task_master);
	$TASK[t_desc]=$t_desc;
	$TASK[t_excuse]=$t_excuse;
	$TASK[t_totaltime]=0+$t_totaltime;
	$TASK[t_d_end]="(CURRENT_DATE)";



	$adodb->BeginTrans();

	if (!module_update($MODULES->crm->files->task_master))
	{
		$adodb->RollbackTrans();
		$error=label("DB failure");
		return;
	}

	push($WEBTD);

	$exe_file="@".$MODULES->crm->name."/".$MODULES->crm->files->task_master->file;

	$query="SELECT * FROM webtd 
			WHERE page_id=$TASK[t_page_id] AND server=$SERVER_ID
			AND ver=$ver AND lang='$lang'
			AND html='$exe_file'
			ORDER BY pri
			LIMIT 1";


	ob_start();
	$_wtd=ado_ObjectArray($adodb,$query);
	for ($_wtdi=0;$_wtdi<count($_wtd);$_wtdi++)
	{
		$WEBTD=$_wtd[$_wtdi];
		include("include/parser_td.h");
	}


	$t_desc=ob_get_contents().$t_desc;
	ob_end_clean();	

	$WEBTD=pop();

	$TASK="";
	module_select($MODULES->crm->files->task_master);
	$TASK[t_desc]=$t_desc;
	module_update($MODULES->crm->files->task_master);
	$query="UPDATE crm_task SET t_page_id=NULL WHERE t_id=$TASK[t_id]";
	if (!$adodb->Execute($query))
	{
		$adodb->RollbackTrans();
		$error=label("DB failure");
		return;
	}
		
	
	$page_id=$page;
	$query="UPDATE webtd SET html='' WHERE server=$SERVER_ID AND page_id=$page_id
		 AND lang='$lang' AND ver=$ver";
	$adodb->Execute($query);
	include("include/action/UsunStrone.h");
	$adodb->CommitTrans();
	global $WEBPAGE;
	$location_reload=kameleon_href("","",$WEBPAGE->prev);
	$page=$WEBPAGE->prev;

	$auto_module_att="$UFILES/.task/.".$TASK[t_id];
	$action=$oldaction;



	if (!$TASK[t_proc_state]) return;
	module_select($MODULES->crm->files->proc_state,"ph_id=".$TASK[t_proc_state]);

	global $PROC_HIST;
	if (!$PROC_HIST[ph_auto_terminate_state]) return;

	$query="SELECT count(*) AS c FROM crm_task 
		WHERE t_server=$SERVER_ID 
		AND t_d_end IS NULL
		AND t_proc_state=".$TASK[t_proc_state];
	parse_str(ado_query2url($query));
	if ($c) return;

	$HTTP_GET_VARS[t_proc_state]=$TASK[t_proc_state];
	$action="CrmStateCompleted";

?>