<?
	
	$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];

	if (!is_array($KOSZYK_OFERT))
	{
		echo sysmsg("no_article_in_cart","cart");
		return;
	}

	$table= "
	<FORM METHOD=POST ACTION=\"$self\" name=\"offerForm\" onSubmit=\"return false\">
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>		
		<Th>".sysmsg("Lp.","cart")."</Th>
		<Th>".sysmsg("Article Id","cart")."</Th>
		<Th>".sysmsg("Article sizes","cart")."</Th>
		<Th>".sysmsg("Quantity","cart")."</Th>
		<Th>".sysmsg("Price","cart")."</Th>
		<Th>".sysmsg("Value","cart")."</Th>
		<Th></Th>
	</TR>
	</thead>
	<tbody>	
	";

	$i=0;
	reset($KOSZYK_OFERT);
	while (list($tid,$tcount) = each($KOSZYK_OFERT))
	{
		$sql = "SELECT to_nazwa, to_indeks FROM towar WHERE to_id = $tid";
		parse_str(ado_query2url($sql));
		$link = $next_char."list[to_id]=$tid";
		$_tr = " class=t1";
		if (($i+1)%2) $_tr = " class=t2";
		$cena = system_cena($SKLEP_ID,$tid);
		$options = "<img src=\"$UIMAGES/autoryzacja/ikona_szukaj_b.gif\" width=12 height=12 hspace=5 vspace=0 border=0 alt=\"".sysmsg("Show picture","system")."\" onClick=\"opisProduktu('$link')\" style=\"cursor:hand\">";
		$table.= "
		<TR $_tr>
			<TD class=\"c2\">".($i+1)."</TD>
			<TD class=\"c2\" title=\"$to_nazwa\" onClick=\"opisProduktu('$link')\" style=\"cursor:hand\">$to_indeks</TD>
			<TD class=\"c2\">".towar_wymiary($tid)."</TD>
			<TD class=\"c2\"><INPUT TYPE=\"text\" NAME=\"ILOSC[$tid]\" style=\"width:50px\" onChange=\"chageItemQuantity('$tid',this.value,'".kwant_towaru($SKLEP_ID,$tid)."',$cena)\" id=\"ilosc_$tid\" value=\"$tcount\"></TD>
			<TD class=\"c2\" id=\"cena_$tid\">".u_cena($cena)."</TD>
			<TD class=\"c2\" id=\"wartosc_$tid\">".u_cena($cena*$tcount)."</TD>
			<TD class=\"c4\">$options</TD>
		</TR>
		";
		$i++;
	}

	$table.= "</tbody>
	<tfoot>
	<TR>
		<TD class=\"c4\" colspan=6 align=\"right\">
		<INPUT TYPE=\"button\" onClick=\"history.back()\" value=\"".sysmsg("Return to depository","system")."\">
		<INPUT TYPE=\"button\" value=\"".sysmsg("Count values","system")."\">
		<INPUT TYPE=\"button\" onClick=\"clearCart()\" value=\"".sysmsg("Clear cart","system")."\">
		<INPUT TYPE=\"button\" onClick=\"document.sendForm.submit()\" value=\"".sysmsg("Send offer","system")."\">
	<td>
	</tr>
	</tfoot>
	</table>
	</form>
	<script src=\"$SKLEP_INCLUDE_PATH/js/scripts.js\"></script>
	<script id=\"qscript\" src=\"\"></script>
	<FORM METHOD=POST ACTION=\"$next\" name=\"sendForm\">
	
	</FORM>
	";

	echo $table;

?>

<script>

	var mainForm = document.offerForm;

	function clearCart()
	{
		if (confirm('<? echo sysmsg("Are You sure, You want to clear the cart ?","cart") ?>'))
		{
			qobj = getObject('qscript');
			qobj.src = '<? echo $SKLEP_INCLUDE_PATH ?>/js/changeQuantity.php?clearCart=1&randSID='+Math.random();
		}
	}

	function chageItemQuantity(id,quant,kwant,cenat)
	{
		quant = quant.replace(",",".");
		if (isNaN(quant))
		{
			alert('<? echo sysmsg("Wrong value","system") ?>');
			return;
		}
		qobj = getObject('qscript');
		qobj.src = '<? echo $SKLEP_INCLUDE_PATH ?>/js/changeQuantity.php?tid='+id+'&tquant='+quant+'&tadd=0&kwant='+kwant+'&cenat='+cenat+'&randSID='+Math.random();
	}

</script>
