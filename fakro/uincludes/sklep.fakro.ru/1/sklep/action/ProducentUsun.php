<?
	$killkat = $FORM[delid];
	$action_id = $FORM[delid];
	if (strlen($killkat))
	{
		$sql = "DELETE FROM producent
				WHERE pr_id = $killkat";
		$projdb->execute($sql);
	}
?>
