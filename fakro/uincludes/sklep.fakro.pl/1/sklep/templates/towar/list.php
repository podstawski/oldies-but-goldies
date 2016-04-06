<?
	if  ( !(list($i,$p) = each($param)))
	{
		$template_loop=0;
		return;
	}

	eval("\$w = \$tp_$p ;");
	//echo "param: $p = $w, ";
	if (!strlen($w)) $list_continue=1;
	$wymiar=sysmsg("th_tp_$p","system");
	$title=sysmsg("title_tp_$p","system");
	$jm=sysmsg("tp_${p}_jm","towar-param")
?>
