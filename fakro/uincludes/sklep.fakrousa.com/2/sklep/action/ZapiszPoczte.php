<?
	$poczta = $_REQUEST[POCZTA];

	if (strlen($poczta[id]))
	{
		$sql = "UPDATE poczta SET
				po_nazwa = '".$poczta[nazwa]."',
				po_cena_nt = ".$poczta[netto].",
				po_cena_br = ".$poczta[brutto].",
				po_darmo_powyzej = ".$poczta[powyzej]."				
				WHERE po_id = ".$poczta[id];
	}
	else
	{
		$sql = "INSERT INTO poczta (
				po_nazwa,
				po_cena_nt,
				po_cena_br,
				po_darmo_powyzej				
				) VALUES (
				'".$poczta[nazwa]."',
				".$poczta[netto].",
				".$poczta[brutto].",
				".$poczta[powyzej]."				
				)";
	}

	$adodb->execute($sql);
?>
