<?
	if ($AUTH[id]>0)
	{
		if ($i>=$res->RecordCount()) 
		{
			$template_loop=0;
			return;
		}
		parse_str(ado_explodename($res,$i));
		$i++;

		$sql = "SELECT * FROM towar_sklep 
			LEFT JOIN towar ON ts_to_id = to_id 
			LEFT JOIN towar_parametry ON tp_to_id=to_id
			WHERE
			ts_id = $ko_ts_id AND ts_sk_id = $SKLEP_ID";
		parse_str(ado_query2url($sql));
		
		$_tr = " class=t1";
		if (($i)%2) $_tr = " class=t2";

		$JS_ONCHANGE="chageItemQuantity('$ko_id',this.value)";
		$JS_DELETE="deleteItem('$ko_id')";


		$tcount=$ko_ilosc;

		$cena = $WM->system_cena($to_id,$ko_ilosc,$AUTH[parent]);
		$wart = $cena*$ko_ilosc;

		$total_quant+= $ko_ilosc;
		$total_value+= ($cena*$ko_ilosc);
		$total_oryg+= $WM->oryginalna_cena($to_id)*$ko_ilosc;

		include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");
		include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");
	}
	else
	{
		if  ( !(list($tid,$tcount) = each($KOSZYK_OFERT)))
		{
			$template_loop=0;
			return;
		}

		$sql = "SELECT * FROM towar
			LEFT JOIN towar_sklep ON ts_to_id = to_id AND ts_sk_id = $SKLEP_ID
			LEFT JOIN towar_parametry ON tp_to_id=to_id
			WHERE
			to_id = $tid ";
		parse_str(ado_query2url($sql));

		
		$cena = $WM->system_cena($tid,$tcount);
		$wart = $cena*$tcount;
		$total_value+= $wart;

		include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");
		include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");


		$_tr = " class=t1";
		if ((++$i)%2) $_tr = " class=t2";
		$powiazane=$WM->towary_powiazane($to_id)+0;
		$JS_ONCHANGE="chageItemQuantity('$tid',this.value,'".$WM->kwant_towaru($tid)."',$cena,'$powiazane')";
		$JS_DELETE="chageItemQuantity('$tid','0','1',$cena,'$powiazane')";
	}


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

?>
