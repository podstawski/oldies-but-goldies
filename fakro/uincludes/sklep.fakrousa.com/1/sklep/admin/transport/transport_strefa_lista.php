<?
	# tr_strefa_id tr_strefa_typ tr_strefa_name tr_strefa_opis tr_strefa_vat 
	
	$sql = "SELECT * FROM tr_strefa ORDER BY tr_strefa_opis";
	$res = $adodb->execute($sql);

	$tab = "
	<FORM METHOD=POST ACTION=\"$next\">
	<INPUT TYPE=\"submit\" value=\"".sysmsg('Add delivery type','raport')."\" class=\"fb\">
	</FORM><br>
	<TABLE width=\"100%\" class=\"list_table\">
	<TR>
		<Th>".sysmsg('Lp','post')."</Th>
		<Th>".sysmsg('State','post')."</Th>
		<Th>".sysmsg('Tax','post')."</Th>
		<Th>".sysmsg('Zone','post')."</Th>
		<Th>[".sysmsg('Action','post')."]</Th>
	</TR>
	";

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));

		$buttons = "<A HREF=\"$next${next_char}list[id]=$tr_strefa_id\"><img src=\"$SKLEP_IMAGES/i_editmode_n.gif\" border=0></A>";
		$buttons.= "<img onClick=\"killRecord('$tr_strefa_id')\" src=\"$SKLEP_IMAGES/i_delete_n.gif\" border=0 style=\"cursor:hand\">";

		$tab.= "
		<TR>
			<TD>".($i+1)."</TD>
			<TD>$tr_strefa_opis ($tr_strefa_name)</TD>
			<TD>$tr_strefa_vat</TD>
			<TD>$tr_strefa_typ</TD>
			<TD class=\"colact\">$buttons</TD>
		</TR>
		";

	}
	

	$tab.="</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killDeliv\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"TransportStrefaUsun\">
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
