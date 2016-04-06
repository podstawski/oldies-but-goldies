<?
	if (!$za_id) return;

	$query="SELECT * FROM zamowienia WHERE za_id=$za_id";
	parse_str(ado_query2url($query));

	$hash2=crypt("$za_id-$za_data-$new_status",$hash);
	if ($hash!=$hash2) return;
	
	$query="SELECT su_parent FROM system_user WHERE su_id=".system_auth_user($AUTH);
	parse_str(ado_query2url($query));

	
	$sql="UPDATE zamowienia SET za_status=$new_status,za_data_przyjecia = $NOW ,za_data_status=$NOW
			WHERE za_id=$za_id AND za_su_id=$su_parent";
	if ($za_status==0) $projdb->execute($sql);
	
?>