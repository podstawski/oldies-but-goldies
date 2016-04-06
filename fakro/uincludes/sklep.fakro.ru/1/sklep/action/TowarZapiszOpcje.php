<?
	$to_id = $FORM[id];
	$opcje = $FORM[opcje];
	$action_id = $to_id;
	if (!strlen($to_id)) return;

	$sql = "DELETE FROM opcje_towaru WHERE ot_to_id = $to_id";
	$adodb->execute($sql);

	$total = count(explode("\n",$opcje));
	if (!strlen($total)) $total = 0;

	$sql = "INSERT INTO opcje_towaru (ot_to_id, ot_opcje, ot_ilosc)
			VALUES ($to_id, '$opcje', $total)";

	$adodb->execute($sql);
?>
