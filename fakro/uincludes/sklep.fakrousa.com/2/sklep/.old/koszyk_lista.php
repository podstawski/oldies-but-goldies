<?
	if (!strlen($AUTH[id])) return;

	$sql = "SELECT * FROM koszyk WHERE
			ko_su_id = ".$AUTH[id]." 
			AND ko_rez_data IS NULL 
			AND (ko_deadline > $NOW OR ko_deadline IS NULL)
			ORDER BY ko_id";

	$res = $adodb->execute($sql);
	
	if (!$res->RecordCount())
	{
		echo sysmsg("no_article_in_cart","cart");
		return;
	}

	if ($AUTH[p_price])
		$add_column = "<Th>".sysmsg("Price","system")."</Th>
		<Th>".sysmsg("Value","system")."</Th>";
	$table= "
	<FORM METHOD=POST ACTION=\"$more\" name=\"prepareOrderForm\" onSubmit=\"return false;\">
	<INPUT TYPE=\"button\" class=\"but\" onClick=\"clearCart()\" value=\"".sysmsg("Clear cart","system")."\">
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
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
				ts_id = $ko_ts_id AND ts_to_id = to_id AND ts_sk_id = $SKLEP_ID";
		parse_str(ado_query2url($sql));
		
		$buttons = "<img src=\"$UIMAGES/autoryzacja/i_nie.gif\" onClick=\"deleteItem('$ko_id')\" style=\"cursor:hand\" alt=\"".sysmsg("Delete article from cart","system")."\">";

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
			<TD class=\"c2 ls\" title=\"$to_nazwa\" onClick=\"opisProduktu('$link')\" style=\"cursor:hand\">$to_indeks</TD>
			<TD class=\"c2 ls\">".towar_wymiary($to_id)."</TD>
			<TD class=\"c2\"><INPUT TYPE=\"text\" NAME=\"ILOSC[$ko_id]\" style=\"width:50px\" onChange=\"chageItemQuantity('$ko_id',this.value)\" value=\"$ko_ilosc\"></TD>
			$add_column			
			<TD class=\"c4\">$buttons</TD>
		</TR>
		";

		$total_quant+= $ko_ilosc;
		$total_value+= ($cena*$ko_ilosc);
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

	if ($AUTH[p_order])
		$order_button="<INPUT TYPE=\"button\" onClick=\"document.prepareOrderForm.submit()\" value=\"".sysmsg("Prepare order","system")."\">";

	$table.= "
	<tr><td class=\"c4\" colspan=\"$colspan\" align=\"right\">
	<INPUT TYPE=\"button\" onClick=\"document.cartToFav.submit()\" value=\"".sysmsg("Add to favourites","system")."\">	
	<INPUT TYPE=\"button\" onClick=\"document.prepareResForm.submit()\" value=\"".sysmsg("Prepare reservation","system")."\">	
	$order_button
	</td></tr></tfoot></table>
	</form>
	<FORM METHOD=POST ACTION=\"$self\" id=\"deleteCartForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KoszykUsun\">
	<INPUT TYPE=\"hidden\" id=\"darticle_id\" name=\"list[article_id]\">
	<INPUT TYPE=\"hidden\" id=\"dclear_cart\" name=\"list[clear_cart]\">
	</FORM>
	<FORM METHOD=POST ACTION=\"$self\" id=\"changeCartForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KoszykZmien\">
	<INPUT TYPE=\"hidden\" id=\"carticle_id\" name=\"list[article_id]\">
	<INPUT TYPE=\"hidden\" id=\"carticle_quant\" name=\"list[quantity]\">
	</FORM>
	<FORM METHOD=POST ACTION=\"$next\" name=\"prepareResForm\">
	</FORM>
	<script src=\"$SKLEP_INCLUDE_PATH/js/scripts.js\"></script>
	<form action=\"$self\" method=\"POST\" name=\"cartToFav\">
	<input type=\"hidden\" name=\"action\" value=\"KoszykDoUlubionych\">
	</form>
	";

	echo $table;

?>
<script>
	
	var art_input = getObject('darticle_id');
	var clr_input = getObject('dclear_cart');
	var delForm = getObject('deleteCartForm');

	var cart_input = getObject('carticle_id');
	var quant_input = getObject('carticle_quant');
	var chngForm = getObject('changeCartForm');

	function deleteItem(id)
	{
		if (confirm('<? echo sysmsg("Are You sure, You want to delete this article ?","cart") ?>'))
		{
			art_input.value = id;
			delForm.submit();
		}
	}

	function clearCart()
	{
		if (confirm('<? echo sysmsg("Are You sure, You want to clear the cart ?","cart") ?>'))
		{
			clr_input.value = 1;
			delForm.submit();
		}
	}

	function chageItemQuantity(id,val)
	{
		val = val.replace(",",".");

		if (isNaN(val))
		{
			alert('<? echo sysmsg("Wrong value","system") ?>');
			return;
		}

		cart_input.value = id;
		quant_input.value = val;
		chngForm.submit();
	}
	
</script>
