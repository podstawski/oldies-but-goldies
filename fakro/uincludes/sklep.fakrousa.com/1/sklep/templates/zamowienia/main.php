<?
	if ($AUTH[id]<=0)
	{
		$error="brak usera";
		return;
	}

	if (!$size) $size=7;

	$tydzien_temu=$NOW-$size*24*3600;
	$FROMWHERE="FROM zamowienia WHERE za_su_id = ".$AUTH[parent]." 
				AND (za_status IN (0,1) OR za_data>$tydzien_temu)";

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}

	
	$sql = "SELECT * $FROMWHERE
			ORDER BY za_data DESC";

	$navi=$size?navi($self,$LIST,$size):"";


	if (strlen($navi))
		$res = $adodb->SelectLimit($sql,$size,$LIST[start]+0);
	else
		$res = $adodb->execute($sql);

	if (!$res->RecordCount())
	{
		$error = sysmsg("No orders in database.","system");
		return;
	}

	$sysmsg_lp=sysmsg("Lp.","cart");
	$sysmsg_number=sysmsg("Order number","system");
	$sysmsg_order=sysmsg("Order","system");
	$sysmsg_count=sysmsg("Articles count","system");
	$sysmsg_status=sysmsg("Status","system");
	$sysmsg_sure=sysmsg("Are You sure, You want to delete this order ?","order");	
	$sysmsg_article_name = sysmsg("Article name","cart");
	$sysmsg_value=sysmsg("Value","cart");
	/*FAKRO*/
	#$sysmsg_value_netto=sysmsg("Value netto","order");
	#$sysmsg_print=sysmsg("Print","order");
	/**/
	$lp=0;
	$i=0;
?>
