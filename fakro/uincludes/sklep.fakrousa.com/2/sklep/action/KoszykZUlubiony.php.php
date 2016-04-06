<?

	$_error_log = sysmsg("Following articles cant be added to cart:","system");
	$is_error = 0;


	$query="SELECT ul_to_id,ul_ilosc FROM ulubione WHERE ul_nazwa='$LIST[nazwa]' AND ul_su_id=$AUTH[id]";

	$ul_res=$projdb->Execute($query);
	for ($ul=0;$ul<$ul_res->RecordCount();$ul++)
	{
		parse_str(ado_ExplodeName($ul_res,$ul));

		$towar_id = $ul_to_id;
		$quantity = $ul_ilosc;

		$sql = "SELECT * FROM towar_sklep WHERE ts_to_id = $towar_id AND ts_sk_id = $SKLEP_ID";
		parse_str(ado_query2url($sql));

		if (!strlen($ts_id)) return;
			
		if (!$ts_kwant_zam) $ts_kwant_zam = $WM->kwant_towaru($towar_id);
		$kwant = $ts_kwant_zam;
		$ilosc_kwantow = ceil($quantity / $kwant);
		$quantity = $ilosc_kwantow * $kwant;
		if ($SYSTEM[mag]) $dostepne = $WM->dostep_magazynu($towar_id);

		if ($SYSTEM[mag] && $dostepne < $quantity) 
		{
			$sql = "INSERT INTO nieudane_zakupy (nz_ts_id,nz_su_id,nz_data,nz_proba,nz_dostepne)
					VALUES ($ts_id,".$AUTH[id].",$NOW,$quantity,$dostepne)";
			$adodb->execute($sql);
			$sql = "SELECT to_nazwa, to_indeks FROM towar WHERE to_id = $towar_id";
			parse_str(ado_query2url($sql));
			$_error_log.= "\n $to_nazwa ($to_indeks)";
			$is_error = 1;
			continue;
		}

		if (!$ts_czas_koszyk)
			$deadline = $NOW+$WM->towar_czas_zycia($towar_id);	
		else
			$deadline = $NOW+$ts_czas_koszyk;

		if (!$SYSTEM[mag]) $deadline="NULL";

		$sql = "SELECT COUNT(*) AS jest FROM koszyk WHERE ko_su_id = ".$AUTH[id]." AND ko_ts_id = $ts_id";
		parse_str(ado_query2url($sql));

		if (!$jest)
		{
			$sql = "INSERT INTO koszyk (ko_su_id,ko_ts_id,ko_ilosc, ko_deadline)
					VALUES (".$AUTH[id].",$ts_id,$quantity,$deadline)";
		}
		else
		{
			$sql = "UPDATE koszyk SET ko_ilosc = ko_ilosc + $quantity
					WHERE ko_ts_id = $ts_id AND ko_su_id = ".$AUTH[id];
		}

		$adodb->execute($sql);
	}

	if ($is_error)
	{
		$WM->sysinfo=$_error_log;
	}
?>
