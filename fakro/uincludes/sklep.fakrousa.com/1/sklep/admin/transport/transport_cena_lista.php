<?
	$sql = "SELECT * FROM tr_ceny,tr_typ WHERE tr_ceny.tr_typ_id = tr_typ.tr_typ_id ORDER BY tr_strefa_typ, tr_ceny.tr_typ_id";
	$res = $adodb->execute($sql);

	$tab = "
	<FORM METHOD=POST ACTION=\"$next\">
	<INPUT TYPE=\"submit\" value=\"".sysmsg('Add delivery type','raport')."\" class=\"fb\">
	</FORM><br>
	<TABLE width=\"100%\" class=\"list_table\">
	<TR>
		<Th>".sysmsg('Lp','post')."</Th>
		<Th>".sysmsg('Zone','post')."</Th>
		<Th>".sysmsg('Name','post')."</Th>
		
		<Th>".sysmsg('Weight','post')."</Th>
		
		<Th>".sysmsg('Volume','post')."</Th>
		
		<Th>".sysmsg('Price','article')."</Th>
		
		<Th>[".sysmsg('Action','post')."]</Th>
	</TR>
	";

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));

		$buttons = "<A HREF=\"$next${next_char}list[id]=$tr_ceny_id\"><img src=\"$SKLEP_IMAGES/i_editmode_n.gif\" border=0></A>";
		$buttons.= "<img onClick=\"killRecord('$tr_ceny_id')\" src=\"$SKLEP_IMAGES/i_delete_n.gif\" border=0 style=\"cursor:hand\">";

		$tab.= "
		<TR>
			<TD>".($i+1)."</TD>
			<TD>$tr_strefa_typ</TD>
			<TD>$tr_typ_name</TD>
			
			<TD>$tr_waga_od / $tr_waga_do</TD>
			
			<TD>$tr_objetosc_od / $tr_objetosc_do</TD>
			
			<TD>$tr_ceny</TD>
			<TD class=\"colact\">$buttons</TD>
		</TR>
		";

	}
	

	$tab.="</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killDeliv\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"TransportCenaUsun\">
	<INPUT TYPE=\"hidden\" name=\"form[id]\" id=\"killId\">
	</FORM>";

	echo $tab;


?>
<script>

	function killRecord(id)
	{
		if (confirm('Czy na pewno usun±æ ten rodzaj wysy³ki ?'))
		{
			document.killDeliv.killId.value = id;
			document.killDeliv.submit();
		}
	}

</script>
