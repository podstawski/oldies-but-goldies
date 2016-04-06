<?
	include_once("include/fun.h");
	include_once("include/xml_fun.h");

	$c_array = explode(";",$costxt);
	
	$ilosc_wpisow = count($c_array);

	if ($ilosc_wpisow < 3) return;
////
	$ilosc_w = $c_array[1];
	$ilosc_w+=2;
	if ($ilosc_w > 2) 
		for ($i=2; $i < $ilosc_w; $i++)
			parse_str($c_array[$i]);
//////
	
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

	if (strlen($c_array[0])) $curr_file = $c_array[0];
		else $curr_file = "http://adv.gammanet.pl/waluty/index.g";
	
	$plik = file($curr_file);
	
	$xml_string=implode("\n",$plik);
	
	$waluty = xml2obj($xml_string);
	
	$tabela = $waluty->tabela_kursow->numer_tabeli;
	$data = $waluty->tabela_kursow->data_poczatkowa;
	if (!strlen($data)) $data = $waluty->tabela_kursow->data_publikacji;

	echo "<span class=\"api2_curr_date\">".label("Date")." : ".FormatujDate($data)."</span>";
	if ($show_tableno)
		echo "<br><span class=\"api2_curr_tabela\">".label("Table no")." : $tabela</span>";

	echo "<TABLE class=\"api2_curr_table\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\">";
	if ($show_header) 
	{
		echo "
				<tr class=\"api2_curr_head_tr\">";
		if ($show_country) 
			echo "<td class=\"api2_curr_head_td_country\">".label("Country")."</td>";
		echo "
				<td class=\"api2_curr_head_td_count\">".label("Currency unit")."</td>
				<td class=\"api2_curr_head_td_code\">".label("Currency code")."</td>";
			if ($show_names) 
			echo "
				<td class=\"api2_curr_head_td_code\">".label("Currency name")."</td>";
		echo "
				<td class=\"api2_curr_head_td_value\">".label("Average course ")."</td>
			</tr>";

	}
	if ($show_pln) $PLN = "PLZ";
/*
	for ($i=0; $i < count($waluty->tabela_kursow->pozycja); $i++)
	{
		$kod = $waluty->tabela_kursow->pozycja[$i]->kod_waluty;
		$kraj = $waluty->tabela_kursow->pozycja[$i]->nazwa_kraju;
		$kurs = $waluty->tabela_kursow->pozycja[$i]->kurs_sredni;
		$kurs = eregi_replace(",",".",$kurs);
		$jednostka = $waluty->tabela_kursow->pozycja[$i]->jednostka_waluty;
		if (!strlen($jednostka)) $jednostka = $waluty->tabela_kursow->pozycja[$i]->przelicznik;
		$checked = "";
		$nr = $i + 1;
		if ($c_array[$nr])
		{
			if ($style_name == "api2_curr_tr")
				$style_name = "api2_curr_tr2";
			else $style_name = "api2_curr_tr";

			echo "<tr class=\"$style_name\">";
			if ($show_country) 
				echo "<td class=\"api2_curr_td_country\">$kraj</td>";
			echo "
					<td class=\"api2_curr_td_count\">$jednostka</td>
					<td class=\"api2_curr_td_code\">$kod</td>";
			if ($show_names) 
			echo "
					<td class=\"api2_curr_td_code\">".label("$kod")."</td>";

			echo "
					<td class=\"api2_curr_td_value\">".sprintf("%01.4f",$kurs)." $PLN</td>
				</tr>";
		}
	}
*/

	for ($i=0; $i < count($waluty->tabela_kursow->pozycja); $i++)
	{
		$wal_kod = $waluty->tabela_kursow->pozycja[$i]->kod_waluty;
		$kod = $wal_kod;
		$kraj = $waluty->tabela_kursow->pozycja[$i]->nazwa_kraju;
		$kurs = $waluty->tabela_kursow->pozycja[$i]->kurs_sredni;
		$kurs = eregi_replace(",",".",$kurs);
		$jednostka = $waluty->tabela_kursow->pozycja[$i]->jednostka_waluty;
		if (!strlen($jednostka)) $jednostka = $waluty->tabela_kursow->pozycja[$i]->przelicznik;

		if ($$kod)
		{
			if ($style_name == "api2_curr_tr")
				$style_name = "api2_curr_tr2";
			else $style_name = "api2_curr_tr";

			echo "<tr class=\"$style_name\">";
			if ($show_country) 
				echo "<td class=\"api2_curr_td_country\">$kraj</td>";
			echo "
					<td class=\"api2_curr_td_count\">$jednostka</td>
					<td class=\"api2_curr_td_code\">$kod</td>";
			if ($show_names) 
			echo "
					<td class=\"api2_curr_td_code\">".label("$kod")."</td>";

			echo "
					<td class=\"api2_curr_td_value\">".sprintf("%01.4f",$kurs)." $PLN</td>
				</tr>";
		}
	}

	echo "</table>";
?>