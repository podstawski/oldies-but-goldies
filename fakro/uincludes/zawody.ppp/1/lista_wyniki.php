<?
global $lang,$ver,$CHARSET_TAB,$WEBTD,$SERVER_ID;
global $lang;

$tab = unserialize(stripslashes($costxt));
################
if(is_array($tab)) {
	$fakro = $tab["fakro"];
	$plik = $UFILES.'/wyniki/'.$fakro['data'].'/'.$fakro['plik'];
	
	if(@file_exists($plik)) {
		require_once("$INCLUDE_PATH/lib/Excel/reader.php");
		
		$xls = new Spreadsheet_Excel_Reader();
		$xls->setOutputEncoding('iso-8859-2');
		$xls->read($plik);
		
		echo '<table class="list_table">';
		echo '<col width="40">';
		echo '<col width="220">';
		echo '<col width="220">';
		echo '<col width="100">';
		echo '<thead>';
		echo '<tr>';
		echo '<td>Miejsce</td>';
		echo '<td>Imię i nazwisko</td>';
		echo '<td>Organizacja</td>';
		echo '<td>Czas przejazdu</td>';
		echo '</tr>';
		echo '</thead>';
		
		echo '<tbody>';
		
		$color = 0;
		
		for($i = 1; $i <= $xls->sheets[0]['numRows']; $i++) {
			$color_row = (($color++)%2)?1:0;
			echo '<tr class="bg'.$color_row.'">';
			echo '<td>'.$xls->sheets[0]['cells'][$i][1].'&nbsp;</td>';
			echo '<td>'.$xls->sheets[0]['cells'][$i][5].' '.$xls->sheets[0]['cells'][$i][2].' '.$xls->sheets[0]['cells'][$i][3].'</td>';
			echo '<td>'.$xls->sheets[0]['cells'][$i][6].'</td>';
			echo '<td>'.$xls->sheets[0]['cells'][$i][4].'</td>';
			echo '</tr>';
			}
		echo '</tbody>';
		echo '</table>';
		}
	}
?>