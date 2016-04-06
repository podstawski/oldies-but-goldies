<?
	function _convert($str)
	{
		return $str;
	}

	$pdf->AddFont('verdanabpl', '', 'verdanabpl.php'); 
	$pdf->AddFont('verdanapl', '', 'verdanapl.php'); 

	$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];
	
	$_count = $CIACHO["_count"];

	$bg_color = hex2dec("#9FA7B2");
	$pdf->SetFillColor($bg_color['R'],$bg_color['G'],$bg_color['B']);

	$pdf->SetFont('verdanabpl','',18);

				// width, heigth, txt, border, enter (0|1), align, fill (0|1)
//	$pdf->Cell(0,5,_convert('Mšczna pupa mędrca w żółtej rzece'),1,1,"L",1);
	//$pdf->Ln();
//function Image($file,$x,$y,$w=0,$h=0,$type='',$link='')

	if (file_exists("$UIMAGES/sb/logo_pdf.jpg"))
	{
		list($sze,$wys,$foo,$foo) = @getimagesize("$UIMAGES/sb/logo_pdf.jpg");
		if (!$sze) $sze = 1;
		$nwys = floor(($wys * 50) / $sze);
		$pdf->Image("$UIMAGES/sb/logo_pdf.jpg",80,5,50,$nwys);
	}

	if (!is_array($KOSZYK_OFERT)) return;
	$pdf->Cell(0,30," ",0,1,"L",0);


	$pdf->Cell(0,8,_convert(sysmsg("Offer query","cart")),0,0,"C",0);
	$pdf->Ln();
	$pdf->SetFont('verdanabpl','',10);
	$pdf->Cell(0,8,_convert(sysmsg("from www","cart").":"),0,0,"C",0);
	$pdf->Ln();
	$pdf->SetFont('verdanapl','',12);
	$pdf->Cell(0,10,_convert(sysmsg("Date","cart").": ".date("d-m-Y, H:i")),0,0,"R",0);
	$pdf->Ln();$pdf->Ln();
	//$f = file($SKLEP_INCLUDE_PATH."/pdf/daneadresowe");
	$f = explode("\n",sysmsg("Company offer address","firma"));
	$cel = "";$upcel="";$cwidth=0;
	$pdf->SetFont('verdanabpl','',12);
	$cwidth = 6*count($f);
//	$pdf->Cell(90,$cwidth,"",0,0,"L",0);
//	$pdf->Cell(100,$cwidth,"",1,0,"L",0);
//	$pdf->Cell(0,$cwidth,"",0,0,"L",0);
	$pdf->Rect(95,75,110,($cwidth+3),'');
	for ($i=0; $i < count($f); $i++)
	{
		$pdf->Cell(85,6,_convert("........................................"),0,0,"L",0);
		$pdf->Cell(110,6,_convert(trim($f[$i])),0,0,"L",0);
		$pdf->Ln();

	}
	$pdf->SetFont('verdanabpl','',8);
	$pdf->Cell(0,15,"",0,0,"L",0);
	$pdf->Ln();
	$pdf->Cell(10,5,_convert(sysmsg("Lp.","cart")),1,0,"L",1);
	$pdf->Cell(60,5,_convert(sysmsg("Article name","cart")),1,0,"L",1);
	$pdf->Cell(45,5,_convert(sysmsg("Article sizes","cart")),1,0,"L",1);
	$pdf->Cell(20,5,_convert(sysmsg("Quantity","cart")),1,0,"L",1);
	$pdf->Cell(25,5,_convert(sysmsg("Price","cart")),1,0,"L",1);
	$pdf->Cell(30,5,_convert(sysmsg("Value","cart")),1,0,"L",1);
	$pdf->Ln();
	$pdf->SetFont('verdanapl','',8);
	$i=0;
	reset($KOSZYK_OFERT);
	$total = 0;
	$vtotal = 0;
	while (list($tid,$tcount) = each($KOSZYK_OFERT))
	{
		$sql = "SELECT to_nazwa, to_indeks, to_jm FROM towar WHERE to_id = $tid";
		parse_str(ado_query2url($sql));
		$cena = $WM->system_cena($tid);
		$pdf->Cell(10,5,($i+1),1,0,"L",0);
		$pdf->Cell(60,5,_convert($to_nazwa),1,0,"L",0);
		$pdf->Cell(45,5,_convert($WM->towar_wymiary($tid)),1,0,"L",0);
		$pdf->Cell(20,5,_convert($tcount." ".sysmsg("$to_jm","magazyn")),1,0,"R",0);
		$pdf->Cell(25,5,_convert(u_cena($cena)),1,0,"R",0);
		$pdf->Cell(30,5,_convert(u_cena($cena*$tcount)),1,0,"R",0);
		$pdf->Ln();
		$total+=$tcount;
		$vtotal+=($cena*$tcount);
		$i++;
	}
	$pdf->SetFont('verdanabpl','',8);
	$pdf->Cell(115,5,_convert(sysmsg("Total","cart").":"),1,0,"R",0);
	$pdf->Cell(20,5,_convert($total),1,0,"R",0);
	$pdf->Cell(55,5,_convert(u_cena($vtotal)),1,0,"R",0);
	$pdf->SetFont('verdanabpl','',10);
	$pdf->Ln();$pdf->Ln();$pdf->Ln();$pdf->Ln();
	$pdf->Cell(120,5,"",0,0,"R",0);
	$pdf->Cell(0,5,"............................",0,0,"C",0);
	$pdf->Ln();
	$pdf->Cell(120,5,"",0,0,"R",0);
	$pdf->Cell(0,5,"podpis",0,0,"C",0);
	$_count+=1;
	$LIST[id] = $_count;
	if (!headers_sent()) setcookie("ciacho[_count]",$_count);
?>
