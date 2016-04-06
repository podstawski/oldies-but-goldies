<?
	$LIST[id]=$CIACHO[admin_to_id];

	$to_id = $LIST[id];
	$killId = $LIST[killKat];

	if (!strlen($to_id)) return;
	
	$sql = "SELECT * FROM towar WHERE to_id = $to_id ";
	parse_str(ado_query2url($sql));

	if (!strlen($to_vat))
		$to_vat = 0;

	$sql = "SELECT * FROM towar_sklep,sklep WHERE ts_to_id = $to_id 
		AND ts_sk_id=sk_id";
	$res = $adodb->execute($sql);

	$vis = "";
	if (!$SYSTEM[czas])
		$vis = "style=\"display:none\"";

	$table = "
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<tr>
		<th>".sysmsg("Shop","admin")."</th>
		<th>".sysmsg("Quant","admin")."</th>
		<th title=\"".sysmsg("Sort priority","admin")."\">PRI</th>
		<th title=\"".sysmsg("Sort priority","admin")." 2\">PRI2</th>
		<th $vis>".sysmsg("Cart time","admin")."</th>
		<th title=\"".sysmsg("Stack controll","admin")."\">ST</th>
		<th>".sysmsg("Price","admin")."</th>
		<th>".sysmsg("Gross","admin")."</th>
	</tr>
	</thead>
	<tbody>
	";
	$lst = "";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		$chck = "";
		parse_str(ado_explodename($res,$i));				
		if ($ts_magazyn) $chck = "checked";

		$lst.= ";$ts_id";

		$vis = "";
		if (!$SYSTEM[czas])
			$vis = "style=\"display:none\"";

		$brutto = $ts_cena + ($ts_cena * ($to_vat/100));
		$brutto = round($brutto,2);
		$table.= "
		<tr>
			<td class=\"c2\">$sk_nazwa</td>
			<td class=\"c2\"><INPUT TYPE=\"text\" NAME=\"SKLEP_KW[$ts_id]\" value=\"$ts_kwant_zam\" style=\"width:40px\"></td>
			<td class=\"c2\"><INPUT TYPE=\"text\" NAME=\"SKLEP_PRI[$ts_id]\" value=\"$ts_pri\" style=\"width:25px\"></td>
			<td class=\"c2\"><INPUT TYPE=\"text\" NAME=\"SKLEP_PRI2[$ts_id]\" value=\"$ts_pri2\" style=\"width:25px\"></td>
			<td $vis class=\"c2\"><INPUT TYPE=\"text\" NAME=\"SKLEP_CZ[$ts_id]\" value=\"$ts_czas_koszyk\" style=\"width:50px\"> s.</td>
			<td class=\"c2\"><INPUT TYPE=\"checkbox\" NAME=\"SKLEP_MG[$ts_id]\" $chck value=\"1\"></td>
			<td class=\"c2\"><INPUT TYPE=\"text\" NAME=\"SKLEP_CE[$ts_id]\" id=\"cnt$ts_id\" onChange=\"changeNt(this.value,'cbr$ts_id')\" value=\"$ts_cena\" style=\"width:50px\"></td>
			<td class=\"c4\"><INPUT TYPE=\"text\" id=\"cbr$ts_id\" onChange=\"changeBr(this.value,'cnt$ts_id')\" value=\"$brutto\" style=\"width:50px\"></td>
		</tr>
		";
	}
	$lst = substr($lst,1);

	$table.= "</tbody>
	<tfoot>
	<tr>
		<td class=\"c4\" colspan=\"7\"><INPUT TYPE=\"submit\" class=\"button\" value=\"".sysmsg("Save","admin")."\"></td>
	</tr>
	</tfoot></TABLE>
	";

	echo "
	<FORM METHOD=POST ACTION=\"$next\" method=\"POST\" name=\"gobackform\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	</form>
	<FORM METHOD=POST ACTION=\"$next\" method=\"POST\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"SklepTowarZapisz\">
	<INPUT TYPE=\"hidden\" name=\"SKLEPY\" value=\"$lst\">
	$table
	</FORM>	
	";


	$query="SELECT * FROM sklep ORDER BY sk_nazwa";
	$res=$projdb->Execute($query);

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		$ts_id=0;
		$ts_aktywny=0;
		$query="SELECT ts_id,ts_aktywny FROM towar_sklep 
				WHERE ts_to_id=$to_id AND ts_sk_id=$sk_id";	
		parse_str(ado_query2url($query));

		$ch=$ts_aktywny?"checked":"";

		echo "<input type=\"checkbox\" $ch value=\"$sk_id\" 
			onClick=\"towarSklep(this)\"> $sk_nazwa<br>";
	}

	$method=$KAMELEON_MODE?"POST":"GET";
?>

<form name="TowarSklep" action="<?echo $self?>" method="<?echo $method; ?>">
<input type="hidden" name="list[to_id]" value="<?echo $to_id?>">
<input type="hidden" name="list[sk_id]" id="ts_sk_id">
<input type="hidden" name="action" value="TowarSklep">
</form>


<script>

	function towarSklep(obj)
	{
		if (!obj.checked) if (!confirm("Na pewno ?")) 
		{
			obj.checked="true";
			return;
		}
		
		document.TowarSklep.ts_sk_id.value=obj.value;
		document.TowarSklep.submit();
	}
	
	var to_vat = <? echo $to_vat ?>;

	function changeBr(val,id)
	{
		val = val*1;
		netto = ((val * 100) / (100 + to_vat));
		netto = Math.round(netto*100)/100;
		nt_v = getObject(''+id+'');
		nt_v.value = netto;

	}

	function changeNt(val,id)
	{
		val = val*1;
		brutto = val + (val * (to_vat/100));
		brutto = Math.round(brutto*100)/100;
		br_v = getObject(''+id+'');
		br_v.value = brutto;
	}

</script>

