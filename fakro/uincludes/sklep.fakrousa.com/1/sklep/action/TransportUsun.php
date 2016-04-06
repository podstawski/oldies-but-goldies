<?
	$killId = $FORM[id];

	if (!strlen($killId)) return;

	$sql = "DELETE FROM tr_typ WHERE tr_typ_id = $killId";
	$adodb->execute($sql);
?>
