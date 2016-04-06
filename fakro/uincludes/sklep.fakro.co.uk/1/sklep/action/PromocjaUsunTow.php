<?
	$killid = $FORM[pt_id];
	$action_id = $killid;
	if (!strlen($killid)) return;

	$sql= "DELETE FROM promocja_towaru WHERE pt_id = $killid";

	$adodb->execute($sql);
	
?>
