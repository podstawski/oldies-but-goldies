<?
	if ($AUTH[id]>0)
	{
		$error="uПytkownik z autoryzacja";
		return;
	}
	$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];
	if (!is_array($KOSZYK_OFERT) || !count($KOSZYK_OFERT) )
	{
		$error="brak pozycji";
		return;
	}


	$sysmsg_return = sysmsg("Return to depository","system");
	$sysmsg_count = sysmsg("Count values","system");
	$sysmsg_clear = sysmsg("Clear cart","system");
	$sysmsg_send = sysmsg("Send offer","system");

	$sysmsg_sure = sysmsg("Are You sure, You want to clear the cart ?","cart");
?>
