<?

$all = $FORM[all];
if (!$all)
	$ADD = "AND ts_aktywny = 1";


if (!function_exists("iso2win"))
{
	function iso2win($str)
	{
		$str=ereg_replace("Ą","Ľ",$str);
		$str=ereg_replace("Ś","",$str);
		$str=ereg_replace("Ź","",$str);
		$str=ereg_replace("ą","š",$str);
		$str=ereg_replace("ś","",$str);
		$str=ereg_replace("ź","",$str);
		return ($str);
	}
} 

 function u_Format ($c)
 {
  return number_format(round($c,2),4,".","");
 }

	
	$kodowanie = 1250; //win
	$magazyn = "MAG";
	$podmiot = $FORM[podmiot];

	$sql = "SELECT * FROM towar,towar_sklep
			WHERE to_id = ts_to_id $ADD
			ORDER BY upper(to_indeks)";

	$res = $projdb->execute($sql);
	
	$towary = "";
	$ceny = "";

	for ($i=0;$i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));

		$to_opis_m_i = str_replace("\r","",$to_opis_m_i);
		$to_opis_m_i = str_replace("\n","\r\n",$to_opis_m_i);
		$to_opis_m_i = substr($to_opis_m_i,0,254);
		$to_opis_m_i = $to_nazwa;

		if (!strlen(trim($to_jm)))
			$to_jm = "szt.";

		if (!strlen(trim($to_vat)))
			$to_vat = 22;

		$to_indeks = strtoupper($to_indeks);

		$towary.= "1,\"$to_indeks\",,,\"$to_nazwa\",\"$to_opis_m_i\",\"$to_nazwa\",,,\"$to_jm\",\"$to_vat\",".u_Format($to_vat).",\"$to_vat\",".u_Format($to_vat).",0.0000,0.0000,\"$to_jm\",0,,,\"$to_jm\",$sm_stan_min,0,,,0,\"$to_jm\",0.0000,0.0000,,1,,0,0,,,,,,,, \n";
		$ceny.= "\"$to_indeks\",\"Detaliczna\",".u_Format($ts_cena*1.1).",".u_Format($ts_cena*1.342).",".u_Format(10).",".u_Format(9.09).",".u_Format($ts_cena*0.1)." \n";
		$ceny.= "\"$to_indeks\",\"Hurtowa\",".u_Format($ts_cena*1.05).",".u_Format($ts_cena*1.281).",".u_Format(5).",".u_Format(4.76).",".u_Format($ts_cena*0.05)." \n";
		$ceny.= "\"$to_indeks\",\"Specjalna\",".u_Format($ts_cena*1.03).",".u_Format($ts_cena*1.2566).",".u_Format(3).",".u_Format(2.91).",".u_Format($ts_cena*0.03)." \n";
	}

$d = date("YmdHis");

$fc = "[INFO]
\"1.05\",3,$kodowanie,\"Gammanet - Wirtualny Magazyn\",\"$podmiot\",\"$podmiot\",\"$podmiot\",,,,,\"$magazyn\",\"Główny\",\"Magazyn główny\",,0,,,\"Szef\",$d,\"Polska\",\"PL\",,0

[NAGLOWEK]
\"TOWARY\"

[ZAWARTOSC]
$towary
[NAGLOWEK]
\"CENNIK\"

[ZAWARTOSC]
$ceny
";


if (!$KAMELEON_MODE)
{
	Header('Content-Type: application/epp');
	Header('Content-Length: '.strlen($fc));
	Header('Content-disposition: attachment; filename=towary.epp');
	echo iso2win($fc);
	exit();
}
else
{
	$plk = fopen("$UFILES/eksport.epp","w");
	fwrite($plk,iso2win($fc));
	fclose($plk);
	echo "<a href=\"$UFILES/eksport.epp\">$UFILES/eksport.epp</a>";
}

?>
