<?
function _convert($str) {
	#$str=str_replace('@','&at;',$str);
	#$str=urlencode($str);
	return $str;
	}

$bg_color = hex2dec("#9FA7B2");
$pdf->SetFillColor($bg_color['R'],$bg_color['G'],$bg_color['B']);

// width, heigth, txt, border, enter (0|1), align, fill (0|1)
//function Image($file,$x,$y,$w=0,$h=0,$type='',$link='')

$za_id = $LIST[id];
$show_index = $LIST[show_indx];
$pdf->AddFont('verdanabpl', '', 'verdanabpl.php'); 
$pdf->AddFont('verdanapl', '', 'verdanapl.php'); 

if(file_exists("$UIMAGES/sb/logo_pdf.jpg")) {
	list($sze,$wys,$foo,$foo) = @getimagesize("$UIMAGES/sb/logo_pdf.jpg");
	if(!$sze) $sze = 1;
	
	$nwys = floor(($wys * 50) / $sze);
	$pdf->Image("$UIMAGES/sb/logo_pdf.jpg",125,10,50,$nwys);
	}

if(!strlen($za_id)) return;

$sql = "SELECT zampoz.*, towar.*, towar_parametry.*
			FROM
				zampoz, towar LEFT JOIN towar_parametry ON tp_to_id = to_id
				LEFT JOIN towar_sklep ON ts_to_id = to_id AND ts_sk_id=$SKLEP_ID
			WHERE zp_za_id = $za_id 
				AND zp_ts_id = ts_id
			";

$res = $adodb->execute($sql);
if(!$res->RecordCount()) return;
$total_quant = 0;
$total_value = 0;

$sql = "SELECT * FROM zamowienia
			LEFT JOIN system_user ON za_su_id = su_id
			LEFT JOIN poczta ON za_poczta=po_id
			WHERE za_id = $za_id ";

parse_str(ado_query2url($sql));
if ($AUTH[parent] != $za_su_id && !$AUTH[p_admin]) return;
$AUTH[nazwa]=$su_nazwisko;
$AUTH[kod]=$su_login;

$sql = "SELECT * FROM zamowienia WHERE za_id = $za_id";
parse_str(ado_query2url($sql));

if($show_index) {
	$AUTH[p_price] = 1;
	parse_str($za_parametry);
	$AUTH[platnosc] = $platnosc;
	$AUTH[dostawa]= $dostawa;
	}

       if (strlen($za_adres)>0 && strlen($za_adres)<32)
       {
              $ad_adres="";
              $query="SELECT ad_adres FROM adresy WHERE ad_ws='$za_adres' AND ad_su_id=$za_su_id";
              parse_str(ado_query2url($query));
              if (strlen($ad_adres)) $za_adres=$ad_adres;
       }
       
       $su_nazwa_firmy = $su_nazwisko;

       $query="SELECT *, su_email AS osoba_email FROM system_user WHERE su_id=$za_osoba";
       parse_str(ado_query2url($query));
       
       if (!strlen($za_adres))
       {
              $za_adres = $su_ulica."\n".$su_miasto." ".$su_kod_pocztowy;
              $za_adres = str_replace("&nbsp;"," ",$za_adres);
			  $za_adres = str_replace("/","\n",$za_adres);
       }

if(!strlen($su_nazwa))
	$su_nazwa = "$su_imiona $su_nazwisko $su_nazwa_firmy";

$su_nazwa = "$su_nazwa_firmy";

if($za_data_przyjecia)
	$za_numer.= " / ".date('d-m-Y H:i',$za_data_przyjecia);

$data_sts = "";
if(strlen($za_data_przyjecia)) $data_sts = date("d-m-Y H:i",$za_data_przyjecia);
if(strlen($za_data_realizacji)) $data_sts = date("d-m-Y",$za_data_realizacji);

