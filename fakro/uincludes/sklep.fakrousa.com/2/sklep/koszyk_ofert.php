<?
	global $KOSZYK_NEXT;
	$KOSZYK_NEXT=$next;

	$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];

	$quant = 0;
	if (is_array($KOSZYK_OFERT))		
		while(list($key,$val) = each($KOSZYK_OFERT))
			$quant+=$val;

	echo "<span id=\"koszyk_ofert_szt\">$quant</span> ";

?>
