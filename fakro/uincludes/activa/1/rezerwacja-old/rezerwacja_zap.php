<?
$id = $_SESSION['rezerwacja']['id'];
?>

<?
$result_daty = $idb->getvalues($idb->query("SELECT daty.id, daty.od, daty.do, daty.data_pytania FROM daty, typy_pokoi WHERE daty.id='".$id."'"));

$_daty_od = str_replace('-','', $result_daty["od"]);
$_daty_do = str_replace('-','', $result_daty["do"]);

$_ilosc_nocy = ($_daty_do-$_daty_od);
?>


<table width="100%" border="0" cellspacing="2" cellpadding="2" class="rezerwacja">
<form method="post" action="<?=$_action;?>">
<input type="hidden" name="mode" value="zapis">
<col>
<col width="100">
<col width="80">
<tr>
    <td colspan="3">Przyjazd: <strong><?=$result_daty["od"];?></strong>, Ilość nocy: <?=$_ilosc_nocy;?><br></td>
</tr>
<tr class="bg_naglowek">
    <td>Nazwa pokoju</td>
    <td align="center">Ilość pokoi</td>
    <td align="center">Cena</td>
</tr>
<?
$do_razem = '0';
$do_razem_cena = '0';
$nazwa_typ_pokoju = '0';
$nazwa_ilosc = '0';

$result_tp = $idb->query("SELECT * FROM typy_pokoi WHERE id_pytania = '".$id."'");
for($i = 0; $i < $idb->rowcount(); $i++) {
	$idb->getvalues();
	
	$ilosc = $_POST['pokoj'][$idb->row['id_tp']];
	
	if($ilosc){
		$cena = (($ilosc*$idb->row['cena'])*$_ilosc_nocy);
		$cena_razem = $cena_razem+$cena;
		$nazwa_typ_pokoju = $nazwa_typ_pokoju+'1';
?>
<tr class="<? echo (($i && ($i%2))?"bg1":"bg0"); ?>">
    <td>
	<input type="hidden" size="1" name="a" value="<?=$idb->row['id_tp'];?>">
	<input type="hidden" name="<?=$nazwa_typ_pokoju;?>" value="<?=$idb->row['typ_pokoju'];?>">
	<input type="hidden" name="<?=$nazwa_ilosc=$nazwa_ilosc+'100';?>" value="<?=$ilosc;?>">
	<?=$idb->row['typ_pokoju'];?></td>
    <td align="center"><?=$ilosc;?></td>
    <td align="center"><?=$cena;?> PLN</td>
</tr>
<? } ?>
<? } ?>
<tr>
    <td colspan="2" align="right">Razem:</td>
    <td align="center"><strong><?=$cena_razem;?> PLN</strong></td>
</tr>
</table>

<br>

<table width="100%" border="0" cellspacing="2" cellpadding="2" class="rezerwacja">
<col width="40%">
<col width="60%">
<tr class="formularz">
	<td>Imię:</td>
	<td><input style="width: 100%;" name="kontakt[name]" type="text" /></td>
</tr>
<tr class="formularz">
	<td>Nazwisko:</td>
	<td><input style="width: 100%;" name="kontakt[surname]" type="text" /></td>
</tr>
<tr class="formularz">
	<td>Adres:</td>
	<td><input style="width: 100%;" name="kontakt[ulica]" type="text" /></td>
</tr>
<tr class="formularz">
	<td>Miasto:</td>
	<td><input style="width: 30%;" name="kontakt[miasto]" type="text" /></td>
</tr>
<tr class="formularz">
	<td>Kod pocztowy:</td>
	<td><input style="width: 30%;" name="kontakt[kod]" type="text" /></td>
</tr>
<tr class="formularz">
	<td>E-mail:</td>
	<td><input style="width: 100%;" name="kontakt[mail]" type="text" /></td>
</tr>
<tr class="formularz">
	<td>Telefon:</td>
	<td><input style="width: 100%;" name="kontakt[telefon]" type="text" /></td>
</tr>
<tr class="formularz">
	<td>Preferowana forma kontaktu:</td>
	<td><select style="width: 30%;" name='kontakt[preferencje]'><option>telefon</option><option>e-mail</option></select></td>
</tr>

<tr class="formularz">
	<td colspan="2">
	<br>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
<tr>
	<td valign="top" align="right"><p><input type="checkbox" name="ZGODA[zgoda]" value="tak" /></p></td>
	<td>Wyrażam zgodę na wykorzystywanie i przetwarzanie przez Hotel Aktiv sp. z o.o. z siedzibą w Muszynie przy ul. Złockie 78, moich danych osobowych zawartych w tym formularzu w celach marketingowych, zgodnie z ustawą z dn. 29.08.1997r. o ochronie danych osobowych (Dz. U. Nr 133, poz.883).</td>
</tr>
<tr>
	<td valign="top" align="right"><input type="checkbox" name="ZGODA[zgodaMarketing]" value="tak" /></td>
	<td>Wyrażam zgodę na otrzymywanie informacji promocyjnych, informacyjnych, reklamowych i marketingowych o produktach Hotel Aktiv sp. z o.o.&nbsp;33-370 Muszyna, ul. Złockie 78, na m&oacute;j adres e-mail i telefon zgodnie z ustawą z dnia 18.07.2002r. o świadczeniu usług drogą elektroniczną (Dz. U. Nr 144, poz. 1204).</td>
</tr>
</table>

	</td>
</tr>
<tr>
	<td align="right" colspan="2"><input style="width: 100px" type="submit" value="Wyślij rezerwację" /></td>
</tr>
</form>
</table>
<br>


