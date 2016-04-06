<?
	$towar_id = $FORM[towar_id];
	$quantity = $FORM[quantity];

	if (!strlen($AUTH[id]) || !strlen($towar_id) || !strlen($quantity)) return;

	$action_id=$towar_id;

	$adodb->debug=0;

	$sql = "SELECT * FROM towar_sklep WHERE ts_to_id = $towar_id AND ts_sk_id = $SKLEP_ID ";
	parse_str(ado_query2url($sql));

	if (!strlen($ts_id)) return;
	
	if (!$ts_kwant_zam) $ts_kwant_zam = $WM->kwant_towaru($towar_id);
	if ($ts_kwant_zam != 0 && strlen($ts_kwant_zam))
	{
		$kwant = $ts_kwant_zam;
		$ilosc_kwantow = ceil($quantity / $kwant);
		$quantity = $ilosc_kwantow * $kwant;
	}

	if ($SYSTEM[mag]) $dostepne = $WM->dostep_magazynu($towar_id);

	if ($SYSTEM[mag] && $dostepne < $quantity) 
	{
		$sql = "INSERT INTO nieudane_zakupy (nz_ts_id,nz_su_id,nz_data,nz_proba,nz_dostepne)
				VALUES ($ts_id,".$AUTH[id].",$NOW,$quantity,$dostepne)";
		$adodb->execute($sql);
		echo "
		<script>
			alert('".sysmsg("Not enough articles","cart")."');
		</script>
		";
		return;
	}

	if (!$ts_czas_koszyk)
		$deadline = $NOW+$WM->towar_czas_zycia($towar_id);	
	else
		$deadline = $NOW+$ts_czas_koszyk;

	if (!$SYSTEM[czas]) $deadline="NULL";

	$sql = "SELECT ko_id AS jest,ko_ilosc FROM koszyk 
			WHERE ko_su_id = ".$AUTH[id]." AND ko_ts_id = $ts_id";
	parse_str(ado_query2url($sql));

	if (!$jest)
	{
		/* sprawdzenie czy jest bon */
    $voucher_id = 'NULL';
    $sql = "SELECT ko_rez_uwagi AS voucher_id FROM koszyk WHERE ko_su_id = '".$AUTH['id']."' AND ko_rez_data IS NULL ORDER BY ko_rez_uwagi ASC LIMIT 1";
    parse_str(ado_query2url($sql));
    
    if(!$voucher_id) {
      $voucher_id = 'NULL';
    }
  
    $sql = "INSERT INTO koszyk (ko_su_id,ko_ts_id,ko_ilosc, ko_deadline, ko_rez_uwagi, ko_opcje)
        VALUES (".$AUTH[id].",$ts_id,$quantity,$deadline,".$voucher_id.",".$AUTH['id'].")";
    $adodb->execute($sql);
    
//    $sql = "INSERT INTO koszyk (ko_su_id,ko_ts_id,ko_ilosc, ko_deadline, ko_rez_uwagi, ko_opcje)
//        VALUES (".$AUTH[id].",$ts_id,$quantity,$deadline,NULL,".$AUTH['id'].")";
//    $adodb->execute($sql);

		if (!$SYSTEM[koszyk]) $adodb->sysinfo=sysmsg("Article added to offer","cart");
	}
	else
	{
		$sql = "UPDATE koszyk SET ko_ilosc = ko_ilosc + $quantity, ko_deadline=$deadline
				WHERE ko_ts_id = $ts_id AND ko_su_id = ".$AUTH[id];
		if ($ko_ilosc!=$quantity) $adodb->execute($sql);
		
	}

	if (!$ts_aktywny)
	{
		$MAX=0;
		$query="SELECT ts_to_id,ko_ilosc FROM koszyk 
				LEFT JOIN towar_sklep ON ko_ts_id = ts_id AND ts_sk_id = $SKLEP_ID
				WHERE ko_su_id=".$AUTH[id]." AND ts_aktywny>0";
		$res=$adodb->execute($query);
		for ($i=0;$i<$res->recordCount();$i++)
		{
			parse_str(ado_explodename($res,$i));
			$pow=$WM->towary_powiazane($ts_to_id);
			if ($pow==$towar_id) $MAX+=$ko_ilosc;
		}
		if ($quantity>$MAX) $quantity=$MAX;

		$sql = "UPDATE koszyk SET ko_ilosc = $quantity 
				WHERE ko_su_id = ".$AUTH[id]." AND ko_ts_id = $ts_id
				AND ko_ilosc>$quantity";
		$adodb->execute($sql);

	}



	//SZUKAMY UKRYTYCH TOWARÓW W PROMOCJACH POWIĽZANYCH Z DODAWANYM

	$powiazane = $WM->towary_powiazane($towar_id);
//	echo "= $powiazane =";
	$adodb->debug=0;
	if (strlen($powiazane) && $ts_aktywny)
	{
		$powiazane = explode(",",$powiazane);
		for ($i=0; $i < count($powiazane); $i++)
		{
			$sql = "SELECT * FROM towar_sklep WHERE ts_to_id = ".$powiazane[$i];
			parse_str(ado_query2url($sql));
			if (!strlen($ts_id)) return;

			$sql = "SELECT ko_id AS jest,ko_ilosc FROM koszyk 
					WHERE ko_su_id = ".$AUTH[id]." AND ko_ts_id = $ts_id";
			parse_str(ado_query2url($sql));

			if (!$jest)
			{
				$sql = "INSERT INTO koszyk (ko_su_id,ko_ts_id,ko_ilosc, ko_deadline)
						VALUES (".$AUTH[id].",$ts_id,$quantity,$deadline)";
				$adodb->execute($sql);
			}
			else
			{
				$sql = "UPDATE koszyk SET ko_ilosc = $quantity, ko_deadline=$deadline
						WHERE ko_ts_id = $ts_id AND ko_su_id = ".$AUTH[id];
				$adodb->execute($sql);
				
			}
		}
	}
	$adodb->debug=0;
	//ENDOF SZUKAMY UKRYTYCH TOWARÓW W PROMOCJACH POWIĽZANYCH Z DODAWANYM

	
?>
