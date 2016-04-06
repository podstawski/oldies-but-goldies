<?
	if (!$email) 
	{
		$error="&nbsp;";
		return;
	}
	$status=sysmsg("Missing email in database","system");
	
	if ($su_id) $status=sysmsg("Email exists in database","system");
	if ($su_data_dodania) $data_dodania_data=date("d-m-Y",$su_data_dodania);
	if ($su_data_dodania) $data_dodania_godz=date("H:i",$su_data_dodania);	

?>
