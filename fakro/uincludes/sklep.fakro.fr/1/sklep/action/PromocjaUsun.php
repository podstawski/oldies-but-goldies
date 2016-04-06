<?
	$killid = $FORM[delid];
	
	if (!strlen($killid)) return;

	$sql = "DELETE FROM promocja WHERE pm_id = $killid";

	$projdb->execute($sql);
	$action_id = $killid;
?>
