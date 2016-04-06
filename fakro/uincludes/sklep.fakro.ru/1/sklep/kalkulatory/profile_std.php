<?
	
	$sql = "SELECT * FROM towar WHERE to_id = ".$LIST[id];
	parse_str(ado_query2url($sql));
	$sql = "SELECT * FROM towar_parametry WHERE tp_to_id = ".$LIST[id];
	parse_str(ado_query2url($sql));
	$first_m = $WM->kwant_towaru($LIST[id]);
	
	if ($AUTH[id] > 0)
	{
		$button_label = sysmsg("button_cart","buttons");
		$fun = "opener.calcAddToCart(".$LIST[id].",document.calc._q.value);window.close()";
	}
	else
	{
		$button_label = sysmsg("button_ofert","buttons");
		$fun = "opener.chageItemQuantity(".$LIST[id].",document.calc._q.value,$first_m,1,'".sysmsg("$to_jm","cart")."');window.close()";
	}
	
	if (!strlen($tp_l)) $tp_l = $tp_b*1000;
	//if (!strlen($tp_m_jm)) $tp_m_jm = $tp_m_m;

	if (!strlen($first_m) || !strlen($tp_l) || !$first_m || !$tp_l || !$tp_m_jm || !strlen($tp_m_jm))
	{
		echo "
		<TABLE width=\"100%\" class=\"kalk\" cellspacing=0 cellpadding=0>
		<TR>
			<TD align=\"center\">".sysmsg("Calculator not avalible.","magazyn")."</TD>
		</TR>
		<TR>
			<TD align=\"center\"><INPUT TYPE=\"button\" value=\"".sysmsg("button_close","buttons")."\" onClick=\"window.close()\"></TD>
		</TR>
		</TABLE>";
		return;
	}


	$forma = "
	<FORM METHOD=POST ACTION=\"$self\" name=\"calc\">
	<TABLE width=\"100%\" class=\"kalk\" cellspacing=0 cellpadding=0>
	<thead>
	<TR>
		<TD colspan=\"3\" background=\"$SKLEP_IMAGES/sb/back_head0.jpg\">
		<div class=\"tt\">".sysmsg("Calculator","system")." ".$to_indeks."</div>
		<div class=\"st\">".sysmsg("tp_l","magazyn")." : $tp_l; ".sysmsg("tp_m_m","magazyn")." : $tp_m_jm</div>
		</TD>
	</TR>
	</thead>
	<tbody>
	<TR>
		<TD class=\"cl\">".sysmsg("tp_$to_jm","magazyn")."</TD>
		<TD class=\"cl\">".sysmsg("quantity","magazyn")."</TD>
		<TD class=\"cl\">".sysmsg("tp_l","magazyn")."</TD>
	</TR>
	<TR>
		<TD class=\"ci\"><INPUT TYPE=\"text\" NAME=\"_q\" value=\"$first_m\" onChange=przelicz(0) style=\"width:50px\"> ".sysmsg("to_jm","magazyn")."</TD>
		<TD class=\"ci\"><INPUT TYPE=\"text\" NAME=\"_i\" value=\"1\" onChange=przelicz(1) style=\"width:50px\"> ".sysmsg("pieces","magazyn")."</TD>
		<TD class=\"ci\"><INPUT TYPE=\"text\" NAME=\"_d\" value=\"1\" onChange=przelicz(2) style=\"width:50px\"> ".sysmsg("tp_mb","magazyn")."</TD>
	</TR>
	</tbody>
	<tfoot>
	<TR>
		<TD align=\"right\" colspan=\"3\"><INPUT TYPE=\"button\" value=\"".sysmsg("button_count","buttons")."\">
		<INPUT TYPE=\"button\" value=\"".sysmsg("button_close","buttons")."\" onClick=\"window.close()\">
		<INPUT TYPE=\"button\" value=\"$button_label\" onClick=\"$fun\"></TD>
	</TR>
	</tfoot>
	</TABLE>
	</FORM>
	";

	echo win2iso($forma);
?>
<script>
function przelicz(co)
{	

	subd = document.calc._d.value;
	subq = document.calc._q.value;
	subi = document.calc._i.value;

	subd = subd.replace(",",".");
	subq = subq.replace(",",".");
	subi = subi.replace(",",".");

	document.calc._d.value = subd;
	document.calc._q.value = subq;
	document.calc._i.value = subi;

	if (co==0) // zmieniono wage
	{

		if (isNaN(subq) || subq == '') 
		{
			alert('<? echo sysmsg("Wrong value","system") ?>');
			return;
		}

		document.calc._d.value=document.calc._q.value / <? echo $tp_m_jm ?>;
		document.calc._i.value=document.calc._d.value / <? echo ($tp_l/1000) ?>;
	}
	if (co==1) // ilosc
	{

		if (isNaN(subi) || subi == '') 
		{
			alert('<? echo sysmsg("Wrong value","system") ?>');
			return;
		}

		document.calc._d.value=document.calc._i.value * <? echo ($tp_l/1000) ?>;		
		document.calc._q.value=document.calc._d.value * <? echo $tp_m_jm ?>;
	}
	if (co==2) // dlugosc
	{

		if (isNaN(subd) || subd == '') 
		{
			alert('<? echo sysmsg("Wrong value","system") ?>');
			return;
		}

		document.calc._q.value=document.calc._d.value * <? echo $tp_m_jm ?>;
		document.calc._i.value=document.calc._d.value / <? echo ($tp_l/1000) ?>;
	}

	document.calc._q.value=Math.floor(10*document.calc._q.value)/10;
	document.calc._d.value=Math.round(10*document.calc._d.value)/10;
	document.calc._i.value=Math.round(document.calc._i.value);
	document.calc._d.value=document.calc._i.value * <? echo ($tp_l/1000) ?>;		
	document.calc._d.value=Math.round(10*document.calc._d.value)/10;
}

przelicz(0);

</script>
