<?

	if (!is_array($obj->magazyn->towar_sklep))
		$obj->magazyn->towar_sklep = array($obj->magazyn->towar_sklep);

	
	$ts_total=0;
	$ts_count=0;
	$ts_count_aktywny=0;
	$ts_count_magazyn=0;

	foreach ($obj->magazyn->towar_sklep AS $ts)
	{
		$ts_id=$ts->ts_id+0;
		$ts_total++;

		if (!$ts_id)
		{
			$indeks=addslashes(stripslashes($ts->to_indeks));
			$query="SELECT ts_id FROM towar,towar_sklep 
					WHERE ts_to_id=to_id 
					AND to_indeks='$indeks'
					AND ts_sk_id=$SKLEP_ID";
			parse_str(ado_query2url($query));
		}
		
		if (!$ts_id) continue;
		$ts_count++;

		$set="";
		if (strlen($ts->ts_aktywny))
		{
			if ($ts->ts_aktywny) $ts_count_aktywny++;
			$set.='ts_aktywny='.$ts->ts_aktywny;
		}
		if (strlen($ts->ts_cena))
		{
			if (strlen($set)) $set.=',';
			$set.='ts_cena='.tofloat($ts->ts_cena);
		}

		if (strlen($ts->ts_magazyn))
		{
			if ($ts->ts_magazyn) $ts_count_magazyn++;
			if (strlen($set)) $set.=',';
			$set.='ts_magazyn='.$ts->ts_magazyn;
		}

		if (strlen($ts->ts_kwant_zam))
		{
			if (strlen($set)) $set.=',';
			$set.='ts_kwant_zam='.toFloat($ts->ts_kwant_zam);
		}

		if (strlen($ts->ts_czas_koszyk))
		{
			if (strlen($set)) $set.=',';
			$set.='ts_czas_koszyk='.$ts->ts_czas_koszyk+0;
		}

		if (!strlen($set)) continue;
		$query="UPDATE towar_sklep SET $set WHERE ts_id=$ts_id";
		$adodb->execute($query);

		//echo "$query <br>";
	}
	
	echo "<b>";
	echo "Razem $ts_total pozycji.<br>";
	echo "Znaleziono w sklepie $ts_count indeksów.<br>";
	if ($ts_count_aktywny) echo "Aktywnych $ts_count_aktywny pozycji.<br>";
	if ($ts_count_magazyn) echo "Dostępnych $ts_count_magazyn pozycji.<br>";
	echo "</b>";
?>
