<?
	if (!strlen($AUTH[parent])) return;
	
	$ko_rez_data = $LIST[ko_rez_data];
	if (!strlen($ko_rez_data)) return;
	$sql = "SELECT * FROM koszyk WHERE
			ko_su_id = ".$AUTH[parent]." 
			AND ko_rez_data = $ko_rez_data
			AND (ko_deadline > $NOW OR ko_deadline IS NULL)
			ORDER BY ko_id";

	$res = $adodb->execute($sql);
	
	if (!$res->RecordCount())
		return;

	if ($AUTH[p_price])
		$add_column = "<Th>".sysmsg("Price","system")."</Th>
		<Th>".sysmsg("Value","system")."</Th>";

	$table= "
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>		
		<Th>".sysmsg("Lp.","system")."</Th>
		<Th>".sysmsg("Article Id.","system")."</Th>
		<Th>".sysmsg("Article sizes","system")."</Th>
		<Th>".sysmsg("Quantity","system")."</Th>
		$add_column		
	</TR>
	</thead>
	<tbody>	
	";
	$total_quant = 0;
	$total_value = 0;
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
	
		$sql = "SELECT to_indeks, to_id, to_nazwa FROM towar_sklep, towar WHERE
				ts_id = $ko_ts_id AND ts_to_id = to_id AND ts_sk_id = $SKLEP_ID";
		parse_str(ado_query2url($sql));
		
		$buttons = "<img src=\"$UIMAGES/autoryzacja/i_delete_n.gif\" onClick=\"deleteItem('$ko_id')\" style=\"cursor:hand\" alt=\"".sysmsg("Delete article from cart","system")."\">";

		$_tr = " class=t1";
		if (($i+1)%2) $_tr = " class=t2";
		$cena = system_cena($SKLEP_ID,$to_id,$ko_ilosc,$AUTH[parent]);
		if ($AUTH[p_price])
			$add_column = "<TD class=\"c2\" nowrap align=\"right\">".u_cena($cena)."</TD>
			<TD class=\"c2\" nowrap align=\"right\">".u_cena($cena*$ko_ilosc)."</TD>";

		$link = $next_char."list[to_id]=$to_id";				

		$table.= "
		<TR $_tr>
			<TD class=\"c2\">".($i+1)."</TD>
			<TD class=\"c2\" title=\"$to_nazwa\" onClick=\"opisProduktu('$link')\" style=\"cursor:hand\">$to_indeks</TD>
			<TD class=\"c2\">".towar_wymiary($to_id)."</TD>
			<TD class=\"c2\">$ko_ilosc</TD>
			$add_column			
		</TR>
		";

		$total_quant+= $ko_ilosc;
		$total_value+= ($cena*$ko_ilosc);
	}
	$head = "
	<TABLE>
	<TR>
		<TD>".sysmsg("Reservation date","system")."</TD>
		<TD>:<B>".date("d-m-Y H:i:s",$ko_rez_data)."</B></TD>
	</TR>
	<TR>
		<TD>".sysmsg("Reservation number","system")."</TD>
		<TD>:<B>$ko_rez_nr</B></TD>
	</TR>
	<TR>
		<TD valign=\"top\">".sysmsg("Notice","system")."</TD>
		<TD>:<B>".nl2br(stripslashes($ko_rez_uwagi))."</B></TD>
	</TR>
	</TABLE>";

		$colspan = 4;
		if ($AUTH[p_price])
		{
			$add_column = "<TD class=\"c4\">&nbsp;</TD>
			<TD class=\"c4\" align=\"right\"><B>".u_cena($total_value)."</B></TD>";
			$colspan = 6;
		}
				

		$sql = "SELECT COUNT(ko_ilosc) AS cart_count FROM koszyk WHERE
				ko_su_id = ".$AUTH[id]." AND ko_rez_data IS NULL 
				AND (ko_deadline > $NOW OR ko_deadline IS NULL)";
		parse_str(ado_query2url($sql));
		
		if (!$cart_count)
			$subbutton = "<INPUT TYPE=\"button\" onClick=\"moveToCart()\" value=\"".sysmsg("Move to cart","system")."\">";

		if ($AUTH[p_order])
			$order_button="<INPUT TYPE=\"button\" onClick=\"moveToOrder()\" value=\"".sysmsg("Prepare order","system")."\">";

		$movebutton = "
			<tr>
				<TD class=\"c4\" colspan=\"$colspan\" align=\"right\">
				$order_button
				$subbutton
				</TD>
			</tr>
			<FORM METHOD=POST ACTION=\"$next\" name=\"moveForm\">
			<INPUT TYPE=\"hidden\" name=\"action\" value=\"RezerwacjaNaKoszyk\">
			<INPUT TYPE=\"hidden\" name=\"form[ko_rez_data]\" value=\"$ko_rez_data\">
			</FORM>
			<FORM METHOD=POST ACTION=\"$more\" name=\"moveOrderForm\">
			<INPUT TYPE=\"hidden\" name=\"form[ko_rez_data]\" value=\"$ko_rez_data\">
			</FORM>
			";


		$table.= "
		</tbody>
		<tfoot>
		<TR $_tr>
			<TD class=\"c4\">&nbsp;</TD>
			<TD class=\"c4\">&nbsp;</TD>
			<TD class=\"c4\" align=\"right\"><B>".sysmsg("Total","cart").":</B></TD>
			<TD class=\"c4\"><B>$total_quant</B></TD>
			$add_column			
		</TR>
		$movebutton
		</tfoot>
		";

	$table.= "</table>
	<script src=\"$SKLEP_INCLUDE_PATH/js/scripts.js\"></script>
	";

	echo $head.$table;
?>
<script>

	function moveToCart()
	{
		if (confirm('<? echo sysmsg("Are You sure, You want to move this reservation to cart ?","system") ?>'))
			document.moveForm.submit();
	}

	function moveToOrder()
	{
		document.moveOrderForm.submit();
	}

</script>
