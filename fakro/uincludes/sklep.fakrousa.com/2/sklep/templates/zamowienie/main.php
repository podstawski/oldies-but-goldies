<?

	if ($AUTH[id]<=0)
	{
		$error="brak usera";
		return;
	}

	$za_id = $LIST[za_id]+0;
	if (!$za_id) 
	{
		$error="&nbsp;";
		return;
	}

	$sql = "SELECT * FROM zamowienia LEFT JOIN poczta ON za_poczta=po_id
			WHERE za_id = $za_id";
	parse_str(ado_query2url($sql));

	if (!$AUTH[p_admin] && $za_su_id!=$AUTH[parent] )
	{
		$error=sysmsg("No order in database","order");
		return;
	}

	$sql = "SELECT * FROM zampoz LEFT JOIN towar_sklep ON zp_ts_id = ts_id
			LEFT JOIN towar ON to_id=ts_to_id 
			LEFT JOIN towar_parametry ON tp_to_id = to_id
			WHERE zp_za_id = $za_id  
			";

	$res = $adodb->execute($sql);
		
	if (!$res->RecordCount())
	{
		$error=sysmsg("No order in database","order");
		return;
	}

	$data_zam = "";
	$godz_zam = "";
	if (strlen($za_data))
	{
		$data_zam = date("d-m-Y",$za_data);
		$godz_zam = date("H:i",$za_data);
	}
	$data_sts = "";
	$godz_sts = "";
	if (strlen($za_data_przyjecia)) 
	{
		$data_sts = date("d-m-Y",$za_data_przyjecia);
		$godz_sts = date("H:i",$za_data_przyjecia);
		$data_przyj = $data_sts;
		$godz_przyj = $godz_sts;
	}
	if (strlen($za_data_realizacji)) 
	{
		$data_sts = date("d-m-Y",$za_data_realizacji);
		$godz_sts = date("H:i",$za_data_realizacji);
		$data_realiz = $data_sts;
		$godz_realiz = $godz_sts;
	}

	if (strlen($za_data_status))
	{
		$data_sts = date("d-m-Y",$za_data_status);
		$godz_sts = date("H:i",$za_data_status);	
	}


	parse_str($za_parametry);

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
	$sysmsg_number=sysmsg("Reservation number","system");
	$sysmsg_date=sysmsg("Order date","system");
	$sysmsg_person=sysmsg("Order person","system");
	$sysmsg_status=sysmsg("Status","system");
	$sysmsg_return=sysmsg("Return","system");
	$sysmsg_article_name = sysmsg("Article name","cart");

	$status=sysmsg("status_$za_status","status");


	$lp=0;
	$i=0;
	$total_quant = 0;
	$total_value = 0;
	$total_value_br = 0;

	$display_noprice = ($AUTH[p_price]) ? "" : "none";

	$osoba_przyjecia=$WM->osoba($za_osoba_przyjecia);
	$osoba_realizacji=$WM->osoba($za_osoba_realizacji);
	$firma=$WM->osoba($za_su_id);

	if (strlen($za_data_przyjecia)) 
	{
		$data_przy = date("d-m-Y",$za_data_przyjecia);
		$godz_przy = date("H:i",$za_data_przyjecia);
	}

	if (strlen($za_adres)>0 && strlen($za_adres)<32)
	{
		$ad_adres="";
		$query="SELECT ad_adres FROM adresy WHERE ad_ws='$za_adres' AND ad_su_id=$za_su_id";
		parse_str(ado_query2url($query));
		if (strlen($ad_adres)) $za_adres=$ad_adres;
	}

	if (!strlen($za_adres))
	{
		$query="SELECT * FROM system_user WHERE su_id=$za_osoba";
		parse_str(ado_query2url($query));
		$za_adres = $su_ulica."<br>".$su_kod_pocztowy." ".$su_miasto;
	}

	$firma[za_adres] = $za_adres;

	if (!strlen($firma[su_nazwa])) $firma[su_nazwa] = $su_imiona." ".$su_nazwisko;
?>