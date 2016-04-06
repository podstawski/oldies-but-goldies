<?
	if ($AUTH[id] <= 0) return;

	$sql = "SELECT COUNT(ko_ilosc) AS cart_count FROM koszyk WHERE
			ko_su_id = ".$AUTH[id]." AND ko_rez_data IS NULL 
			AND (ko_deadline > $NOW OR ko_deadline IS NULL)";
	parse_str(ado_query2url($sql));

	$button_display = "none";		
	$cart_button_display = "none";
	if ($cart_count)
	{
		$cart_button_display = "";
	}
	else
	{
		$button_display = "";
	}


	$sql = "SELECT ul_nazwa, count(*) AS ul_count FROM ulubione
			WHERE ul_su_id = ".$AUTH[id]." 
			GROUP BY ul_nazwa";


	$res = $adodb->execute($sql);

	$sysmsg_lp = sysmsg("Lp.","system");
	$sysmsg_favourit_name = sysmsg("Favourit name","system");
	$sysmsg_articles_count = sysmsg("Articles count","system");
	$sysmsg_add_to_cart=sysmsg("Add to cart","system");
	$sysmsg_confirm = sysmsg("Are You sure, You want to delete this favourit ?","order");
	$sysmsg_cart_to_favourites = sysmsg("Cart to favourites","system");

	$lp=0;
	$i=0;


?>
