<?
	$towar="";
	$ws_action="$SOAP_PATH/$action.h";
	include("$SKLEP_INCLUDE_PATH/admin/ws_action.php");

	if (strlen($error)) return;

	if (!is_array($towar)) return;

	$towar = txt_addslash($towar);
//	echo "<h2>Towar</h2><pre>";print_r($towar); echo "</pre>";	


	$query="SELECT to_id,to_indeks FROM towar";
	$res=$projdb->execute($query);
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$to_ids[$to_indeks]=$to_id;
	}

	foreach($towar AS $obj)
	{	
		$to_id=$to_ids[$obj[to_indeks]];
		

		if (!$to_id)
		{
			$sql = "INSERT INTO towar (to_indeks) VALUES('".$obj[to_indeks]."');
					SELECT MAX(to_id) AS to_id FROM towar";
			parse_str(ado_query2url($sql));
		}
		if (!$to_id) continue;

		$obj[to_ws_update]=$NOW;
		$WM->update_towar($to_id,$obj);
	}

?>
