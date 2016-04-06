<?
	if ($i>=$res->RecordCount()) 
	{
		$template_loop=0;
		return;
	}
	parse_str(ado_explodename($res,$i));
	$i++;
	$lp=$i;

	$selected=($pr_id==$CIACHO[pr_id])?"selected":"";

?>
