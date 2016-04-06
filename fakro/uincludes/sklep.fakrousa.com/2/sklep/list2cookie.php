<?
	global $LIST,$_COOKIE;

	if (!strlen($costxt)) return;

	$c=explode(':',$costxt);
	if (!strlen($c[1])) $c[1]=$c[0];
	$_COOKIE[$c[1]]=$LIST[$c[0]];
?>
<script>
	document.cookie='<?echo $c[1]?>=<?echo $LIST[$c[0]]?>;path=/';
</script>
