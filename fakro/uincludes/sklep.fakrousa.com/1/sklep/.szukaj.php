<?
	if ($WEBTD->more && !$WEBTD->cos) 
		$kameleon_adodb->execute("UPDATE webtd SET cos=1 WHERE sid=".$WEBTD->sid);
	include("$SKLEP_INCLUDE_PATH/szukaj.php");
?>
