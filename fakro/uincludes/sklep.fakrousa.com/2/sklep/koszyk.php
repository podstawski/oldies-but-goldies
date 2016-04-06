<?
	global $KOSZYK_NEXT;

	$KOSZYK_NEXT=$next;
	$cart_count = 0;
	if ($AUTH[id] > 0)
	{
		$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];
		if (count($KOSZYK_OFERT))
		{
			$_REQUEST[action]="KoszykOfertDoKoszyka";
			$action=$_REQUEST[action];
			include("$SKLEP_INCLUDE_PATH/action.php");
			$SKLEP_SESSION["KOSZYK_OFERT"]=array();
		}

		$sql = "SELECT COUNT(ko_ilosc) AS cart_count FROM koszyk WHERE
				ko_su_id = ".$AUTH[id]." AND ko_rez_data IS NULL AND (ko_deadline > $NOW OR ko_deadline IS NULL)";

		$sql = "SELECT SUM(ko_ilosc) AS cart_count FROM koszyk WHERE
				ko_su_id = ".$AUTH[id]." AND ko_rez_data IS NULL 
				AND (ko_deadline > $NOW OR ko_deadline IS NULL)";
		parse_str(ado_query2url($sql));
	}
	else
	{
		include("$SKLEP_INCLUDE_PATH/koszyk_ofert.php");
		return;
	}
	
	if (!strlen($cart_count)) $cart_count = 0;

	echo "$cart_count";
?>
