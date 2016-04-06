<?
	if ($AUTH[id] <= 0 || !strlen($AUTH[parent])) return;

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c FROM zamowienia WHERE za_su_id = ".$AUTH[parent];
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}

	$sql = "SELECT * FROM zamowienia WHERE za_su_id = ".$AUTH[parent]." ORDER BY za_data DESC";
	
	$navi=$size?navi($self,$LIST,$size):"";

	if (strlen($navi))
		$res = $adodb->SelectLimit($sql,$size,$LIST[start]+0);
	else
		$res = $adodb->execute($sql);

	if (!$res->RecordCount())
	{
		echo sysmsg("No orders in database.","system");
		return;
	}
	
	$table = "
	$navi
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>		
		<Th>".sysmsg("Lp.","system")."</Th>
		<Th>".sysmsg("Order number.","system")."</Th>
		<Th>".sysmsg("Order","system")."</Th>
		<Th>".sysmsg("Status","system")."</Th>
		<Th>".sysmsg("Articles count","system")."</Th>
		<Th></th>
	</TR>
	</thead>
	<tbody>
	";


	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		
		$data_zam = "";
		if (strlen($za_data)) $data_zam = date("d-m-Y",$za_data);
		$data_sts = "";
		if (strlen($za_data_przyjecia)) $data_sts = date("d-m-Y H:i",$za_data_przyjecia);
		if (strlen($za_data_realizacji)) $data_sts = date("d-m-Y",$za_data_realizacji);

		$sql = "SELECT COUNT(*) AS total_count FROM zampoz 
				WHERE zp_za_id = $za_id";
		parse_str(ado_query2url($sql));
		
		$buttons = "<A HREF=\"$next${next_char}list[za_id]=$za_id\"><img align=\"absmiddle\" src=\"$UIMAGES/autoryzacja/i_tree_n.gif\" alt=\"".sysmsg("show details","system")."\" border=\"0\"></A>";
		if (!$za_status && $AUTH[p_order])
			$buttons.= "&nbsp;<img align=\"absmiddle\" src=\"$UIMAGES/autoryzacja/i_nie.gif\" border=\"0\" onClick=\"deleteItem('$za_id')\" alt=\"".sysmsg("delete","system")."\" style=\"cursor:hand\">";
		$table.= "
		<TR>		
			<td class=\"c2\">".($i+1+$LIST[start])."</td>
			<td class=\"c2\">$za_numer_obcy</td>
			<td class=\"c2\">$data_zam</td>
			<td class=\"c2\">".sysmsg("status_$za_status","status")." $data_sts</td>
			<td class=\"c2\">$total_count</td>
			<td class=\"c2\">$buttons</td>
		</TR>
		";
	}

	$table.= "</tbody></table>
	<FORM METHOD=POST ACTION=\"$self\" id=\"deleteZamForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZamowienieUsun\">
	<INPUT TYPE=\"hidden\" id=\"za_id\" name=\"form[za_id]\">
	</FORM>	
	<script src=\"$SKLEP_INCLUDE_PATH/js/scripts.js\"></script>
	";

	echo $table;

?>
<script>
	var zam_input = getObject('za_id');
	var delForm = getObject('deleteZamForm');

	function deleteItem(id)
	{
		if (confirm('<? echo sysmsg("Are You sure, You want to delete this order ?","order") ?>'))
		{
			zam_input.value = id;
			delForm.submit();
		}
	}
</script>
