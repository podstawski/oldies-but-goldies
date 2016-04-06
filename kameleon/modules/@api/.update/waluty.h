<?
	$xml="";
	global $waluty_list, $plik_walut, $liczba_walut, $WALUTY;
/*
	for($i=0; $i < $liczba_walut; $i++)
		$string.=";$waluty_list[$i]";

	$costxt = $plik_walut.$string.";".$WALUTY[show_header].";".$WALUTY[show_country].";".$WALUTY[show_pln];
	$costxt.= ";".$WALUTY[show_tableno].";".$WALUTY[show_names];
*/
/////////
	if (is_array($waluty_list))
		while (list($key,$val) = each($waluty_list))
			$string.=";$key=$val";
	
	$liczba_walut = count($waluty_list);

	$costxt = $plik_walut.";".$liczba_walut.$string.";".$WALUTY[show_header].";".$WALUTY[show_country].";".$WALUTY[show_pln];
	$costxt.= ";".$WALUTY[show_tableno].";".$WALUTY[show_names];

?>
