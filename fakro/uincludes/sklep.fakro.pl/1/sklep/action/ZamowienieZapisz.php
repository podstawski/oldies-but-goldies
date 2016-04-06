<?
	if ($AUTH[id] <= 0 || !strlen($FORM[order_number]) || !strlen($AUTH[parent])) return;
	$LIST[order_number] = $FORM[order_number];
	$LIST[uwagi] = $FORM[uwagi];
	$LIST[dostawa] = $FORM[dostawa];
	$LIST[dostawa_brutto] = $FORM[dostawa_brutto];
	$LIST[dostawa_netto] = $FORM[dostawa_netto];
	$dostawa=($FORM[rodzaj_dostawy])?$FORM[rodzaj_dostawy]:"NULL";

	$FORM[dostawa_brutto]+=0;
	$FORM[dostawa_netto]+=0;

	if (!strlen($LIST[dostawa_netto]))
		$LIST[dostawa_netto] = 0;

	if (!strlen($LIST[dostawa_brutto]))
		$LIST[dostawa_brutto] = 0;

	$adodb->debug=0;
	$adodb->BeginTrans();

	$subsql = "SELECT MAX(za_numer)+1 AS maxnumer FROM zamowienia WHERE za_su_id = ".$AUTH[parent];
	parse_str(ado_query2url($subsql));	
	if (!strlen($maxnumer)) $maxnumer=1;

	if (strlen($FORM[platnosc])) $AUTH[platnosc]='payment_'.$FORM[platnosc];
	

	$pram_string = "osoba_id=".$AUTH[id]."&osoba=".urlencode($AUTH[imiona])."+".urlencode($AUTH[nazwisko])."&platnosc=".urlencode(sysmsg($AUTH[platnosc],"order"))."&dostawa=".urlencode(sysmsg($AUTH[dostawa],"order"));

	$sql = "INSERT INTO zamowienia (za_su_id,za_numer,za_numer_obcy,za_uwagi,za_status,za_data,za_adres,za_parametry,za_sk_id,za_osoba,za_poczta_nt,za_poczta_br,za_poczta,za_data_status)
			VALUES (".$AUTH[parent].",$maxnumer,'".$LIST[order_number]."','".$LIST[uwagi]."',0,$NOW,'".$LIST[dostawa]."','$pram_string',$SKLEP_ID,$AUTH[id],$LIST[dostawa_netto],$LIST[dostawa_brutto],$dostawa,$NOW);
			SELECT MAX(za_id) AS zamowienie_id FROM zamowienia";

	
	parse_str(ado_query2url($sql));

	$action_id=$zamowienie_id;

	$sql = "SELECT ko_ts_id, ko_ilosc FROM koszyk WHERE ko_su_id = ".$AUTH['id']." 
			AND ko_rez_data IS NULL AND (ko_deadline > $NOW OR ko_deadline IS NULL) ORDER BY ko_id";
	$res = $adodb->execute($sql);

	if (!$res->RecordCount())
	{
		$adodb->RollbackTrans();
		return;
	}

	$suma_nt=0;$suma_br=0;
	$query = "";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$to_id = "";
		$sql = "SELECT to_id, to_indeks, to_vat FROM towar_sklep, towar 
				WHERE ts_id = $ko_ts_id 
				AND ts_to_id = to_id 
				AND ts_sk_id = $SKLEP_ID";
		parse_str(ado_query2url($sql));
		if (!strlen($to_id)) continue;
		$cena = $WM->system_cena($to_id,$ko_ilosc,$AUTH[parent]);

		if ($WM->oryginalna_cena($to_id) != 0)
			$rabat=100*($WM->oryginalna_cena($to_id)-$cena)/$WM->oryginalna_cena($to_id);
		else
			$rabat=0;

		if (!strlen($cena)) $cena = "NULL";
		$query.= "INSERT INTO zampoz (zp_za_id,zp_ts_id,zp_ilosc,zp_cena,zp_to_indeks,zp_rabat)
				  VALUES ($zamowienie_id, $ko_ts_id, $ko_ilosc,$cena,'$to_indeks',$rabat);";

		$suma_nt+=$ko_ilosc*$cena;
		$suma_br+=round($ko_ilosc*$cena*(100+$to_vat))/100;
	}

	$query.= "DELETE FROM koszyk WHERE ko_su_id = ".$AUTH['id'];

	$query.=";
			UPDATE zamowienia SET za_wart_nt=$suma_nt,za_wart_br=$suma_br WHERE za_id=$zamowienie_id;";
  
//  sprawdzenie czy user ma voucher
  $voucher_id = '';
  $ko_rez_uwagi = '';
  $query_koszyk = "SELECT ko_rez_uwagi FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_opcje = '".$AUTH['id']."' AND ko_rez_data IS NULL AND (ko_deadline > $NOW OR ko_deadline IS NULL) GROUP BY ko_rez_uwagi";
  parse_str(ado_query2url($query_koszyk));

  if($ko_rez_uwagi) {
    /* ok */
    //$query_voucher = "SELECT * FROM voucher WHERE su_id=".$AUTH['id']." AND voucher_status=2";

    /* nowa */
    $query_voucher = "SELECT * FROM voucher WHERE su_id=".$AUTH['id']." AND voucher_status=2 AND voucher_id='".$ko_rez_uwagi."' ";
    parse_str(ado_query2url($query_voucher));

    if($voucher_id) {
      $suma_br_voucher = $voucher_wartosc;
      $suma_nt_voucher = $voucher_wartosc;

      /* ok */
      //$query.=";
      //UPDATE voucher SET voucher_status=3, voucher_koszyk_id=$zamowienie_id WHERE su_id=$AUTH[id] AND voucher_status=2;";

      /* nowa */
      $query.=";
      UPDATE voucher SET voucher_status=3, voucher_koszyk_id=$zamowienie_id WHERE su_id=$AUTH[id] AND voucher_status=2 AND voucher_id=$voucher_id;";

      $query.=";
      UPDATE zamowienia SET za_voucher_id=$voucher_id WHERE za_id=$zamowienie_id;";


      $suma_br = ($suma_br-$suma_br_voucher);
      $suma_nt = ($suma_nt-$suma_nt_voucher);

      if($suma_br<0) $suma_br = 0;
      if($suma_nt<0) $suma_nt = 0;
    }
  }
//

  //$adodb->debug=1;
	if ($adodb->execute($query))
		$adodb->CommitTrans();
	else
		$adodb->RollbackTrans();
  $adodb->debug=0;
	$query="";
	$sql="";
	$LIST[za_id] = $zamowienie_id;
	$FORM[za_id] = $zamowienie_id;
	$FORM[za_wart_br]=$suma_br;
	$FORM[za_wart_nt]=$suma_nt;

	$FORM[brutto]=$suma_br+$FORM[dostawa_brutto];
	$FORM[netto]=$suma_nt+$FORM[dostawa_netto];

	$nowe_zamowienie=array($FORM);
	$_REQUEST['form']=$FORM;
?>
