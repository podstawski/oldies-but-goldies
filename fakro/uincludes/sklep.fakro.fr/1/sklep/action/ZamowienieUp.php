<?
	switch ($FORM[acc_status])
	{
		case 0: $new_stat = 1; break;
		case 1: $new_stat = -1; break;
		case -5: $new_stat = 0; break;
	}

	if (strlen($FORM[new_status])) $new_stat=$FORM[new_status];

	if ($FORM[acc_status]>0 && $new_stat<0) $WM->ruch_mag_zam($FORM[accept_id],"Realizacja zamowienia",-1);	
	if ($FORM[acc_status]<0 && $new_stat>0) $WM->ruch_mag_zam($FORM[accept_id],"Anulowanie realizacji zamowienia",1);	

	include("$SKLEP_INCLUDE_PATH/action/ZamowienieUpDownSwitch.php");

	$sql = "UPDATE zamowienia SET za_status = $new_stat $date_up $kom WHERE za_id = ".$FORM[accept_id];
	$adodb->execute($sql);

	$action_id=$FORM[accept_id];

	//echo $sql;
?>