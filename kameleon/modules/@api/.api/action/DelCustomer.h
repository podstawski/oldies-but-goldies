<?
	$id=explode(":",base64_decode($AUTH[id]));

	$query="DELETE FROM crm_customer WHERE c_server=$SERVER_ID AND c_id=$id[0] AND oid=$id[1]";

	$adodb->Execute($query);	
?>
