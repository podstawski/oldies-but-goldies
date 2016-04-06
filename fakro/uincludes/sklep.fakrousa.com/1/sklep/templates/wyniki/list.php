<?
	if ($i>=$result->RecordCount()) 
	{
		$template_loop=0;
		return;
	}

	$lp = $i+1+$LIST[start];

	parse_str(ado_explodename($result,$i));	
	$i++;

	$query="SELECT * FROM towar_parametry WHERE tp_to_id=$to_id";
	parse_str(ado_query2url($query));

	
	$link = $next_char."list[to_id]=$to_id";
	
	if ($AUTH[id]>0)
	{
		$koszyk_display = "inline";
		$zamowienie_display = "none";
	}
	else
	{
		$koszyk_display = "none";
		$zamowienie_display = "inline";
	}
	
	$sysmsg_add_article_to_offer_cart = sysmsg("Add article to offer cart","system");
	$sysmsg_to_jm = sysmsg("$to_jm","cart");
	$sysmsg_show_picture = sysmsg("Show picture","system");
	$kwant = $WM->kwant_towaru($to_id);
	
	$cena=$WM->system_cena($to_id);

	include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");
	include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");

	$LIST[to_id] = $to_id;
	$oc = u_cena($WM->oryginalna_cena($to_id));
	$rabat = (100-round(100*$cena/$WM->oryginalna_cena($to_id),2));
?>
