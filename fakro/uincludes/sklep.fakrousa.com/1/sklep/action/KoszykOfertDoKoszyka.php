<?
	$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];
	if (!is_array($KOSZYK_OFERT) || !strlen($AUTH[id]) ) return;
	

	$post_sql='';
	$_error_log = sysmsg("Following articles cant be added to cart:","system");
	$is_error = 0;
	while(list($key,$val) = each($KOSZYK_OFERT))
		if ($val)
		{
			$towar_id = $key;
			$quantity = $val;

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

			/*
			//SzUKAMY UKRYTYCH TOWARÓW W PROMOCJACH POWI¥ZANYCH Z DODAWANYM


			
			$powiazane = $WM->towary_powiazane($towar_id);
			if (strlen($powiazane) && $ts_aktywny )
			{
				$powiazane = explode(",",$powiazane);
				for ($i=0; $i < count($powiazane); $i++)
				{
					if ($_COOKIE[auto_to_id][$powiazane[$i]]) continue;
					
					$sql = "SELECT * FROM towar_sklep WHERE ts_to_id = ".$powiazane[$i];
					parse_str(ado_query2url($sql));
					if (!strlen($ts_id)) continue;

					if (in_array($powiazane[$i],array_keys($KOSZYK_OFERT)))
					{
						$post_sql.="UPDATE koszyk SET ko_ilosc=$quantity, ko_deadline=$deadline
									WHERE ko_ts_id = $ts_id AND ko_su_id = ".$AUTH[id].";";
					}


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
						$sql = "UPDATE koszyk SET ko_ilosc = ko_ilosc + $quantity, ko_deadline=$deadline
								WHERE ko_ts_id = $ts_id AND ko_su_id = ".$AUTH[id];
						$adodb->execute($sql);
						
					}
				}
			}

			//ENDOF SZUKAMY UKRYTYCH TOWARÓW W PROMOCJACH POWI¥ZANYCH Z DODAWANYM
			*/
		}

		if (strlen($post_sql)) $adodb->execute($post_sql);


		if ($is_error)
		{
			echo "
			<script>
				alert('$_error_log');
			</script>
			";
		}

?>