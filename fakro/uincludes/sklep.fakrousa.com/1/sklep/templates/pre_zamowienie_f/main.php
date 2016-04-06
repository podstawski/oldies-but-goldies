<?
	if ($AUTH[id]<=0)
	{
		$error="brak usera";
		return;
	}
	$adodb->debug=0;
	
	$sql = "SELECT su_stan FROM system_user WHERE su_id = ".$AUTH[id]."";
	$res = $adodb->execute($sql);
	parse_str(ado_explodename($res,0));
	
	$sql = "SELECT *
		FROM
		tr_ceny,tr_typ,tr_strefa
		WHERE
		tr_ceny.tr_ceny_id = '$_POST[transport]' AND
		tr_strefa.tr_strefa_name = '$su_stan' AND
		tr_ceny.tr_typ_id = tr_typ.tr_typ_id AND
		tr_ceny.tr_strefa_typ = tr_strefa.tr_strefa_typ";
		
	$res = $adodb->execute($sql);
	
	$jscript = "poczta = new Array()\n";
	$deliv_display = 'none';
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		$deliv_display = 'inline';
		parse_str(ado_explodename($res,$i));
		
		$jscript.= "poczta[$tr_ceny_id]= new Array()\n";
		$jscript.= "poczta[$tr_ceny_id]['n']= $tr_ceny\n";
		$jscript.= "poczta[$tr_ceny_id]['b']= $tr_ceny\n";
	}

	$sql = "SELECT MAX(za_numer) AS order_number FROM zamowienia WHERE za_su_id = ".$AUTH[parent];
	parse_str(ado_query2url($sql));
	$order_number+=1;

	$sql = "SELECT count(*) AS order_number_all FROM zamowienia";
	parse_str(ado_query2url($sql));
	$order_number_all+=1;

	$sql = "SELECT su_adres1, su_adres2, su_adres3
		FROM system_user WHERE su_id = ".$AUTH[parent];
	parse_str(ado_query2url($sql));

	$adres_options="";
	$adres_display="";

	for ($i=1;$i<=3;$i++)
	{
		eval("\$adr=\$su_adres$i ;");
		$adr=stripslashes($adr);
		$a=addslashes($adr);
		if (strlen($adr)) $adres_options.="<option value=\"$a\">$adr</option>\n";
	}
	
	$sql = "SELECT * FROM adresy WHERE ad_su_id = ".$AUTH[parent]." ORDER BY ad_adres";
	$res = $projdb->execute($sql);
	for ($i=0; $i < $res->RecordCount();$i++)
	{
		parse_str(ado_explodename($res,$i));
		$adr=stripslashes($ad_adres);
		if (strlen($ad_ws)) $ad_adres = $ad_ws;
		$a=addslashes($ad_adres);
		$adres_options.="<option value=\"$a\">$adr</option>\n";		
	}

	if (!strlen($adres_options)) $adres_display="none";


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
	$sysmsg_article_name=sysmsg("Article name","cart");
	$sysmsg_article_sizes=sysmsg("Article sizes","cart");
	$sysmsg_quantity=sysmsg("Quantity","cart");
	$sysmsg_price=sysmsg("Price","cart");
	$sysmsg_value=sysmsg("Value","cart");
	$sysmsg_tax=sysmsg("Tax","cart");
	$sysmsg_total=sysmsg("Total","cart");
	$sysmsg_notice=sysmsg("Notice","system");
	$sysmsg_adres=sysmsg("Delivery addres","system");
	$sysmsg_submit=sysmsg("Submit order","system");
	$sysmsg_please=sysmsg("Please, fill the order number field ","cart");
	$sysmsg_order_number=sysmsg("Order number","system");
	$sysmsg_delivery = sysmsg("Delivery","cart");
	$sysmsg_choose_delivery = sysmsg("Choose delivery type","cart");
	$sysmsg_delivery_free = sysmsg("Delivery free","cart");
	$sysmsg_delivery_costs = sysmsg("Delivery costs","cart");
	$sysmsg_netto = sysmsg("netto","cart");
	$sysmsg_brutto = sysmsg("gross","cart");

	if (!strlen($adres_options)) $sysmsg_adres="&nbsp;";

	$lp=0;
	$i=0;
	$total_quant = 0;
	$total_value = 0;
	$total_value_br = 0;

	$display_noprice = ($AUTH[p_price]) ? "" : "none";
?>
