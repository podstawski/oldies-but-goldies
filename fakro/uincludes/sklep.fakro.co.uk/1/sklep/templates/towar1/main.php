<?
	$to_id = 0;

	$sql = "SELECT * FROM towar LEFT JOIN towar_sklep ON ts_to_id=to_id AND ts_sk_id=$SKLEP_ID
			WHERE to_id = $LIST[to_id] ";
	parse_str(ado_query2url($sql));

	
	if (!$to_id)
	{
		$error=sysmsg("missing article","article");
		return;
	}


	
	$sql = "SELECT * FROM towar_parametry WHERE tp_to_id = $to_id";
	parse_str(ado_query2url($sql));

	$kategorie="";
	$query="SELECT tk_ka_id FROM towar_kategoria WHERE tk_to_id=$to_id";
	$result = $adodb->execute($query);
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_explodeName($result,$i));
		if (strlen($kategorie)) $kategorie.=",";
		$kategorie.=$tk_ka_id;
	}

	if (!strlen($to_foto_d) || !strlen($to_foto_m))
	{
		$sql = "SELECT ka_foto_m, ka_foto_d FROM kategorie, towar_kategoria
				WHERE tk_to_id = $to_id AND tk_ka_id = ka_id 
				AND ka_foto_d<>'' LIMIT 1";
		parse_str(ado_query2url($sql));
	}

	if (!strlen($to_foto_d)) $to_foto_d = $ka_foto_d;
	if (!strlen($to_foto_m)) $to_foto_m = $ka_foto_m;
	
	include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");

	include("$SKLEP_INCLUDE_PATH/templates/towar_foto.php");
	if ($to_pr_id)
	{
		include("$SKLEP_INCLUDE_PATH/templates/towar_producent.php");
	}


	$param=array("a","b","c","d","l","r1","r2","o","gatunek","stan");

	
	$sysmsg_index = sysmsg("Article index","article");
	$sysmsg_name = sysmsg("Article name","article");
	$sysmsg_close = sysmsg("button_close","buttons");
	$sysmsg_cena_market = sysmsg("Market price","article");
	$sysmsg_netto = sysmsg("Netto","article");
	

	eval("\$to_opis_m = \$to_opis_m_$lang ;");
	eval("\$to_opis_d = \$to_opis_d_$lang ;");



	$cena = $WM->system_cena($to_id);

	$cena_o=$WM->oryginalna_cena($to_id);

	if (!$cena_o)
		$rabat = 0;
	else
		$rabat=round(100*100*($cena_o-$cena)/$cena_o,2)/100;

	$rabat_display="";
	if (!$rabat) $rabat_display="none";






	include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");
	
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


	$query="SELECT count(pt_id) AS promocja_towaru_count 
			FROM promocja_towaru LEFT JOIN promocja ON pt_pm_id=pm_id 
			WHERE pt_ts_id=$ts_id 
			AND (pm_poczatek<$NOW OR pm_poczatek IS NULL)
			AND (pm_koniec>$NOW OR pm_koniec IS NULL)
			AND (pt_poczatek<$NOW OR pt_poczatek IS NULL)
			AND (pt_koniec>$NOW OR pt_koniec IS NULL)		
			";
	parse_str(ado_query2url($query));

	if (!$ts_aktywny) 
	{
		$cena='';
		$cena_o='';
	}
//	if ($to_id && $page == 12)
//		$load_content = "loadContent('$to_id')";
?>
