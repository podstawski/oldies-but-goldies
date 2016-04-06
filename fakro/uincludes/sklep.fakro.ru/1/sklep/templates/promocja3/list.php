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

	include("$SKLEP_INCLUDE_PATH/templates/towar_foto.php");
	if ($to_pr_id)
	{
		include("$SKLEP_INCLUDE_PATH/templates/towar_producent.php");
	}

	$cena=$WM->system_cena($to_id);

	$cena_zl=u_cena($cena);
	
	include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");
	include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");
	
	eval("\$to_opis_m=\$to_opis_m_$lang;");
	eval("\$to_opis_d=\$to_opis_d_$lang;");

	if ($AUTH[id]>0)
		$JS_CART = "addItem2Cart('$to_id',".$WM->kwant_towaru($to_id).",'".sysmsg("$to_jm","cart")."')";
	else
		$JS_CART = "chageItemQuantity('$to_id',".$WM->kwant_towaru($to_id).",".$WM->kwant_towaru($to_id).",0,'".sysmsg("$to_jm","cart")."')";
?>
