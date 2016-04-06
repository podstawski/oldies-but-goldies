<?
	if ($AUTH[id]<=0)
	{
		$error="użytkownik bez autoryzacji";
		return;
	}

	$sql = "SELECT count(*) AS c FROM koszyk WHERE
				ko_su_id = ".$AUTH[id]." 
				AND ko_rez_data IS NULL 
				AND (ko_deadline > $NOW OR ko_deadline IS NULL)";

	parse_str(ado_query2url($sql));
	if (!$c) 
	{
		$error="brak pozycji";
		return;
	}

	$sysmsg_sure_all = sysmsg("Are You sure, You want to clear the cart ?","cart");
	$sysmsg_sure = sysmsg("Are You sure, You want to delete this article ?","cart");
	$sysmsg_wrong = sysmsg("Wrong value","system");

	$sysmsg_addfav = sysmsg("Add to favourites","system");
	$sysmsg_prepres = sysmsg("Prepare reservation","system");
	$sysmsg_prepord = sysmsg("Prepare order","system");
	$sysmsg_count = sysmsg("Count values","system");

	$display_noorder = ($AUTH[p_order]) ? "" : "none";
?>
