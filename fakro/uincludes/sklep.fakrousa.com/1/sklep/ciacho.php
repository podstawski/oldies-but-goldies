<?

	$JS="";

	foreach(explode('&',$costxt) AS $para)
	{
		if (!strlen($para)) continue;
		$p=explode('=',$para);
		$w=urldecode($p[1]);
		$a=urldecode($p[0]);
		$CIACHO[$a]=$w;
		if ($cos) $JS.="document.cookie='$a=$w;path=/';\n";
	}
	$_REQUEST['ciacho']=$CIACHO;

?>
<script>
<?echo $JS;?>
</script>
