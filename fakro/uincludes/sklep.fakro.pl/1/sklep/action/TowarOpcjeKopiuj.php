<?
	$cp = $FORM[copyfrom];
	$main_id = $FORM[id];
	if (!strlen($cp)) return;

	$sql = "SELECT to_id FROM towar WHERE to_indeks = '$cp'";
	parse_str(ado_query2url($sql));

	if (!strlen($to_id)) return;

	$sql = "SELECT ot_ilosc, ot_opcje FROM opcje_towaru WHERE ot_to_id = $to_id";
	parse_str(ado_query2url($sql));

	if (!$ot_ilosc) return;

	$sql = "DELETE FROM opcje_towaru WHERE ot_to_id = $main_id";
	$adodb->execute($sql);

	$sql = "INSERT INTO opcje_towaru (ot_to_id, ot_opcje, ot_ilosc)
			VALUES ($main_id, '$ot_opcje', $ot_ilosc)";
	$adodb->execute($sql);
	$action_id = $main_id;


?>
