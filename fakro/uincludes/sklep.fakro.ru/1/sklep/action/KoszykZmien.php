<?
	$article_id = $FORM[article_id];
	$quantity = $FORM[quantity];

	$action_id=$article_id;

	if (!strlen($article_id) || !strlen($quantity)) return;
	if ($AUTH[id] <= 0 || !strlen($AUTH[id])) return;
	$quantity+=0;

	$sql = "SELECT ts_id, ts_to_id AS towar_id,ts_aktywny 
			FROM koszyk LEFT JOIN towar_sklep ON ko_ts_id = ts_id AND ts_sk_id = $SKLEP_ID 
			WHERE ko_id = $article_id 
			";
	

	parse_str(ado_query2url($sql));
	if (!$towar_id) return;

	$kwant = $WM->kwant_towaru($towar_id);

	if ($kwant != 0 && strlen($kwant))
	{
		$ilosc_kwantow = ceil($quantity / $kwant);
		$quantity = $ilosc_kwantow * $kwant;
	}

	if ($SYSTEM[mag]) $dostepne = $WM->dostep_magazynu($towar_id,$article_id);

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
	}

	$sql = "UPDATE koszyk SET ko_ilosc = $quantity 
			WHERE ko_su_id = ".$AUTH[id]." AND ko_id = $article_id";
	$adodb->execute($sql);

	//SUKAMY UKRYTYCH TOWARгW W PROMOCJACH POWIЅZANYCH Z DODAWANYM

	$sql = "SELECT ts_to_id FROM koszyk, towar_sklep 
			WHERE ko_id = $article_id AND ko_ts_id = ts_id";
	parse_str(ado_query2url($sql));

	$powiazane = $WM->towary_powiazane($ts_to_id);
	if (strlen($powiazane))
	{
		$powiazane = explode(",",$powiazane);
		for ($i=0; $i < count($powiazane); $i++)
		{
			$sql = "SELECT ko_id FROM koszyk, towar_sklep 
					WHERE ko_ts_id = ts_id 
					AND ts_to_id = ".$powiazane[$i];
			parse_str(ado_query2url($sql));

			if (!strlen($ko_id)) continue;

			$sql = "UPDATE koszyk SET ko_ilosc = $quantity 
					WHERE ko_su_id = ".$AUTH[id]." AND ko_id = $ko_id";
			$adodb->execute($sql);
				
		}
	}

	//ENDOF SZUKAMY UKRYTYCH TOWARгW W PROMOCJACH POWIЅZANYCH Z DODAWANYM
?>
