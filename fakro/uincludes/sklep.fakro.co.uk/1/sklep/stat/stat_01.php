<?
$od=unixdate($_REQUEST[c_data_od],0);
$do=unixdate($_REQUEST[c_data_do],1)-1;

$query="SELECT to_nazwa, COUNT(towar.to_nazwa) AS ilosc, SUM(zampoz.zp_cena*zampoz.zp_ilosc) AS wartosc
		FROM zamowienia,zampoz,towar
		WHERE zamowienia.za_status = '-1' AND zampoz.zp_za_id = zamowienia.za_id AND zampoz.zp_to_indeks = towar.to_indeks AND za_data_realizacji >= $od AND za_data_realizacji <= $do
		GROUP BY towar.to_nazwa
		ORDER BY towar.to_nazwa";
$res = $projdb->execute($query);

echo '<table class="list_table">';
echo '<col>';
echo '<col width="50"/>';
echo '<col width="50"/>';
echo '<thead>';
echo '<tr>';
echo '<td>group</td>';
echo '<td align="right">quantity</td>';
echo '<td align="right">value</td>';
echo '</tr>';
echo '</thead>';

for($s=0; $s < $res->RecordCount(); $s++) {
	parse_str(ado_Explodename($res,$s));
	
	echo '<tr>';
	echo '<td>'.$to_nazwa.'</td>';
	echo '<td align="right">'.$ilosc.'</td>';
	echo '<td align="right">'.u_cena($wartosc).'</td>';
	echo '</tr>';
	$_ilosc += $ilosc;
	$_wartosc += $wartosc;
	}

echo '<tr>';
echo '<td></td>';
echo '<td align="right"><strong>'.$_ilosc.'</strong></td>';
echo '<td align="right"><strong>'.u_cena($_wartosc).'</strong></td>';
echo '</tr>';
echo '</table>';
?>