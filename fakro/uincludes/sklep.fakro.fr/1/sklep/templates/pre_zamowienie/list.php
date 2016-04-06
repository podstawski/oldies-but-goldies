<?
	if ($i>=$res->RecordCount()) 
	{
		$template_loop=0;
		return;
	}
	parse_str(ado_explodename($res,$i));
	$i++;

	$sql = "SELECT * FROM towar_sklep 
			LEFT JOIN towar ON ts_to_id = to_id 
			LEFT JOIN towar_parametry ON ts_to_id = tp_to_id
			WHERE ts_id = $ko_ts_id AND ts_sk_id = $SKLEP_ID";
	parse_str(ado_query2url($sql));
		
	$_tr = " class=t1";
	if (($i)%2) $_tr = " class=t2";

	$JS_ONCHANGE="chageItemQuantity('$ko_id',this.value)";
	$JS_DELETE="deleteItem('$ko_id')";

	$display_noprice = ($AUTH[p_price]) ? "" : "none";

	$tcount=$ko_ilosc;

	$cena = $WM->system_cena($to_id,$ko_ilosc,$AUTH[parent]);
	$wart=$cena*$tcount;
	
	$wart_to_waga = ($to_waga*$tcount);

	$wymiary=$WM->towar_wymiary($to_id);
	if (!strlen($wymiary)) $wymiary="&nbsp;";

	$lp++;

	include("$SKLEP_INCLUDE_PATH/templates/towar_foto.php");
	if($to_pr_id)
	{
		include("$SKLEP_INCLUDE_PATH/templates/towar_producent.php");
	}
	
	/************************************************************************************/
	$total_value_waga+=$wart_to_waga;
	$_ile_razy = floor($total_value_waga/$tr_waga_do);
	$_waga_reszta = $total_value_waga-($_ile_razy*$tr_waga_do);
	
	if($_ile_razy != 0) $_waga_wartosc = $_ile_razy*$tr_waga_cena_brutto;
	
	$_waga_wartosc_cena_brutto = $_waga_wartosc+towar_waga($_waga_reszta)+$doplata_korsyka;
	
	#$total_value_br_zl=u_Cena($total_value_br+$_waga_wartosc_cena_brutto);
	/************************************************************************************/
	
	eval("\$to_opis_m=\$to_opis_m_$lang;");
	eval("\$to_opis_d=\$to_opis_d_$lang;");

	$total_quant+= $ko_ilosc;
	$total_value+= ($cena*$ko_ilosc);

	include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");

	if (strlen($ko_rez_nr)) $order_number = $ko_rez_nr;
?>
