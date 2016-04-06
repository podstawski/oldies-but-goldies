<?
	global $NLETTER;
	$xml="";
	$NLETTER[info] = eregi_replace(";",",",$NLETTER[info]);
	$costxt = $NLETTER[group].";".$NLETTER[outmail].";"
			  .$NLETTER[outpage].";".$NLETTER[msgpage].";"
			  .$NLETTER[info].";".$NLETTER[inpage].";"
			  .$NLETTER[msginpage].";".$NLETTER[host_addr];
?>
