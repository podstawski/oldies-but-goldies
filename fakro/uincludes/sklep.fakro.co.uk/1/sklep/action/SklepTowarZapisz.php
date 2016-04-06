<?
	$SKLEPY = explode(";",$SKLEPY);
	$sql = "";
	for ($i = 0; $i < count($SKLEPY); $i++)
	{
		$val = $SKLEPY[$i];

		$SKLEP_KW[$val] = toFloat($SKLEP_KW[$val]);
		$SKLEP_CE[$val] = toFloat($SKLEP_CE[$val]);
		$SKLEP_CZ[$val] = toFloat($SKLEP_CZ[$val]);
		$SKLEP_PRI[$val] = $SKLEP_PRI[$val]+0;
		if (!$SKLEP_PRI[$val]) $SKLEP_PRI[$val]='NULL';
		if (!$SKLEP_PRI2[$val]) $SKLEP_PRI2[$val]='NULL';
		if (!strlen($SKLEP_MG[$val])) $SKLEP_MG[$val] = 0; 

		$sql.= "UPDATE towar_sklep SET 
				ts_kwant_zam = ".$SKLEP_KW[$val].",
				ts_czas_koszyk = ".$SKLEP_CZ[$val].",
				ts_cena = ".$SKLEP_CE[$val].",
				ts_pri = ".$SKLEP_PRI[$val].",
				ts_pri2 = ".$SKLEP_PRI2[$val].",
				ts_magazyn = ".$SKLEP_MG[$val]."
				WHERE ts_id = $val;";

		$action_id=$val;

	}

	$adodb->execute($sql);
?>
