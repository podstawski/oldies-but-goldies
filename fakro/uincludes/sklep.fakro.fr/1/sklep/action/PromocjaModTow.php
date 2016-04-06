<?
	$pt_id = $FORM[pt_id];
	$pm_id = $FORM[pm_id];
	$to_id = $FORM[to_id];
	$rabat = $FORM[rabat];
	$pocz = toFloat($FORM[pm_poczatek]);
	$kon = toFloat($FORM[pm_koniec]);

	$pm_rabat_domyslny = $FORM[pm_rabat_domyslny];

	$action_id = $pt_id;
	$rabat = toFloat($rabat);
	$pm_rabat_domyslny = toFloat($pm_rabat_domyslny);	

	if (!strlen($FORM[pt_poczatek])) $czasod = "NULL";	
	else
	{
		$t = explode("-",$FORM[pt_poczatek]);
		$czasod = strtotime($t[2]."-".$t[1]."-".$t[0]." ".$FORM[godzinap]);
	}

	if (!strlen($FORM[pt_koniec])) $czasdo = "NULL";	
	else
	{
		$t = explode("-",$FORM[pt_koniec]);
		$czasdo = strtotime($t[2]."-".$t[1]."-".$t[0]." ".$FORM[godzinak]);
	}


//	$adodb->debug=1;
//	$projdb->debug=1;
	if (!strlen($rabat) || $rabat=='NULL')
	{		
	
		$sql = "SELECT ts_cena FROM towar_sklep WHERE 
				ts_sk_id = $SKLEP_ID AND ts_to_id = $to_id LIMIT 1";

		parse_str(ado_query2url($sql));

		if (strlen($ts_cena) && strlen($pm_rabat_domyslny))
		{
			$proc = round((100 - $pm_rabat_domyslny) / 100,2);
			$rabat = $ts_cena*$proc;
		}
		else
			$rabat = "NULL";
	}


	if (strlen($pt_id))
	{
		$sql = "UPDATE promocja_towaru SET
				pt_cena = $rabat, 
				pt_poczatek = $czasod,
				pt_koniec = $czasdo						
				WHERE pt_id = $pt_id";
	}
	else
	{
		if (!strlen($pm_id)) return;

		$sql = "SELECT ts_id FROM towar_sklep WHERE 
				ts_sk_id = $SKLEP_ID AND ts_to_id = $to_id";
		parse_str(ado_query2url($sql));
		if (!strlen($ts_id)) return;
		
		if ($czasod=="NULL" && $czasdo=="NULL") 
		{
			$czasod = $pocz;
			$czasdo = $kon;
		}


		$sql = "INSERT INTO promocja_towaru 
				(pt_ts_id,pt_pm_id,pt_cena,pt_poczatek,pt_koniec) VALUES
				($ts_id,$pm_id,$rabat,$czasod,$czasdo)";
	}
//	$adodb->debug=1;
	$adodb->execute($sql);
//$adodb->debug=0;
//	$adodb->debug=0;
//	$projdb->debug=0;
?>
