<?
	$nazwa = $LIST[nazwa];
	if (!strlen($nazwa)) $nazwa = $FORM[nazwa];
	if (!strlen($nazwa)) 
	{
		$sql = "SELECT ul_nazwa AS nazwa FROM ulubione 
				WHERE ul_su_id = ".$AUTH[id]." ORDER BY ul_id DESC LIMIT 1";
		parse_str(ado_query2url($sql));
		if (!strlen($nazwa)) 
		{
			$error = "Brak";
			return;
		}
	}

	$sql = "SELECT * FROM ulubione WHERE ul_nazwa = '".$nazwa."' AND ul_su_id = ".$AUTH[id];
	$res = $adodb->execute($sql);

	$sysmsg_favourit_name = sysmsg("Favourit name","system");
	$sysmsg_lp = sysmsg("Lp.","system");
	$sysmsg_article_id = sysmsg("Article Id.","system");
	$sysmsg_article_sizes = sysmsg("Article sizes","system");
	$sysmsg_quantity = sysmsg("Quantity","system");
	$sysmsg_confirm = sysmsg("Are You sure, You want to delete this item ?","order");
	$sysmsg_price = sysmsg("Price","system");
	$sysmsg_value = sysmsg("Value","system");
	$sysmsg_total = sysmsg("Total","cart");
	$sysmsg_delart = sysmsg("Delete article from favourites","system");
	$display_prices = $AUTH[p_price] ? "none" : "";
	$sysmsg_article_name = sysmsg("Article name","cart");

	$lp=0;
	$i=0;

?>
