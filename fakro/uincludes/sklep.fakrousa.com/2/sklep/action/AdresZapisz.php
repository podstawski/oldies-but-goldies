<?
	
	if (!strlen($FORM[su_id])) return;

	if (!strlen($FORM[adr_id]))
	{
		$sql = "
		INSERT INTO adresy (
			ad_su_id,
			ad_adres
		)VALUES
		(
			".$FORM[su_id].",
			'".$FORM[adres]."'
		)
		";
	}
	else
	{
		$sql = "
		UPDATE adresy SET
		ad_adres = '".$FORM[adres]."'
		WHERE ad_id = ".$FORM[adr_id];
	}

	$projdb->execute($sql);
?>
