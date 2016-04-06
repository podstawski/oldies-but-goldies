<?
	global $SKLEP_SESSION,$WM;
	
	if (!$WEBTD->sid)
	{
		$WMS=$WM->session;
		$SKLEP_SESSION["WMS"]=$WMS;
		$WM->close();
	}

	if (!$WEBTD->staticinclude) $WM->puke_ws_debug();

	$adodb=null;
?>