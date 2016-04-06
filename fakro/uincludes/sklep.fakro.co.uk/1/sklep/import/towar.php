<?
	//$projdb->debug=1;
	//$adodb->debug=1;

	if (!function_exists('kategoria'))
	{
		function kategoria($k)
		{
			$KAT=explode("|",$k);



			for ($k=0; $k< count($KAT); $k++)
			{
				$ka_id = "";
				$nazwa=addslashes(stripslashes($KAT[$k]));
				$sql = "SELECT ka_id FROM kategorie WHERE ka_nazwa = '$nazwa'";
				$add_sql = "";
				if (strlen($_last_parent))
					$sql.= " AND ka_parent = $_last_parent";
				else
					$sql.= " AND ka_parent IS NULL";
				

				parse_str(ado_query2url($sql));

				if (!$ka_id)
				{	
					$parent=$_last_parent?$_last_parent:"NULL";

					$query="INSERT INTO kategorie (ka_nazwa,ka_parent) VALUES ('$nazwa',$parent);
							SELECT max(ka_id) AS _last_parent FROM kategorie";
					parse_str(ado_query2url($query));
				}
				else $_last_parent=$ka_id;
			}

			return ($_last_parent);
		}
	}

	if (!is_array($obj->magazyn->towar) && is_object($obj->magazyn->towar)) $obj->magazyn->towar = array($obj->magazyn->towar);
	if (!is_array($obj->magazyn->towar)) return;


	


	$razem=0;
	$dodano=0;
	$cz_start=time();

	foreach ($obj->magazyn->towar AS $_towar)
	{
		if (is_array($_towar)) reset($_towar);

		$kat_val = $_towar->kategoria;
		if (!is_array($kat_val)) $kat_val=array($kat_val);

		$KID=array();
		for ($k=0; $k< count($kat_val); $k++) 
		{
			$_k=kategoria($kat_val[$k]);
			if ($_k) $KID[]=$_k;
		}

		$_last_towar = "";
		$top_sql = "";
		$bottom_sql = "";
		$sklep_sql = "";
		$set_sql = "";

		while (list($key,$val) = each($_towar))
		{
			if (is_object($val) || is_array($val)) continue;
			if (!strlen($val)) continue;
			if ($key == "linia") continue;
			if ($key=="kategoria") continue;
			if ($key=="kwant") 
			{
				$sklep_sql.=",ts_kwant_zam=".toFloat($val);
				continue;
			}
			if ($key=="czas") 
			{
				$sklep_sql.=",ts_czas_koszyk=".toFloat($val);
				continue;
			}
			if (substr($key,0,3)=="ts_") 
			{
				$sklep_sql.=",$key=".toFloat($val);
				continue;
			}
			$top_sql.= ",to_$key";
			$bottom_sql.= ",'".addslashes(stripslashes($val))."'";
			$set_sql.=",to_$key='".addslashes(stripslashes($val))."'";
		}
		$top_sql = substr($top_sql,1);
		$bottom_sql = substr($bottom_sql,1);
		
		$to_id = "";
		$sql = "SELECT to_id FROM towar WHERE to_indeks = '".addslashes(stripslashes($_towar->indeks))."' LIMIT 1";
		$res = $projdb->execute($sql);
		if (is_object($res))
			@parse_str(ado_explodename($res,0));
		else
			echo "Blad (linia $_towar->linia) - ".nl2br($sql)."<hr>";

		if (!strlen($to_id))
		{
			$sql="SELECT nextval('public.towar_to_id_seq'::text) AS to_id";
			parse_str(ado_query2url($sql));

			$sql = "INSERT INTO towar (to_id,$top_sql) VALUES ($to_id,$bottom_sql);";

			$res = $projdb->execute($sql);
			if (!is_object($res)) echo "Blad (linia $_towar->linia) - ".nl2br($sql)."<hr>";
			$_last_towar = $to_id;
			$dodano++;
		}
		else
		{
			$set_sql=substr($set_sql,1);
			$sql = "UPDATE towar SET $set_sql WHERE to_id=$to_id";
			if (strlen($set_sql)) $res = $projdb->execute($sql);
		}

		$_last_towar = $to_id;

		if (!$to_id) continue;
		$ts_id="";

		$sql="SELECT ts_id FROM towar_sklep WHERE ts_to_id=$to_id AND ts_sk_id=$SKLEP_ID";
		parse_str(ado_query2url($sql));

		if (!strlen($ts_id))
		{
			$sql="SELECT nextval('public.towar_sklep_ts_id_seq'::text) AS ts_id";
			parse_str(ado_query2url($sql));
			$sql="INSERT INTO towar_sklep (ts_id,ts_to_id,ts_sk_id,ts_aktywny) VALUES($ts_id,$to_id,$SKLEP_ID,0)";
			parse_str(ado_query2url($sql));
		}

		if (strlen($sklep_sql))
		{
			$sql="UPDATE towar_sklep SET ts_aktywny=1 $sklep_sql WHERE ts_id=$ts_id";
			$projdb->execute($sql);
		}

		if (is_array($KID) && count($KID))
		{
			$sql = "DELETE FROM towar_kategoria WHERE tk_to_id = $_last_towar AND tk_ka_id NOT IN (".implode(',',$KID).")";
			if (!$projdb->execute($sql))
				echo "Blad (linia $_towar->linia)- ".nl2br($sql)."<hr>";

			foreach($KID AS $ka_id)
			{
				$sql = "INSERT INTO towar_kategoria (tk_to_id,tk_ka_id) 
						SELECT $_last_towar,$ka_id 
						WHERE 1 NOT IN (SELECT count(*)  FROM towar_kategoria WHERE tk_to_id=$_last_towar AND tk_ka_id=$ka_id);";

				$res = $projdb->execute($sql);
			}
		}


		$sql = "DELETE FROM towar_parametry WHERE tp_to_id = $_last_towar";
		 if (!$projdb->execute($sql)) echo "Blad (linia $_towar->linia)- ".nl2br($sql)."<hr>";


		if (is_object($_towar->parametry))
		{

			$top_sql = "";
			$bottom_sql = "";
			while (list($tow_key,$tow_val) = each($_towar->parametry))
			{
				if (is_object($val) || is_array($val)) continue;
				if (!strlen(trim($tow_val))) continue;
				if ($tow_key == "linia") continue;
				$top_sql.= ",tp_$tow_key";
				if (trim($tow_key) != "gatunek" && trim($tow_key) != "stan")
					$bottom_sql.= ",$tow_val";
				else
					$bottom_sql.= ",'".addslashes(stripslashes($tow_val))."'";
			}
			$top_sql = substr($top_sql,1);
			$bottom_sql = substr($bottom_sql,1);
			$sql = "INSERT INTO towar_parametry (tp_to_id,$top_sql) VALUES ($_last_towar,$bottom_sql)";
			if (!$projdb->execute($sql)) echo "Blad (linia $_towar->linia) - ".nl2br($sql)."<hr>";
		}

		$razem++;
		if (!($razem%100) || $razem==1)
		{
			$txt="";
			if ($LINIA["od"]) $txt.="Start w linii: ".number_format($LINIA["od"],0," "," ")."<br><br>";

			$txt.="Razem: ".number_format(count($obj->magazyn->towar),0," "," ");
			$txt.="<br>Zanalizowano $razem pozycji z czego dodano $dodano.";
	
			$czas=time()-$cz_start;
			$sek=$czas%60;
			$czas=floor($czas/60);
			$txt.="<br>Czas: $czas min. $sek s.";

			$czas=time()-$import_start_time;
			$sek=$czas%60;
			$czas=floor($czas/60);
			$txt.="<br>Czas od startu: $czas min. $sek s.";
			

			echo "<script> import_debug('$txt'); </script>";
			ob_flush();
			flush();
		}
	}


?>
