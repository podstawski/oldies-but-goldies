<?
	$killId = $FORM[id];

	if (!strlen($killId)) return;

	$sql = "DELETE FROM tr_ceny WHERE tr_ceny_id = $killId";
	$adodb->execute($sql);
?>
