<?
	if ($i>=$res->RecordCount()) 
	{
		$template_loop=0;
		return;
	}
	parse_str(ado_explodename($res,$i));
	$i++;
	$lp=$i+$LIST[start];

	$_tr = " class=t1";
	if (($i)%2) $_tr = " class=t2";

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
	}
	if (strlen($za_data_realizacji)) 
	{
		$data_sts = date("d-m-Y",$za_data_realizacji);
		$godz_sts = date("H:i",$za_data_realizacji);
	}

	parse_str($za_parametry);
	
	$query = "SELECT COUNT(*) AS total_count FROM zampoz 
				WHERE zp_za_id = $za_id";	
	parse_str(ado_query2url($query));

	$query = "SELECT Sum(zp_ilosc*zp_cena) AS wartosc_netto ,
				Sum(round(zp_ilosc*zp_cena*(100+to_vat))/100) AS wartosc_brutto
				FROM zampoz,towar_sklep,towar
				WHERE ts_id=zp_ts_id AND to_id=ts_to_id
				AND zp_za_id = $za_id";	

	parse_str(ado_query2url($query));

	$query = "SELECT Sum(zp_ilosc_ws*zp_cena_ws) AS wartosc_netto_ws ,
				Sum(round(zp_ilosc_ws*zp_cena_ws*(100+to_vat))/100) AS wartosc_brutto_ws
				FROM zampoz,towar_sklep,towar
				WHERE ts_id=zp_ts_id AND to_id=ts_to_id
				AND zp_za_id = $za_id";	

	parse_str(ado_query2url($query));

	$wartosc_netto_ws_zl=u_cena($wartosc_netto_ws);
	$wartosc_brutto_ws_zl=u_cena($wartosc_brutto_ws);

	$wartosc_netto_zl=u_cena($wartosc_netto);
	$wartosc_brutto_zl=u_cena($wartosc_brutto);

	$display_delete = $za_status ? "none" : "";

	$status = sysmsg("status_$za_status","status");
	
	include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");

	$osoba_przyjecia=$WM->osoba($za_osoba_przyjecia);
	$osoba_realizacji=$WM->osoba($za_osoba_realizacji);

?>
