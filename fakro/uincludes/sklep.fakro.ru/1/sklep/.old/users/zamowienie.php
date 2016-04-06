<?
	$za_id = $LIST[za_id];

	if (!strlen($za_id)) return;

	$sql = "SELECT zampoz.*, towar.*, towar_parametry.* 
			FROM zampoz, towar, towar_sklep, towar_parametry
			WHERE zp_za_id = $za_id 
			AND ts_to_id = to_id 
			AND zp_ts_id = ts_id
			AND tp_to_id = to_id";

	$res = $adodb->execute($sql);
	
	if ($AUTH[p_price])
		$add_column = "<Th>".sysmsg("Price","system")."</Th>
		<Th>".sysmsg("Value","system")."</Th>";
	$table= "
	<FORM METHOD=POST ACTION=\"$more\" id=\"prepareOrderForm\">
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
	
		$_tr = " class=t1";
		if (($i+1)%2) $_tr = " class=t2";
		$cena = $zp_cena;
		if ($AUTH[p_price])
			$add_column = "<TD class=\"c2\" nowrap align=\"right\">".u_cena($cena)."</TD>
			<TD class=\"c2\" nowrap align=\"right\">".u_cena($cena*$zp_ilosc)."</TD>";

		$link = $next_char."list[to_id]=$to_id";				
				
		$table.= "
		<TR $_tr>
			<TD class=\"c2\">".($i+1)."</TD>
			<TD class=\"c2\" title=\"$to_nazwa\" onClick=\"opisProduktu('$link')\" style=\"cursor:hand\">$to_indeks</TD>
			<TD class=\"c2\">".towar_wymiary($to_id)."</TD>
			<TD class=\"c2\">$zp_ilosc</TD>
			$add_column			
		</TR>
		";

		$total_quant+= $zp_ilosc;
		$total_value+= ($cena*$zp_ilosc);
	}
	
	$sql = "SELECT * FROM zamowienia WHERE za_id = $za_id";
	parse_str(ado_query2url($sql));

	$data_sts = "";
	if (strlen($za_data_przyjecia)) $data_sts = date("d-m-Y H:i",$za_data_przyjecia);
	if (strlen($za_data_realizacji)) $data_sts = date("d-m-Y",$za_data_realizacji);

	parse_str($za_parametry);
	$head = "
	<TABLE>
	<TR>
		<TD>".sysmsg("Order date","system")."</TD>
		<TD>:<B>".date("d-m-Y H:i:s",$za_data)."</B></TD>
	</TR>
	<TR>
		<TD>".sysmsg("Order number","system")."</TD>
		<TD>:<B>$za_numer_obcy</B></TD>
	</TR>
	<TR>
		<TD>".sysmsg("Status","system")."</TD>
		<TD>:<B>".sysmsg("status_$za_status","status")." $data_sts</B></TD>
	</TR>
	<TR>
		<TD valign=\"top\">".sysmsg("Notice","system")."</TD>
		<TD>:<B>".stripslashes(nl2br($za_uwagi))."</B></TD>
	</TR>
	<TR>
		<TD valign=\"top\">".sysmsg("Order person","system")."</TD>
		<TD>:<B>$osoba</B></TD>
	</TR>
	</TABLE>";

		if ($AUTH[p_price])
			$add_column = "<TD class=\"c4\">&nbsp;</TD>
			<TD class=\"c4\" align=\"right\"><B>".u_cena($total_value)."</B></TD>";
				
		$table.= "
		<tfoot>
		<TR $_tr>
			<TD class=\"c4\">&nbsp;</TD>
			<TD class=\"c4\">&nbsp;</TD>
			<TD class=\"c4\" align=\"right\"><B>".sysmsg("Total","cart").":</B></TD>
			<TD class=\"c4\"><B>$total_quant</B></TD>
			$add_column			
		</TR>
		";


	$table.= "</tbody></table>";
	echo $head.$table;
?>
