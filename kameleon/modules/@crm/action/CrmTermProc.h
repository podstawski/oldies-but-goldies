<?
	global $MODULES,$HTTP_GET_VARS,$PROC;
	global $page_id,$KAMELEON;
	global $SZABLON_PATH,$TD_TYPY;
	global $WEBPAGE;


	$PROC="";

	//$adodb->debug=1;

	if (!module_select($MODULES->crm->files->proc_master)) return;

	if ($PROC[p_author]!=$KAMELEON[username])
	{
		$error=label("You are not the author");
		return;
	}

	$adodb->BeginTrans();
	$PROC[p_d_end]="(CURRENT_DATE)";


	if (!module_update($MODULES->crm->files->proc_master))
	{
		$adodb->RollbackTrans();
		$error=label("DB failure");
		return;
	}


	push($PROC);	
	$action="";
	ob_start();
	include("modules/@crm/".$MODULES->crm->files->proc_master->file);
	$p_desc=ob_get_contents();
	ob_end_clean();	
	$PROC=pop();

	//module_select($MODULES->crm->files->proc_master);
	$PROC[p_desc]=$p_desc;
	
	
	if (!module_update($MODULES->crm->files->proc_master))
	{
		$adodb->RollbackTrans();
		//$error=label("DB failure");
		return;
	}


	$query="UPDATE crm_proc SET p_page_id=NULL WHERE p_id=$PROC[p_id]";
	if (!$adodb->Execute($query))
	{
		$adodb->RollbackTrans();
		$error=label("DB failure");
		return;
	}
	
	

	if ($page!=$WEBPAGE->prev) $location_reload=kameleon_href("","",$WEBPAGE->prev);
	else
	{
		$tree=explode(":",$WEBPAGE->tree);
		$c=count($tree);
		$location_reload=kameleon_href("","",$tree[$c-3]);
	}
		
	$force_allow=1;
	$page_id=$page;
	include("include/action/UsunStrone.h");
	
	$adodb->CommitTrans();

	
	$action=$oldaction;

?>