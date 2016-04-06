<?
$id = $_SESSION['rezerwacja']['id'];

$result_daty = $idb->getvalues($idb->query("SELECT daty.id, daty.od, daty.do, daty.data_pytania FROM daty, typy_pokoi WHERE daty.id='".$id."'"));

$_daty_od = str_replace('-','', $result_daty["od"]);
$_daty_do = str_replace('-','', $result_daty["do"]);

$_ilosc_nocy = ($_daty_do-$_daty_od);
?>

<table width="100%" border="0" cellspacing="2" cellpadding="2" class="rezerwacja">
<col>
<col width="150">
<col width="100">
<col width="80">
<tr>
    <td colspan="4">
	Przyjazd: <strong><?=$result_daty["od"];?></strong>, Ilość nocy: <?=$_ilosc_nocy;?> <a href="<?=$_action;?>">(zmien daty)</a><br>
	Proszę wybrać ilość pokoi, które chcą Państwo zarezerwować.<br></td>
</tr>
<tr class="bg_naglowek">
    <td>Nazwa pokoju</td>
    <td align="center">Maksymalna ilość osób</td>
    <td align="center">Cena za pokój</td>
    <td align="center">Ilość pokoi</td>
</tr>
<form method="post" action="<?=$_action;?>">
<input type="hidden" name="mode" value="zap">
<?
$result_tp = $idb->query("SELECT * FROM typy_pokoi WHERE id_pytania = '".$id."'");
for($i = 0; $i < $idb->rowcount(); $i++) {
	$idb->getvalues();
	
	if($idb->row['cena'] != 0) {
?>
<tr class="<? echo (($i && ($i%2))?"bg1":"bg0"); ?>">
    <td><?=$idb->row['typ_pokoju'];?></td>
    <td align="center"><?=$idb->row['liczba_osob'];?></td>
    <td align="center"><?=$idb->row['cena'];?> PLN</td>
    <td align="center"><input type="text" name="pokoj[<?=$idb->row['id_tp'];?>]" size="5" maxlength="2"></td>
</tr>
<? } ?>
<? } ?>
<tr>
    <td colspan="4" align="right"><input type="submit" value="Zarezerwuj"></td>
</tr>
</form>
<tr>
    <td colspan="4"><br><strong>Cena pokoju zawiera:</strong> nocleg, śniadanie w formie szwedzkiego stołu, korzystanie z kompleksu saun, parking, podatek VAT.</td>
</tr>
</table>
