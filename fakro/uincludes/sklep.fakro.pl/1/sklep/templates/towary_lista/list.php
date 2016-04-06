<?
	if ($i>=count($to_ids)) 
	{
		$template_loop=0;
		return;
	}
	$to_id=$to_ids[$i];
	
	$query="SELECT $towar_pola FROM towar WHERE to_id=$to_id ";
	parse_str(ado_query2url($query));


	$i++;
	$lp++;

	parse_str($WM->table_row2url("towar_sklep",
					array("ts_to_id"=>$to_id,"ts_sk_id"=>$SKLEP_ID)
					,false));
	parse_str($WM->table_row2url("towar_parametry",array("tp_to_id"=>$to_id),true));


	$query="SELECT count(pt_id) AS promocja_towaru_count FROM promocja_towaru,promocja 
			WHERE pt_ts_id=$ts_id AND pt_pm_id=pm_id
			AND (pm_poczatek<$NOW OR pm_poczatek IS NULL)
			AND (pm_koniec>$NOW OR pm_koniec IS NULL)
			AND (pt_poczatek<$NOW OR pt_poczatek IS NULL)
			AND (pt_koniec>$NOW OR pt_koniec IS NULL)		
			";
	parse_str(ado_query2url($query));


	include("$SKLEP_INCLUDE_PATH/templates/towar_foto.php");

	if ($to_pr_id)
	{
		include("$SKLEP_INCLUDE_PATH/templates/towar_producent.php");
	}

	$cena=$WM->system_cena($to_id);

	include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");
	include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");
	
	eval("\$to_opis_m=\$to_opis_m_$lang;");
	eval("\$to_opis_d=\$to_opis_d_$lang;");

	if ($AUTH[id]>0)
		$JS_CART = "addItem2Cart('$to_id',".$WM->kwant_towaru($to_id).",'".sysmsg("$to_jm","cart")."')";
	else
		$JS_CART = "chageItemQuantity('$to_id',".$WM->kwant_towaru($to_id).",".$WM->kwant_towaru($to_id).",0,'".sysmsg("$to_jm","cart")."')";

	if ($SYSTEM[mag])
	{
		$stan_magazynu=$WM->stan_magazynu_display($to_id);
		$dostep_magazynu=$WM->dostep_magazynu_display($to_id);
	}

	$wymiary=$WM->towar_wymiary($to_id);
	if (!strlen($wymiary)) $wymiary="&nbsp;";

