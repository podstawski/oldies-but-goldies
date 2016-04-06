<script language="javascript">
function showModFun()
{
}
</script>
<?
	$c_array = explode(";",$costxt);
	
	$ilosc_wpisow = count($c_array);
	
/////////

	$ilosc_w = $c_array[1];
	$ilosc_w+=2;
	if ($ilosc_w > 2) 
		for ($i=2; $i < $ilosc_w; $i++)
			parse_str($c_array[$i]);
//////////

	$ih = $ilosc_wpisow-5;
	$ic = $ilosc_wpisow-4;
	$ip = $ilosc_wpisow-3;
	$it = $ilosc_wpisow-2;
	$in = $ilosc_wpisow-1;


	$show_header = $c_array[$ih];
	$show_country = $c_array[$ic];
	$show_pln = $c_array[$ip];
	$show_names = $c_array[$in];
	$show_tableno = $c_array[$it];

	if ($show_header) $c1 = "checked";
	if ($show_country) $c2 = "checked";
	if ($show_pln) $c3 = "checked";
	if ($show_names) $c4 = "checked";
	if ($show_tableno) $c5 = "checked";

	if (strlen($c_array[0])) $curr_file = $c_array[0];
		else $curr_file = "http://adv.gammanet.pl/waluty/nbp.g";
	
	$plik = file($curr_file);
	$xml_string=implode('',$plik);
	

	$waluty = xml2obj($xml_string);

	
	//print_r($waluty);

	echo "<table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
			<TR class=k_form>
				<td colspan=3 class=k_formtitle>".label("Show currencies").":</td>
			</TR>";

	$liczba_walut = count($waluty->tabela_kursow->pozycja);
/*
	for ($i=0; $i < $liczba_walut ; $i++)
	{
		$kod = $waluty->tabela_kursow->pozycja[$i]->kod_waluty;
		$kraj = $waluty->tabela_kursow->pozycja[$i]->nazwa_kraju;

		$checked = "";
		$nr = $i + 1;
		if ($c_array[$nr]) $checked = "checked";

		echo "<tr class=k_form>
		<td align=\"right\">($kraj)</td><td align=\"right\">$kod </td>
		<td><INPUT TYPE=\"checkbox\" $checked NAME=\"waluty_list[$i]\" value=\"1\"></td></tr>";
	}
*/
/////////
	for ($i=0; $i < $liczba_walut ; $i++)
	{
		$wal_kod = $waluty->tabela_kursow->pozycja[$i]->kod_waluty;
		$kraj = $waluty->tabela_kursow->pozycja[$i]->nazwa_kraju;

		$checked = "";
		if ($$wal_kod) $checked = "checked"; 
		echo "<tr class=k_form>
			<td align=\"right\">($kraj)</td><td align=\"right\">$wal_kod </td>
			<td><INPUT TYPE=\"checkbox\" $checked NAME=\"waluty_list[$wal_kod]\" value=\"1\"></td></tr>";
	}
/////////

	echo "
		<TR class=k_form>
			<td colspan=3 class=k_formtitle>".label("Options").":</td>
		</TR>
		<tr class=k_form><td align=\"right\">
			".label("Show header").":</td>
		<td colspan=\"2\"><INPUT TYPE=\"checkbox\" NAME=\"WALUTY[show_header]\" $c1 value=\"1\"></td></tr>
		<tr class=k_form><td align=\"right\">
			".label("Show country").":</td>
		<td colspan=\"2\"><INPUT TYPE=\"checkbox\" NAME=\"WALUTY[show_country]\" $c2 value=\"1\"></td></tr>
		<tr class=k_form><td align=\"right\">
			".label("Show PLN").":</td>
		<td colspan=\"2\"><INPUT TYPE=\"checkbox\" NAME=\"WALUTY[show_pln]\" $c3 value=\"1\"></td></tr>

		<tr class=k_form><td align=\"right\">
			".label("Show currency name").":</td>
		<td colspan=\"2\"><INPUT TYPE=\"checkbox\" NAME=\"WALUTY[show_names]\" $c4 value=\"1\"></td></tr>
		<tr class=k_form><td align=\"right\">
			".label("Show table no").":</td>
		<td colspan=\"2\"><INPUT TYPE=\"checkbox\" NAME=\"WALUTY[show_tableno]\" $c5 value=\"1\"></td></tr>

		<tr class=k_form><td align=\"right\">
			<INPUT TYPE=\"hidden\" NAME=\"liczba_walut\" value=\"$liczba_walut\">
			".label("Currencies source file").":</td>
		<td colspan=\"2\"><INPUT class=\"k_input\" TYPE=\"text\" NAME=\"plik_walut\" value=\"$curr_file\" style=\"width:250px\"></td></tr>
		<TR class=k_form>
			<td colspan=\"3\" align=\"right\"><img src=\"img/i_save_n.gif\" style=\"cursor:hand\" onClick=\"ZapiszZmiany()\"></td>
		</TR></TABLE>";

?>