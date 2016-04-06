<?

	$sql = "SELECT * FROM ulubione WHERE ul_nazwa = '".$nazwa."' AND ul_su_id = ".$AUTH[id];
	$res = $adodb->execute($sql);
	if (!$res->RecordCount()) return;

	if ($AUTH[p_price])
		$add_column = "<Th>".sysmsg("Price","system")."</Th>
		<Th>".sysmsg("Value","system")."</Th>";

	$colspan = 1;
	if ($AUTH[p_price]) $colspan = 3;	

	$table= "
	<FORM METHOD=POST ACTION=\"$self\" name=\"prepareOrderForm\" onSubmit=\"return false;\">
	<INPUT TYPE=\"hidden\" NAME=\"list[nazwa]\" value=\"$nazwa\">
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>		
		<Th colspan=\"3\">".sysmsg("Favourit name","system")."</Th>
		<Th colspan=\"$colspan\"><INPUT TYPE=\"text\" NAME=\"list[ul_nazwa]\" value=\"$nazwa\"></Th>
		<Th><img src=\"$UIMAGES/autoryzacja/i_save_n.gif\" onClick=\"submit()\" style=\"cursor:hand\"></Th>
	</tr>
	<TR>		
		<Th>".sysmsg("Lp.","system")."</Th>
		<Th>".sysmsg("Article Id.","system")."</Th>
		<Th>".sysmsg("Article sizes","system")."</Th>
		<Th>".sysmsg("Quantity","system")."</Th>
		$add_column		
		<Th></Th>
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
				ts_to_id = to_id AND to_id = $ul_to_id AND ts_sk_id = $SKLEP_ID";
		parse_str(ado_query2url($sql));
		
		$buttons = "<img src=\"$UIMAGES/autoryzacja/i_nie.gif\" onClick=\"deleteItem('$ul_id')\" style=\"cursor:hand\" alt=\"".sysmsg("Delete article from favourites","system")."\">";

		$_tr = " class=t1";
		if (($i+1)%2) $_tr = " class=t2";
		$cena = system_cena($SKLEP_ID,$to_id,$ul_ilosc,$AUTH[parent]);
		if ($AUTH[p_price])
			$add_column = "<TD class=\"c2\" nowrap align=\"right\">".u_cena($cena)."</TD>
			<TD class=\"c2\" nowrap align=\"right\">".u_cena($cena*$ul_ilosc)."</TD>";

		$link = $next_char."list[to_id]=$to_id";
		
		$table.= "
		<TR $_tr>
			<TD class=\"c2\">".($i+1)."</TD>
			<TD class=\"c2\" title=\"$to_nazwa\" onClick=\"opisProduktu('$link')\" style=\"cursor:hand\">$to_indeks</TD>
			<TD class=\"c2\">".towar_wymiary($to_id)."</TD>
			<TD class=\"c2\">$ul_ilosc</TD>
			$add_column			
			<TD class=\"c2\">$buttons</TD>
		</TR>
		";

		$total_quant+= $ul_ilosc;
		$total_value+= ($cena*$ul_ilosc);
	}

	if ($AUTH[p_price])
		$add_column = "<TD class=\"c4\">&nbsp;</TD>
		<TD class=\"c4\" align=\"right\"><B>".u_cena($total_value)."</B></TD>";
				
	$table.= "
	</tbody>
	<tfoot>
	<TR $_tr>
		<TD class=\"c4\">&nbsp;</TD>
		<TD class=\"c4\">&nbsp;</TD>
		<TD class=\"c4\" align=\"right\"><B>".sysmsg("Total","cart").":</B></TD>
		<TD class=\"c4\"><B>$total_quant</B></TD>
		$add_column			
		<TD class=\"c4\">&nbsp;</TD>
	</TR>";

	$colspan = 5;
	if ($AUTH[p_price]) $colspan = 7;	

	$table.= "
	</tfoot></table>
	</form>
	<FORM METHOD=POST ACTION=\"$self\" id=\"deleteFavForm\">
	<INPUT TYPE=\"hidden\" id=\"darticle_id\" name=\"list[article_id]\">
	<INPUT TYPE=\"hidden\" name=\"list[nazwa]\" value=\"$nazwa\">
	</FORM>
	";

	echo $table;
?>
<script>
	var obj_ul_nazwa = getObject('darticle_id');
	var delForm = getObject('deleteFavForm');

	function deleteItem(id)
	{
		if (confirm('<? echo sysmsg("Are You sure, You want to delete this item ?","order") ?>'))
		{
			obj_ul_nazwa.value = id;
			delForm.submit();
		}
	}
</script>
