<?
	
	$content = "
	<style>
		.towar_tab	{margin-top: 10px;margin-bottom: 10px;}
		.towar_tab	td {padding: 3px;}
		.towar_tab	th {padding: 3px; text-align:left; font-weight: bold; font-size: 11px; background-color: #9FA7B2; border: 1px solid #B0B6BF}
		.towar_tab .c2	{border-right: 1px solid #989E9C; border-bottom: 1px solid #989E9C;}
		.towar_tab tbody .c4	{border-bottom: 1px solid #989E9C; text-align: left; }
	</style>
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<tbody>
	<TR>
		<Td class=\"c2\">".sysmsg("email","system").":</Td>
		<Td class=\"c4\"><A HREF=\"mailto:".$FORM[email]."\">".$FORM[email]."</A></Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("firm name","system").":</Td>
		<Td class=\"c4\">".$FORM[firma]."</Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("address","system").":</Td>
		<Td class=\"c4\">".$FORM[adres]."></Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("zip code","system").":</Td>
		<Td class=\"c4\">".$FORM[kod]."</Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("city","system").":</Td>
		<Td class=\"c4\">".$FORM[miasto]."</Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("phone","system").":</Td>
		<Td class=\"c4\">".$FORM[tel]."</Td>
	</TR>
	<TR>
		<Td class=\"c2\">".sysmsg("person","system").":</Td>
		<Td class=\"c4\">".$FORM[osoba]."</Td>
	</TR>
	<TR>
		<Td valign=\"top\" class=\"c2\">".sysmsg("notice","system").":</Td>
		<Td class=\"c4\">".stripslashes($FORM[uwagi])."</Td>
	</TR>
	</tbody>
	</TABLE>
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>		
		<Th>".sysmsg("Lp.","cart")."</Th>
		<Th>".sysmsg("Article Id","cart")."</Th>
		<Th>".sysmsg("Article sizes","cart")."</Th>
		<Th>".sysmsg("Quantity","cart")."</Th>
		<Th>".sysmsg("Price","cart")."</Th>
		<Th>".sysmsg("Value","cart")."</Th>

	</TR>
	</thead>
	<tbody>	
	";
	$KOSZYK_OFERT = $SKLEP_SESSION["KOSZYK_OFERT"];
	if (!is_array($KOSZYK_OFERT)) return;
	$i=0;
	reset($KOSZYK_OFERT);
	while (list($tid,$tcount) = each($KOSZYK_OFERT))
	{
		$sql = "SELECT to_nazwa, to_indeks FROM towar WHERE to_id = $tid";
		parse_str(ado_query2url($sql));
		$link = $next_char."list[to_id]=$tid";
		$_tr = " class=t1";
		if (($i+1)%2) $_tr = " class=t2";
		$cena = $WM->system_cena($tid);
		$content.= "
		<TR $_tr>
			<TD class=\"c2\">".($i+1)."</TD>
			<TD class=\"c2\" title=\"$to_nazwa\" onClick=\"opisProduktu('$link')\" style=\"cursor:hand\">$to_indeks</TD>
			<TD class=\"c2\">".$WM->towar_wymiary($tid)."</TD>
			<TD class=\"c2\"><INPUT TYPE=\"text\" NAME=\"ILOSC[$tid]\" style=\"width:50px\" onChange=\"chageItemQuantity('$tid',this.value,'".$WM->kwant_towaru($tid)."',$cena)\" id=\"ilosc_$tid\" value=\"$tcount\"></TD>
			<TD class=\"c2\" id=\"cena_$tid\">".u_cena($cena)."</TD>
			<TD class=\"c4\" id=\"wartosc_$tid\">".u_cena($cena*$tcount)."</TD>
		</TR>
		";
		$i++;
	}

	$content.= "</tbody></table>";

	$headers = "MIME-Version: 1.0\r\n";
	$headers.= "Content-type: text/html; charset=utf-8\r\n";
	$headers.= "From: ".$FORM[email]." \r\n";
	$headers.= "Reply-To: ".$FORM[email]."\r\n";
	mail($FORM[mailto],sysmsg("Offer mail subject","system"),win2iso($content),$headers);	

?>
