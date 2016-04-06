<?
	$query="SELECT * FROM zamowienia WHERE za_id=$action_id";
	parse_str(ado_query2url($query));

	$osoba_id=0;
	$su_opiekun=0;
	parse_str($za_parametry);
	$status = sysmsg("status_$za_status","status");

	if ($osoba_id)
	{
		$sql="SELECT su_nazwisko AS osoba_n, su_imiona AS osoba_i,su_email AS osoba_email 
				FROM system_user WHERE su_id=$osoba_id"; 
		parse_str(ado_query2url($sql));
		$osoba="$osoba_i $osoba_n";
	}


	$sql="SELECT su_nazwisko AS master,su_email AS master_email 
			FROM system_user WHERE su_id=".$SYSTEM[master]; 
	parse_str(ado_query2url($sql));


	$sql="SELECT su_nazwisko AS firma,su_email AS firma_email, su_telefon AS telefon, su_nip AS nip,su_opiekun,su_miasto,su_kod_pocztowy,su_ulica,su_gsm,su_login
			FROM system_user WHERE su_id=".$za_su_id; 
	parse_str(ado_query2url($sql));
	$su_login = str_replace("login:","",$su_login);
//	if (strlen($osoba_email)) $firma_email = "";


	$sql="SELECT sum(zp_cena*zp_ilosc) AS za_wartosc, 
				sum(round((100+to_vat)*zp_cena*zp_ilosc)/100) AS za_wartosc_br
			FROM zampoz,towar_sklep,towar
			WHERE zp_za_id=$action_id AND to_id=ts_to_id AND ts_id=zp_ts_id";
	parse_str(ado_query2url($sql));


	$za_wartosc_zl=u_cena($za_wartosc);
	$za_wartosc_br_zl=u_cena($za_wartosc_br);
?>