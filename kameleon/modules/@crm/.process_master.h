<?
	module_select($MODULES->crm->files->proc_master);


	if ( $KAMELEON[username] != $PROC[p_author])
	{
		echo label("You are not an author of the process");
		return;
	}

	if (!strlen($PROC[p_d_start])) $PROC[p_d_start]=$PROC[p_d_create];

	_RevertDate($PROC[p_d_create]);
	_RevertDate($PROC[p_d_start]);
	_RevertDate($PROC[p_d_deadline]);
	_RevertDate($PROC[p_d_end]);

	
	$query="SELECT c_id,c_person FROM crm_customer 
			WHERE c_server=$SERVER_ID
			AND c_parent=$PROC[p_customer]
			ORDER BY c_person";


	$person_res=$adodb->Execute($query);
	$person_count=0;
	if($person_res) $person_count=$person_res->RecordCount();
	$proc_persons="";
	for ($p=0;$p<$person_count;$p++)
	{
		parse_str(ado_ExplodeName($person_res,$p));

		$sel=($c_id==$PROC[p_subcustomer])?" selected":"";
		$proc_persons.="<option$sel value=\"$c_id\">$c_person</option>";
	}

	$PROC[p_subcustomer_options]=$proc_persons;

	_display_form($MODULES->crm->files->proc_master);
?>
