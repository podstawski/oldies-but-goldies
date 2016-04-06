<?
	$sql = "SELECT * FROM producent ORDER BY pr_nazwa";
	$res = $adodb->execute($sql);
	$i=0;

	$JESTEM_W_DRZEWIE_TOWAROW=strstr($tree,":$next_id:")?1:0;
	$formaction=$JESTEM_W_DRZEWIE_TOWAROW?$self:$more;
	$formmethod=$KAMELEON_MODE?"post":"get";
?>
