<?
	if ($AUTH[id] <= 0 || !strlen($FORM[order_number]) || !strlen($AUTH[parent])) return;

	$adodb->BeginTrans();

	$subsql = "SELECT MAX(za_numer)+1 AS maxnumer FROM zamowienia WHERE za_su_id = ".$AUTH[parent];
	parse_str(ado_query2url($subsql));	
	if (!strlen($maxnumer)) $maxnumer=1;

	$pram_string = "osoba_id=".$AUTH[id]."&osoba=".urlencode($AUTH[imiona])."+".urlencode($AUTH[nazwisko])."&platnosc=".urlencode(sysmsg($AUTH[platnosc],"order"))."&dostawa=".urlencode(sysmsg($AUTH[dostawa],"order"));

	$sql = "INSERT INTO zamowienia (za_su_id,za_numer,za_numer_obcy,za_uwagi,za_status,za_data,za_adres,za_parametry,za_sk_id,za_osoba)
			VALUES (".$AUTH[parent].",$maxnumer,'".$FORM[order_number]."','".$FORM[uwagi]."',0,$NOW,'".$FORM[dostawa]."','$pram_string',$SKLEP_ID,$AUTH[id]);
			SELECT MAX(za_id) AS zamowienie_id FROM zamowienia";

	parse_str(ado_query2url($sql));

	$action_id=$zamowienie_id;

	$sql = "SELECT ko_ts_id, ko_ilosc FROM koszyk 
			WHERE ko_su_id = ".$AUTH[parent]." 
			AND ko_rez_data = ".$FORM[data]." AND (ko_deadline > $NOW OR ko_deadline IS NULL) ORDER BY ko_id";

	$res = $adodb->execute($sql);

	if (!$res->RecordCount())
	{
		$adodb->RollbackTrans();
		return;
	}

	$query = "";

	$suma_nt=0;$suma_br=0;

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$to_id = "";
		$sql = "SELECT to_id, to_indeks,to_vat FROM towar_sklep, towar WHERE
				ts_id = $ko_ts_id AND ts_to_id = to_id AND ts_sk_id = $SKLEP_ID";
		parse_str(ado_query2url($sql));
		if (!strlen($to_id)) continue;
		$cena = $WM->system_cena($to_id,$ko_ilosc,$AUTH[parent]);
		if ($WM->oryginalna_cena($to_id) != 0)
			$rabat=100*($WM->oryginalna_cena($to_id)-$cena)/$WM->oryginalna_cena($to_id);
		else
			$rabat=0;

		if (!strlen($cena)) $cena = "NULL";
		$query.= "INSERT INTO zampoz (zp_za_id,zp_ts_id,zp_ilosc,zp_cena,zp_to_indeks,zp_rabat)
				  VALUES ($zamowienie_id, $ko_ts_id, $ko_ilosc,$cena,'$to_indeks',$rabat);";

		$suma_nt+=$ko_ilosc*$cena;
		$suma_br+=round($ko_ilosc*$cena*(100+$to_vat))/100;
	}

	$query.= "DELETE FROM koszyk WHERE ko_su_id = ".$AUTH[parent]." 
			AND ko_rez_data = ".$FORM[data];

	$query.=";
			UPDATE zamowienia SET za_wart_nt=$suma_nt,za_wart_br=$suma_br WHERE za_id=$zamowienie_id;";

	if ($adodb->execute($query))
		$adodb->CommitTrans();
	else
		$adodb->RollbackTrans();


?>
