<?
	if ($AUTH[id]<=0)
	{
		$error="brak usera";
		return;
	}
	$ko_rez_data = $LIST[ko_rez_data];
	if (!strlen($ko_rez_data)) 
	{
		$error=sysmsg("Missing reservation","system");
		return;
	}


	$sql = "SELECT * FROM koszyk WHERE
		ko_su_id = ".$AUTH[parent]." 
		AND ko_rez_data = $ko_rez_data
		AND (ko_deadline > $NOW OR ko_deadline IS NULL)
		ORDER BY ko_id";

	$res = $adodb->execute($sql);
		
	if (!$res->RecordCount())
	{
		$error = sysmsg("no_article","cart");
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
	$sysmsg_move=sysmsg("Move to cart","system");
	$sysmsg_order=sysmsg("Prepare order","system");
	$sysmsg_sure=sysmsg("Are You sure, You want to move this reservation to cart ?","cart");
	$sysmsg_number=sysmsg("Reservation number","system");
	$sysmsg_date=sysmsg("Reservation date","system");
	$sysmsg_delete=sysmsg("Delete article from cart","system");
	$sysmsg_article_name = sysmsg("Article name","cart");

	$lp=0;
	$i=0;
	$total_quant = 0;
	$total_value = 0;
	$total_value_br = 0;

	$display_noprice = ($AUTH[p_price]) ? "" : "none";

	$sql = "SELECT COUNT(ko_ilosc) AS cart_count FROM koszyk WHERE
		ko_su_id = ".$AUTH[id]." AND ko_rez_data IS NULL
		AND (ko_deadline > $NOW OR ko_deadline IS NULL)";
	parse_str(ado_query2url($sql));

	$display_move = $cart_count ? "none" : "";
	$display_order = $AUTH[p_order] ? "" : "none";
?>
