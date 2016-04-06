<?
	$cos+=0;

	$query="SELECT count(*) AS ile FROM zamowienia WHERE za_status=$cos";
	parse_str(ado_query2url($query));

	echo $ile;


?> 
