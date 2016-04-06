<?
	$indx = $FORM[dodaj];
	$rabat = $FORM[rabat];
	$pm_id = $FORM[pm_id];
	$pm_koniec = toFloat($FORM[pm_koniec]);
	$pm_poczatek = toFloat($FORM[pm_poczatek]);
	$action_id = $pm_id;

	if (!strlen($indx)) return;
	$sql = "SELECT to_id,ts_cena,ts_id FROM towar, towar_sklep 
			WHERE to_indeks = '$indx' 
			AND to_id = ts_to_id
			AND ts_sk_id = $SKLEP_ID";

	parse_str(ado_query2url($sql));

	if (!strlen($to_id)) return;

	$sql = "SELECT COUNT(pt_id) AS jest FROM promocja_towaru, towar_sklep
			WHERE pt_ts_id = ts_id AND ts_to_id = $to_id AND pt_pm_id = $pm_id";

	parse_str(ado_query2url($sql));

	if ($jest) return;
	
	if (strlen($ts_cena) && strlen($rabat))
	{
		$rabat = toFloat($rabat);
		$proc = round((100 - $rabat) / 100,2);
		$ts_cena = $ts_cena*$proc;
	}
	else $ts_cena = "NULL";
	
	$sql = "INSERT INTO promocja_towaru (pt_ts_id, pt_pm_id, pt_cena, pt_poczatek, pt_koniec)	
			VALUES ($ts_id, $pm_id, $ts_cena, $pm_poczatek, $pm_koniec)";

	$adodb->execute($sql);

?>
