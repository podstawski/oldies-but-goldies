<?
	
	if ($AUTH[id] <= 0) return;

	$sql = "SELECT * FROM koszyk WHERE
			ko_su_id = ".$AUTH[id]." AND ko_rez_data IS NULL 
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
	<FORM METHOD=POST ACTION=\"$next\" onSubmit=\"return validateForm(this)\">	
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZamowienieZapisz\">
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
		
		$_tr = " class=t1";
		if (($i+1)%2) $_tr = " class=t2";
		$cena = system_cena($SKLEP_ID,$to_id,$ko_ilosc,$AUTH[parent]);
		if ($AUTH[p_price])
			$add_column = "<TD nowrap class=\"c2\" align=\"right\">".u_cena($cena)."</TD>
			<TD nowrap class=\"c4\" style=\"text-align:right\">".u_cena($cena*$ko_ilosc)."</TD>";
				
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
	</TR>";

	$colspan = 1;
	$arrayspan = 4;
	if ($AUTH[p_price]) 
	{
		$colspan = 3;
		$arrayspan = 6;
	}
//colspan=\"$colspan\"
	
	$sql = "SELECT MAX(za_numer) AS order_number 
			FROM zamowienia WHERE
			za_su_id = ".$AUTH[parent];
	
	parse_str(ado_query2url($sql));

	$sql = "SELECT su_adres1, su_adres2, su_adres3 FROM system_user WHERE su_id = ".$AUTH[parent];
	parse_str(ado_query2url($sql));

	if (!strlen(trim($su_adres2)) && !strlen(trim($su_adres3)))
	{
		$adres = "<INPUT TYPE=\"hidden\" name=\"list[dostawa]\" value=\"".addslashes(stripslashes(trim($su_adres1)))."\"><B>".stripslashes(trim($su_adres1))."</B>";
	}
	else if (!strlen(trim($su_adres3)))
	{
		$adres = "<SELECT name=\"list[dostawa]\">
					<option value=\"".addslashes(stripslashes(trim($su_adres1)))."\">".stripslashes(trim($su_adres1))."</option>
					<option value=\"".addslashes(stripslashes(trim($su_adres2)))."\">".stripslashes(trim($su_adres2))."</option>
				</SELECT>";
	} 
	else
	{
			$adres = "<SELECT name=\"list[dostawa]\">
					<option value=\"".addslashes(stripslashes(trim($su_adres1)))."\">".stripslashes(trim($su_adres1))."</option>
					<option value=\"".addslashes(stripslashes(trim($su_adres2)))."\">".stripslashes(trim($su_adres2))."</option>
					<option value=\"".addslashes(stripslashes(trim($su_adres3)))."\">".stripslashes(trim($su_adres3))."</option>
					</SELECT>";
	}

	$order_number+=1;
	if (strlen($ko_rez_nr)) $order_number = $ko_rez_nr;
	$table.= "
	<tr><td class=\"c4\" colspan=\"$arrayspan\" style=\"text-align:left\">
	<B>".sysmsg("Notice","system").":</B>
	<TEXTAREA NAME=\"list[uwagi]\" ROWS=\"5\" style=\"width:470px\">".stripslashes($ko_rez_uwagi)."</TEXTAREA>
	</td></tr>
	<tr><td colspan=\"".(3+$colspan)."\" nowrap>".sysmsg("Delivery addres","system").": $adres</tr>
	<tr><td colspan=\"3\" nowrap><img src=\"$SKLEP_IMAGES/spacer.gif\" width=\"35px\" align=\"left\">
	".sysmsg("Order number","system")." <INPUT TYPE=\"text\" id=\"ordernmb\" NAME=\"list[order_number]\" value=\"$order_number\">
	</td>
	<td class=\"c4\" align=\"right\" colspan=\"$colspan\">	
	<INPUT TYPE=\"submit\" value=\"".sysmsg("Submit order","system")."\">
	</td></tr></tfoot></table></form>";

	echo $table;



?>
<script>
	
	function validateForm(obj)
	{
		if (obj.ordernmb.value == '')
		{
			alert('<? echo sysmsg("Please, fill the order number field ","cart") ?>');
			obj.ordernmb.focus();
			return false;
		}

		return true;
	}

</script>
