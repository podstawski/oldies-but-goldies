<?
	module_select($MODULES->crm->files->customer_master);
	if (module_delete($MODULES->crm->files->customer_master))
	{
		$query="DELETE FROM crm_recent WHERE cr_file_id ='customer_master' 
				AND cr_id=$CUSTOMER[c_id]
				AND cr_server=$SERVER_ID";

		$adodb->Execute($query);
		$force_allow=1;
		$action="UsunStrone";
	}

?>