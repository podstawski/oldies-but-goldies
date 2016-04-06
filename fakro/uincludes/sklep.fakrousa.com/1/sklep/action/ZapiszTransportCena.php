<?
	$transport = $_REQUEST[TRANSPORT];

	if (strlen($transport[tr_ceny_id]))
	{
		$sql = "UPDATE tr_ceny SET
				tr_typ_id = '".$transport[tr_typ_id]."',
				tr_strefa_typ = '".$transport[tr_strefa_typ]."',
				tr_waga_od = '".$transport[tr_waga_od]."',
				tr_waga_do = '".$transport[tr_waga_do]."',
				tr_objetosc_od = '".$transport[tr_objetosc_od]."',
				tr_objetosc_do = '".$transport[tr_objetosc_do]."',
				tr_ceny = '".$transport[tr_ceny]."'
				WHERE tr_ceny_id = ".$transport[tr_ceny_id];
	}
	else
	{
		$sql = "INSERT INTO tr_ceny (
				tr_typ_id,
				tr_strefa_typ,
				tr_waga_od,
				tr_waga_do,
				tr_objetosc_od,
				tr_objetosc_do,
				tr_ceny
				) VALUES (
				'".$transport[tr_typ_id]."',
				'".$transport[tr_strefa_typ]."',
				'".$transport[tr_waga_od]."',
				'".$transport[tr_waga_do]."',
				'".$transport[tr_objetosc_od]."',
				'".$transport[tr_objetosc_do]."',
				'".$transport[tr_ceny]."'
				)";
	}

	$adodb->execute($sql);
?>
