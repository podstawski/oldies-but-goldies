<?
	$killId = $FORM[id];

	if (!strlen($killId)) return;

	$sql = "DELETE FROM poczta WHERE po_id = $killId";
	$adodb->execute($sql);
?>
