<?
	

	if ($LIST[to_id])
	{
		echo "<script>document.cookie='ciacho[to_id]=$LIST[to_id]';</script>";
	}
	else
	{
		$LIST[to_id]=$CIACHO[to_id];
	}
	include_once("$SKLEP_INCLUDE_PATH/templates/cartform.php");
?>
