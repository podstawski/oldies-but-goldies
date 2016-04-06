<?
	$saldo = $WM->saldo_kontrahenta($AUTH[parent]);
	$saldo_zl = u_Cena($saldo);
	$osoba = $WM->osoba($AUTH[id]);
	$firma = $osoba[parent];
?>
