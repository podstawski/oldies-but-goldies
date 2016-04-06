<?
	$killid = $FORM[killRabatId];

	if (strlen($killid))
	{
		$sql = "DELETE FROM rabat_ilosciowy WHERE ri_id = $killid";
		$projdb->execute($sql);
		$action_id = $killid;
	}

?>
