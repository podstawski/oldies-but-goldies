<?
	$nazwa = $FORM[nazwa];
	$dp = $FORM[datap];
	$hp = $FORM[godzinap];
	$dk = $FORM[datak];
	$hk = $FORM[godzinak];
	$rabat = $FORM[rabat];
	$pm_id = $FORM[pm_id];
	$action_id = $pm_id;
	if (!strlen($pm_id)) return;
	if (!strlen($dp)) $czasod = "NULL";	
	else
	{
		$t = explode("-",$dp);
		$czasod = strtotime($t[2]."-".$t[1]."-".$t[0]." $hp");
	}

	if (!strlen($dk)) $czasdo = "NULL";	
	else
	{
		$t = explode("-",$dk);
		$czasdo = strtotime($t[2]."-".$t[1]."-".$t[0]." $hk");
	}

	//$rabat = ereg_replace("[^0-9\.]","",$rabat);
	//$rabat = ereg_replace(",",".",$rabat);
	$rabat = toFloat($rabat);
	if (!strlen($rabat)) $rabat = "NULL";

	$sql = "UPDATE promocja SET 
			pm_symbol = '$nazwa',
			pm_poczatek = $czasod,
			pm_koniec = $czasdo,
			pm_rabat_domyslny = $rabat
			WHERE pm_id = $pm_id";
//	$adodb->debug=1;
	$adodb->execute($sql);
//	$adodb->debug=0;

?>
