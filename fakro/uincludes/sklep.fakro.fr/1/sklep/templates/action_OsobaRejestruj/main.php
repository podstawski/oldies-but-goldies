<?
	$za_pyt=stripslashes($FORM[za_pyt]);

	$sql="SELECT su_nazwisko AS master,su_email AS master_email 
			FROM system_user WHERE su_id=".$SYSTEM[master]; 
	parse_str(ado_query2url($sql));

?>
