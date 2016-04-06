<?
	if (!strlen($FORM[adr_id])) return;
		
	$sql = "DELETE FROM adresy WHERE ad_id = ".$FORM[adr_id];

	$projdb->execute($sql);
?>
