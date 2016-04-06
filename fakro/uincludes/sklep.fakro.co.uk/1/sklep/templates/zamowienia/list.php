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
  
  $ri_procent = '';
  if($za_voucher_id) {
    $sql1 = "SELECT * FROM towar t
    LEFT JOIN towar_kategoria kt ON (t.to_id = kt.tk_to_id)
    LEFT JOIN kategorie k ON (kt.tk_ka_id = k.ka_id)
    LEFT JOIN rabat_ilosciowy ri ON (k.ka_id = ri.ri_ka_id)
    WHERE t.to_indeks='".$za_voucher_id."'
    AND k.ka_nazwa='Coupons'";
    parse_str(ado_query2url($sql1));
  }
	
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
  
  if($ri_procent) {
    ($wartosc_brutto-(($wartosc_brutto*$ri_procent)/100));
    
    $wartosc_netto = ($wartosc_netto-(($wartosc_netto*$ri_procent)/100));
    $wartosc_brutto = ($wartosc_brutto-(($wartosc_brutto*$ri_procent)/100));
    
    if($wartosc_netto<0) $wartosc_netto = 0;
    if($wartosc_brutto<0) $wartosc_brutto = 0;
  }
  
	$wartosc_netto_zl=u_cena($wartosc_netto);
	$wartosc_brutto_zl=u_cena($wartosc_brutto);

	$display_delete = $za_status ? "none" : "";

	$status = sysmsg("status_$za_status","status");
	
	include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");

	$osoba_przyjecia=$WM->osoba($za_osoba_przyjecia);
	$osoba_realizacji=$WM->osoba($za_osoba_realizacji);

?>
