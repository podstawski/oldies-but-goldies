<?
	if ($AUTH[id]>0)
	{
		$sql = "DELETE FROM koszyk WHERE ko_deadline < $NOW OR ko_ilosc=0;
				SELECT * FROM koszyk WHERE
				ko_su_id = ".$AUTH[id]." AND ko_rez_data IS NULL 
				ORDER BY ko_id;
				
				";

		$res = $adodb->execute($sql);
		
		if (!$res->RecordCount())
		{
			$error = sysmsg("no_article_in_cart","cart");
			return;
		}

		$display_noprice = ($AUTH[p_price]) ? "" : "none";
		$favmore = $more;
	}
	else
	{
		$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];
		if (!is_array($KOSZYK_OFERT) || !count($KOSZYK_OFERT))
		{
			$error = sysmsg("no_article_in_cart","cart");
			return;
		}
	
		$tcc=0;
		foreach(array_keys($KOSZYK_OFERT) AS $tc) 
		{
			$tcc+=$KOSZYK_OFERT[$tc];
		}
		if ($tcc==0)
		{
			$error = sysmsg("no_article_in_cart","cart");
			return;
		}	
		
		$i=0;
		reset($KOSZYK_OFERT);
	}
	
	/*****************************************************************************/
	$sql = "SELECT * FROM tr_waga ORDER BY tr_waga_do DESC LIMIT 1;";
	parse_str(ado_query2url($sql));
	/*****************************************************************************/
	
	$total_quant = 0;
	$total_value_br = 0;
	$total_value = 0;
	$total_oryg = 0;

	$sysmsg_lp=sysmsg("Lp.","cart");
	$sysmsg_article_id=sysmsg("Article Id","cart");
	$sysmsg_article_sizes=sysmsg("Article sizes","cart");
	$sysmsg_quantity=sysmsg("Quantity","cart");
	$sysmsg_price=sysmsg("Price","cart");
	$sysmsg_value=sysmsg("Value","cart");
	$sysmsg_article_name = sysmsg("Article name","cart");
	$sysmsg_total_value = sysmsg("Total value","cart");
	$sysmsg_total_value_br = sysmsg("Total value gross","cart");

	$sysmsg_clear = sysmsg("Clear cart","system");
	$lp=0;
	$i=0;
	
?>
