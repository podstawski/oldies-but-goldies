<?
	$killId = $FORM[id];

	if (!strlen($killId)) return;

	$sql = "DELETE FROM tr_strefa WHERE tr_strefa_id = $killId";
	$adodb->execute($sql);
?>
