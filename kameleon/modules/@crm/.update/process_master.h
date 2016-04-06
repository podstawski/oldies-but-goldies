<?
	include_once("modules/@crm/crmfun.h");
	
	global $PROC;

	push($PROC);
	module_select($MODULES->crm->files->proc_master);
	if ( strlen($PROC[p_author]) && $KAMELEON[username] != $PROC[p_author] )
	{
		$error=label("You are not an author of the process");
		return;
	}
	$PROC=pop();


	if (isset($PROC[p_d_create])) _RevertDate($PROC[p_d_create]);
	if (isset($PROC[p_d_start])) _RevertDate($PROC[p_d_start]);
	if (isset($PROC[p_d_deadline])) _RevertDate($PROC[p_d_deadline]);
	if (isset($PROC[p_d_end])) _RevertDate($PROC[p_d_end]);
	
	
	if ( module_update($MODULES->crm->files->proc_master) )
	{
		$title=toText($PROC[p_title]);

		$p_title=label("Process").": $title";
		$q="UPDATE webpage SET title='$p_title' 
			 WHERE lang='$lang' AND
			 id=$page_id AND ver=$ver AND server=$SERVER_ID ;
			UPDATE weblink SET alt='$title' 
			 WHERE lang='$lang' AND
			 page_target=$page_id AND ver=$ver AND server=$SERVER_ID ;";

		if ($adodb->Execute($q)) logquery($q);
	}

?>
