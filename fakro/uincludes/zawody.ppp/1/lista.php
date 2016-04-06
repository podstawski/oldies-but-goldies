<br><br>
<?
setlocale(LC_ALL, 'pl_PL.ISO8859-2');

$data_zawody = '2011';
?>
<?
$idb->query("SELECT * FROM zawody_zgloszenie WHERE zawody = '".$data_zawody."' AND potwierdzenie = 1");

if($idb->rowcount() == 0) {
?>
<div align="center"><font color="#FF0000">Brak danych</font></div>


<SCRIPT LANGUAGE="JavaScript">
/*
	setTimeout('checkBrowser()',60*40);
	function checkBrowser() {
		window.location.href="./?";
		}
*/
</SCRIPT>
<?
	}else{
?>
<table width="570" border="0" cellspacing="3" cellpadding="3">
<tr>
	<td width="20"></td>
	<td width="200"><strong>Imię Nazwisko</strong></td>
	<td width="350"><strong>Firma</strong></td>
</tr>
<?
	for($i = 0; $i < $idb->rowcount(); $i++) {
		$idb->getvalues();
?>
<tr>
	<td align="right"><? echo ++$id; ?>.</td>
	<td><? echo $idb->row['imie']; ?> <? echo $idb->row['nazwisko']; ?></td>
	<td><? echo $idb->row['firma']; ?></td>
</tr>
<?
		}
?>
<tr>
	<td colspan="3" align="right">Lista jest aktualizowana automatycznie</td>
</tr>
</table>
<?
	}
?>

<br><br><br>