$pdf->SetFont('verdanapl','',12);
$pdf->Cell(0,10,"",0,0,"L",0);
$pdf->Ln();
#$pdf->Cell(50,10,_convert(sysmsg("COMPANY NAME","firma")),0,0,"L",0);
#$pdf->Cell(100,6,"",0,0,"L",0);
#$pdf->Ln();
#$pdf->Cell(115,6,"",0,0,"L",0);
$pdf->SetFont('verdanapl','',12);
$pdf->Cell(50,6,_convert(sysmsg("Date","cart").": ".date("d-m-y",$za_data)),0,0,"L",0);
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(50,6,_convert(sysmsg("Confirmation of order","cart").": "),0,0,"R",0);
$pdf->SetFont('verdanabpl','',12);
$pdf->Cell(122,6,_convert($za_numer),0,0,"L",0);
$pdf->Ln();
$pdf->SetFont('verdanapl','',12);
$pdf->Cell(50,6,_convert(sysmsg("Order status","cart").": "),0,0,"R",0);
$pdf->SetFont('verdanabpl','',12);
$pdf->Cell(122,6,_convert(sysmsg("status_$za_status","status")." $data_sts"),0,0,"L",0);
$pdf->Ln();
$pdf->SetFont('verdanapl','',12);
$pdf->Cell(50,6,_convert(sysmsg("Client name","cart").": "),0,0,"R",0);
$pdf->SetFont('verdanabpl','',12);
$pdf->Cell(122,6,_convert("$su_nazwa"),0,0,"L",0);
$pdf->Ln();
$pdf->SetFont('verdanapl','',12);
$pdf->Cell(50,6,_convert(sysmsg("Delivery addres","cart").": "),0,0,"R",0);
$pdf->SetFont('verdanabpl','',12);
$pdf->Cell(122,20,_convert("$za_adres"),0,0,"L",0);
$pdf->Ln();
$pdf->SetFont('verdanapl','',12);
$pdf->Cell(50,6,_convert(sysmsg("Client code","cart").": "),0,0,"R",0);
$pdf->SetFont('verdanabpl','',12);
$pdf->Cell(122,6,_convert($osoba_email),0,0,"L",0);
$pdf->Ln();
$pdf->SetFont('verdanapl','',12);
$pdf->Cell(50,6,_convert(sysmsg("Delivery type","cart").": "),0,0,"R",0);
$pdf->SetFont('verdanabpl','',12);
$pdf->Cell(122,6,_convert($po_nazwa),0,0,"L",0);
$pdf->Ln();
$pdf->SetFont('verdanapl','',12);
$pdf->Cell(50,6,_convert(sysmsg("Client order","cart").": "),0,0,"R",0);
$pdf->SetFont('verdanabpl','',12);
$pdf->Cell(122,6,_convert($za_numer_obcy),0,0,"L",0);
$pdf->Ln();
/*
       $pdf->SetFont('verdanapl','',12);
       $pdf->Cell(35,6,_convert(sysmsg("Payment type","cart").": "),0,0,"L",0);
       $pdf->SetFont('verdanabpl','',12);
       $pdf->Cell(35,6,_convert($AUTH[platnosc]),0,0,"L",0);
*/
       $pdf->Ln();
       $pdf->SetFont('verdanabpl','',12);
       $pdf->Cell(50,6,_convert(sysmsg("Specyfication","cart").":"),0,0,"L",0);
       $pdf->Ln();
       $pdf->SetFont('verdanabpl','',8);
       $pdf->Cell(7,5,_convert(sysmsg("Lp.","cart")),1,0,"L",1);
/* Duplikuje siê 2006-05-15 Camel
       if ($show_index)
       {
              $pdf->Cell(30,5,_convert(sysmsg("Article id.","cart")),1,0,"L",1);
       }
*/
       $pdf->Cell(50,5,_convert(sysmsg("Article name","cart")),1,0,"L",1);
       $pdf->Cell(35,5,_convert(sysmsg("Article index","cart")),1,0,"L",1);
       $pdf->Cell(20,5,_convert(sysmsg("Article sizes","cart")),1,0,"L",1);
       $pdf->Cell(10,5,_convert(sysmsg("Quantity","cart")),1,0,"L",1);

       if ($AUTH[p_price])
       {
              $pdf->Cell(25,5,_convert(sysmsg("Price","cart")),1,0,"R",1);
              $pdf->Cell(25,5,_convert(sysmsg("Value","cart")),1,0,"R",1);
       }
       $pdf->Ln();
       $total_value = 0;
       for ($i=0; $i < $res->RecordCount(); $i++)
       {
              parse_str(ado_explodename($res,$i));
              $pdf->Cell(7,5,_convert(($i+1)),1,0,"L",0);
/*
              if ($show_index)
              {
                     $pdf->Cell(30,5,_convert($to_indeks),1,0,"L",0);
              }
*/
              $pdf->Cell(50,5,_convert(unhtml(sysmsg($to_nazwa,'towar'))),1,0,"L",0);
              $pdf->Cell(35,5,_convert($to_indeks),1,0,"L",0);

              $wymiary=$tp_a;
              if (strlen($tp_b)) $wymiary="$wymiary x $tp_b";
              $pdf->Cell(20,5,_convert($wymiary),1,0,"L",0);
              $pdf->Cell(10,5,_convert($zp_ilosc),1,0,"R",0);
              if ($AUTH[p_price])
              {
                     $pdf->Cell(25,5,_convert(u_cena($zp_cena*((100+$to_vat)/100))),1,0,"R",0);
                     $pdf->Cell(25,5,_convert(u_cena($zp_cena*$zp_ilosc*((100+$to_vat)/100))),1,0,"R",0);
              }

              $pdf->Ln();
              $total_value+= ($zp_cena*$zp_ilosc);
       }
       
       
