<?
	$pdf_file="$SKLEP_INCLUDE_PATH/constant/pdf/$LIST[pdf].php";
	if (!file_exists($pdf_file)) $pdf_file="$SKLEP_INCLUDE_PATH/pdf/$LIST[pdf].php";
	if (!file_exists($pdf_file)) return;

	include_once("$SKLEP_INCLUDE_PATH/pdfclass/xfpdf.class.inc");

	$pdf = new XFPDF("P","mm","A4");
	$pdf->Open();
	$pdf->AddPage();

	
	include($pdf_file);

	if ($LIST[prn]) $pdf->IncludeJS("print(true)"); //zapytac o drukowanie
	//$pdf->IncludeJS("print(false)"); //nie pytac o drukowanie

	if (!$KAMELEON_MODE)
	{
		$pdf->Output($LIST[pdf].$LIST[id].".pdf",$LIST[prn]?"I":"D");
		exit();
	}
	else
	{
		$pdf->Output("$UFILES/$LIST[pdf].pdf","F");
		echo "<a href='$UFILES/$LIST[pdf].pdf' target='pdf'><font size=2 color=red><B>Zapisano do pliku PDF</B></font></a>";
	}
?>
