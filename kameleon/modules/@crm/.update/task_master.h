<?
	include_once("modules/@crm/crmfun.h");

	push($TASK);
	module_select($MODULES->crm->files->TASK_master);
	if ( strlen($TASK[t_author]) && $KAMELEON[username] != $TASK[t_author] )
	{
		$error=label("You are not an author of the TASK");
		$TASK=pop();
		return;
	}
	$TASK=pop();


	if (isset($TASK[t_d_create])) _RevertDate($TASK[t_d_create]);
	if (isset($TASK[t_d_start])) _RevertDate($TASK[t_d_start]);
	if (isset($TASK[t_d_deadline])) _RevertDate($TASK[t_d_deadline]);
	if (isset($TASK[t_d_end])) _RevertDate($TASK[t_d_end]);

	
	if ( module_update($MODULES->crm->files->task_master) )
	{
		$title=toText($TASK[t_title]);

		$t_title=label("Task").": $title";
		$q="UPDATE webpage SET title='$t_title' 
			 WHERE lang='$lang' AND
			 id=$page_id AND ver=$ver AND server=$SERVER_ID ;
			UPDATE weblink SET alt='$title' 
			 WHERE lang='$lang' AND
			 page_target=$page_id AND ver=$ver AND server=$SERVER_ID ;";

		if ($adodb->Execute($q)) logquery($q);
	}

?>