// sprawdzenie czy uzytkownik ma bon
if($za_voucher_id) {
  $ri_procent = '';
  $sql1 = "SELECT * FROM towar t
  LEFT JOIN towar_kategoria kt ON (t.to_id = kt.tk_to_id)
  LEFT JOIN kategorie k ON (kt.tk_ka_id = k.ka_id)
  LEFT JOIN rabat_ilosciowy ri ON (k.ka_id = ri.ri_ka_id)
  WHERE t.to_indeks='".$za_voucher_id."'
  AND k.ka_nazwa='Coupons'";
  parse_str(ado_query2url($sql1));
  
  $voucher_lp = 2;

  $voucher_wartosc_table = $ri_procent;
  $voucher_wartosc = $ri_procent;
  $voucher_table = 'Discount '.$za_voucher_id;

  $pdf->Cell(7,5,_convert(($i+1)),1,0,"L",0);
  $pdf->Cell(50+35+20,5,_convert($voucher_table),1,0,"L",0);
  $pdf->Cell(10,5,_convert("1"),1,0,"R",0);
  if ($AUTH[p_price])
  {
    $pdf->Cell(25+25,5,_convert('-'.$voucher_wartosc_table.'%'),1,0,"R",0);
  }

  $pdf->Ln();
  
  $za_wart_br = ($total_value-(($total_value*$voucher_wartosc)/100));
  if($za_wart_br<0) $za_wart_br = 0; 
  
}else{
  $voucher_lp = 1;
  $voucher_wartosc = 0;
}
//  


// FAKRO pozrycja ceny transportu
       $pdf->Ln();$pdf->Ln();
       $pdf->SetFont('verdanabpl','',12);
       $pdf->Cell(50,6,_convert(sysmsg("Delivery","order").":"),0,0,"L",0);
       $pdf->Ln();

       $sql = "SELECT * FROM zamowienia LEFT JOIN poczta ON za_poczta=po_id
                     WHERE za_id = $za_id";
       parse_str(ado_query2url($sql));

// FAKRO zamiana byl problem z dostaniem sie do odpowienich danych
       $za_poczta_br = $po_cena_br;
       $za_poczta_nt = $po_cena_nt;

       $pdf->SetFont('verdanabpl','',8);
       $pdf->Cell(7,5,'1',1,0,"L",0);
       $pdf->Cell(105,5,_convert($po_nazwa),1,0,"L",0);
       $pdf->Cell(10,5,_convert("1"),1,0,"R",0);
       if ($AUTH[p_price])
       { 
              $pdf->Cell(25,5,_convert(u_cena($za_poczta_br)),1,0,"R",0);
              $pdf->Cell(25,5,_convert(u_cena($za_poczta_br)),1,0,"R",0);
       }

       $pdf->Ln();
       $za_wart_nt+=$za_poczta_nt;
       
       if($voucher_wartosc > 0) {
         $za_wart_br_=($za_wart_br-(($za_wart_br*$voucher_wartosc)/100))+$za_poczta_br;
       }else{
         
         $za_wart_br_=$za_wart_br+$za_poczta_br;
       }

//       $pdf->Ln();
//       $pdf->SetFont('verdanapl','',12);
//       $pdf->Cell(130,6,_convert(sysmsg("Order value","cart").":"),0,0,"R",0);
//       $pdf->SetFont('verdanabpl','',12);       
//       $pdf->Cell(40,6,_convert(u_cena($za_wart_br_-(($za_wart_br_*15)/100))),0,0,"R",0);
//
//       $pdf->Ln();
//       $pdf->SetFont('verdanapl','',12);
//       $pdf->Cell(130,6,_convert(sysmsg("Tax id","order").":"),0,0,"R",0);
//       $pdf->SetFont('verdanabpl','',12);       
//       $pdf->Cell(40,6,_convert(u_cena((($za_wart_br_*15)/100))),0,0,"R",0);

       $pdf->Ln();
       $pdf->SetFont('verdanapl','',12);
       $pdf->Cell(130,6,_convert(sysmsg("Gross","cart").":"),0,0,"R",0);
       $pdf->SetFont('verdanabpl','',12);
       $pdf->Cell(40,6,_convert(u_cena($za_wart_br_)),0,0,"R",0);
       $pdf->Ln();

       $pdf->Ln();

?>
