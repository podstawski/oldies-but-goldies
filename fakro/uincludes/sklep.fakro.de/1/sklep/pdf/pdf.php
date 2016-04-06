<?

	$bg_color = hex2dec("#40F7EF");
	$pdf->SetFillColor($bg_color['R'],$bg_color['G'],$bg_color['B']);


	$pdf->AddFont('verdanabpl', '', 'verdanabpl.php'); 
	$pdf->SetFont('verdanabpl', '', 12); 

	//$pdf->SetFont('verdana','B',10);
	//$pdf->SetFont('helvetica','B',10);

	$pdf->Cell(0,5,'M±czna zupa mêdrca w ¿ó³tej rzece',1,1,"L",1);


?>
