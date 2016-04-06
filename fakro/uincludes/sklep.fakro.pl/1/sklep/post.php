<?
	global $SKLEP_SESSION,$WM;
	
	if (!isset($WEBTD->sid) OR !$WEBTD->sid)
	{
		$WMS=$WM->session;
		$SKLEP_SESSION["WMS"]=$WMS;
		$WM->close();
	}

	if (!isset($WEBTD->staticinclude) OR !$WEBTD->staticinclude) $WM->puke_ws_debug();
	//if (!$WEBTD->staticinclude) $WM->puke_ws_debug();

	$adodb=null;
?>