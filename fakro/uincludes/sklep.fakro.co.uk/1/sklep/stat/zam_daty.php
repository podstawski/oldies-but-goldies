<?
	$dzis0=unixdate(humandate($NOW),0);
	$dow=date("w",$NOW);
	$m=date("m",$NOW);
	$y=date("Y",$NOW);

	if ($dow==0) $dow=7;
	$tydzien0=$dzis0-($dow-1)*3600*24;
	$miesiac0=unixdate(sprintf("01-%02d-$y",$m),0);
	$rok0=unixdate(sprintf("01-01-$y",$m),0);

	$statusy=array(0,1,-1);
	$stat_daty=array(0=>"za_data",1=>"za_data_przyjecia",-1=>"za_data_realizacji");

	$zakresy=array();

?>
