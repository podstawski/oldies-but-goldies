<?
	if ($i>=$res->RecordCount()) 
	{
		$template_loop=0;
		return;
	}
	parse_str(ado_explodename($res,$i));
	$i++;

	$sql = "SELECT * FROM towar_sklep, towar WHERE
		ts_id = $ko_ts_id AND ts_to_id = to_id AND ts_sk_id = $SKLEP_ID";
	parse_str(ado_query2url($sql));
		
	$_tr = " class=t1";
	if (($i)%2) $_tr = " class=t2";

	$JS_ONCHANGE="chageItemQuantity('$ko_id',this.value)";
	$JS_DELETE="deleteItem('$ko_id')";

	$display_noprice = ($AUTH[p_price]) ? "" : "none";

	$tcount=$ko_ilosc;

	$cena = $WM->system_cena($to_id,$ko_ilosc,$AUTH[parent]);
	$wart = $cena*$ko_ilosc;


	$wymiary=$WM->towar_wymiary($to_id);
	if (!strlen($wymiary)) $wymiary="&nbsp;";

	$lp++;

	include("$SKLEP_INCLUDE_PATH/templates/towar_foto.php");
	if ($to_pr_id)
	{
		include("$SKLEP_INCLUDE_PATH/templates/towar_producent.php");
	}

	eval("\$to_opis_m=\$to_opis_m_$lang;");
	eval("\$to_opis_d=\$to_opis_d_$lang;");

	$total_quant+= $ko_ilosc;
	$total_value+= ($cena*$ko_ilosc);
	include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");

	if (strlen($ko_rez_nr)) $order_number = $ko_rez_nr;
?>
