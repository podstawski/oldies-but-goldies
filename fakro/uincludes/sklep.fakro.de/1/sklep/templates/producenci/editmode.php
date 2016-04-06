<?
	if ($next_id==$WEBTD->next) return;


	$pos=strpos($costxt,"&next_id=");
	if ($pos) $costxt=substr($costxt,0,$pos);

	$costxt.="&next_id=".$WEBTD->next;
	$sql = "UPDATE webtd SET costxt='$costxt' WHERE sid = $WEBTD->sid";
	$kameleon_adodb->execute($sql);
?>
