<?
	if ($AUTH[id]<=0)
	{
		$error="brak usera";
		return;
	}

	$sql = "SELECT COUNT(DISTINCT(ko_rez_data)) AS order_number
		FROM koszyk WHERE ko_su_id = ".$AUTH[parent]."AND ko_rez_data IS NOT NULL";

	parse_str(ado_query2url($sql));

	$order_number+=1;



	$sql = "SELECT * FROM koszyk WHERE
		ko_su_id = ".$AUTH[id]." 
		AND ko_rez_data IS NULL 
		AND (ko_deadline > $NOW OR ko_deadline IS NULL)
		ORDER BY ko_id";

	$res = $adodb->execute($sql);
		
	if (!$res->RecordCount())
	{
		$error = sysmsg("no_article_in_cart","cart");
		return;
	}


	$sysmsg_lp=sysmsg("Lp.","cart");
	$sysmsg_article_id=sysmsg("Article Id","cart");
	$sysmsg_article_sizes=sysmsg("Article sizes","cart");
	$sysmsg_quantity=sysmsg("Quantity","cart");
	$sysmsg_price=sysmsg("Price","cart");
	$sysmsg_value=sysmsg("Value","cart");
	$sysmsg_total=sysmsg("Total","cart");
	$sysmsg_notice=sysmsg("Notice","system");
	$sysmsg_submit=sysmsg("Submit reservation","system");
	$sysmsg_please=sysmsg("Please, fill the reservation number field","cart");
	$sysmsg_number=sysmsg("Reservation number","system");
	$sysmsg_article_name = sysmsg("Article name","cart");
	
	if (!strlen($adres_options)) $sysmsg_adres="&nbsp;";

	

	$lp=0;
	$i=0;
	$total_quant = 0;
	$total_value = 0;
	$total_value_br = 0;

	$display_noprice = ($AUTH[p_price]) ? "" : "none";

?>
