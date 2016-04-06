<?
	$killid = $FORM[killRabatId];

	if (strlen($killid))
	{
		$sql = "DELETE FROM rabat_kontrahenta WHERE rk_id = $killid";
		$projdb->execute($sql);
		$action_id = $killid;
	}

?>
