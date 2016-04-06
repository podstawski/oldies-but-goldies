<?
	
	$sql = "SELECT * FROM poczta ORDER BY po_nazwa";
	$res = $adodb->execute($sql);

	$tab = "
	<FORM METHOD=POST ACTION=\"$next\">
	<INPUT TYPE=\"submit\" value=\"Dodaj rodzaj wysyłki\" class=\"fb\">
	</FORM><br>
	<TABLE width=\"100%\" class=\"list_table\">
	<TR>
		<Th>Lp</Th>
		<Th>Nazwa</Th>
		<Th>Cena netto</Th>
		<Th>Cena brutto</Th>
		<Th>Darmo powyżej</Th>
		<Th>[akcje]</Th>
	</TR>
	";

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));

		$buttons = "<A HREF=\"$next${next_char}list[id]=$po_id\"><img src=\"$SKLEP_IMAGES/i_editmode_n.gif\" border=0></A>";
		$buttons.= "<img onClick=\"killRecord('$po_id')\" src=\"$SKLEP_IMAGES/i_delete_n.gif\" border=0 style=\"cursor:hand\">";

		$tab.= "
		<TR>
			<TD>".($i+1)."</TD>
			<TD>$po_nazwa</TD>
			<TD>$po_cena_nt</TD>
			<TD>$po_cena_br</TD>
			<TD>$po_darmo_powyzej</TD>
			<TD class=\"colact\">$buttons</TD>
		</TR>
		";

	}
	

	$tab.="</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killDeliv\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"PocztaUsun\">
	<INPUT TYPE=\"hidden\" name=\"form[id]\" id=\"killId\">
	</FORM>";

	echo $tab;


?>
<script>

	function killRecord(id)
	{
		if (confirm('Czy na pewno usunąć ten rodzaj wysyłki ?'))
		{
			document.killDeliv.killId.value = id;
			document.killDeliv.submit();
		}
	}

</script>
