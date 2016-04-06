<?
	$transport = $_REQUEST[TRANSPORT];

	if (strlen($transport[tr_strefa_id]))
	{
		$sql = "UPDATE tr_strefa SET
				tr_strefa_typ = '".$transport[tr_strefa_typ]."',
				tr_strefa_name = '".$transport[tr_strefa_name]."',
				tr_strefa_opis = '".$transport[tr_strefa_opis]."',
				tr_strefa_vat = '".$transport[tr_strefa_vat]."'
				WHERE tr_strefa_id = ".$transport[tr_strefa_id];
	}
	else
	{
		$sql = "INSERT INTO tr_strefa (
				tr_strefa_typ,
				tr_strefa_name,
				tr_strefa_opis,
				tr_strefa_vat
				) VALUES (
				'".$transport[tr_strefa_typ]."',
				'".$transport[tr_strefa_name]."',
				'".$transport[tr_strefa_opis]."',
				'".$transport[tr_strefa_vat]."'
				)";
	}

	$adodb->execute($sql);
?>
