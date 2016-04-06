<?
if ($AUTH[id]<=0)
{
	$error="brak usera";
	return;
}

$adodb->debug=0;

$sql = "SELECT * FROM koszyk WHERE
		ko_su_id = ".$AUTH[id]." 
		AND ko_rez_data IS NULL 
		AND (ko_deadline > $NOW OR ko_deadline IS NULL)
		ORDER BY ko_id";
$res = $adodb->execute($sql);

for($i=0; $i < $res->RecordCount(); $i++) {
	parse_str(ado_explodename($res,$i));
	
	$sql_1 = "SELECT * FROM towar_sklep LEFT JOIN towar ON ts_to_id = to_id LEFT JOIN towar_parametry ON ts_to_id = tp_to_id WHERE ts_id = $ko_ts_id AND ts_sk_id = $SKLEP_ID";
	$res_1 = $adodb->execute($sql_1);
	for($i_1=0; $i_1 < $res_1->RecordCount(); $i_1++) {
		parse_str(ado_explodename($res_1,$i_1));
		
		$waga = ($ko_ilosc*$to_waga);
		$objetosc = ($ko_ilosc*$to_objetosc);
		
		$_waga += $waga;
		$_objetosc += $objetosc;
		}
	}

##---------------------------------------------------------------
$sql = "SELECT su_stan FROM system_user WHERE
		su_id = ".$AUTH[id]."";
$res = $adodb->execute($sql);
parse_str(ado_explodename($res,0));

$sql = "SELECT *
		FROM
		tr_ceny,tr_typ,tr_strefa
		WHERE
		tr_ceny.tr_typ_id = tr_typ.tr_typ_id AND
		tr_ceny.tr_strefa_typ = tr_strefa.tr_strefa_typ AND
		tr_strefa.tr_strefa_name = '$su_stan' AND
		((tr_ceny.tr_waga_od <= $_waga AND tr_ceny.tr_waga_do >= $_waga) AND (tr_ceny.tr_objetosc_od <= $_objetosc AND tr_ceny.tr_objetosc_do > $_objetosc))
		ORDER BY tr_typ.tr_typ_id";
$res = $adodb->execute($sql);
?>

<FORM METHOD=POST ACTION="<?=$next;?>" name="offerForm" onsubmit="return sprawdzCheckboxy(this)">

<table class="list_table">
<col class="cw cgray" align="right"><col class="cw cgray" align="right"><col class="cw cgray">
<tfoot align="right">
<?
for($i=0; $i < $res->RecordCount(); $i++) {
	parse_str(ado_explodename($res,$i));
?>
<tr>
	<td width="80%"><strong><?=$tr_typ_name;?></strong></td>
	<td width="10%"><em><?=$tr_ceny;?></em></td>
	<td width="10%"><input type="radio" id="transport" name="transport" value="<?=$tr_ceny_id;?>"></td>
</tr>
<?	} ?>
<tr>
	<td colspan="3">
	<input type="submit" value="<?=sysmsg('Go order','order');?>" class="button_o">
	</td>
</tr>
</tfoot>
</table>
</form>

<script language="JavaScript" type="text/javascript">
function sprawdzCheckboxy(f) {
	var transportChecked 
	
	if(f.transport.length > 1) {
		for(var i=0; i< f.transport.length; i++) {
			if (f.transport[i].checked) {
				transportChecked = f.transport[i].value
				}
			}
		if(!transportChecked) {
			alert("<?=sysmsg('Payment type not chosen','order');?>");
			return false;
			}
		}else{
		if (f.transport.checked) {
				transportChecked = f.transport.value
				}
		if(!transportChecked) {
			alert("<?=sysmsg('Payment type not chosen','order');?>");
			return false;
			}
		}
	}
</script>
