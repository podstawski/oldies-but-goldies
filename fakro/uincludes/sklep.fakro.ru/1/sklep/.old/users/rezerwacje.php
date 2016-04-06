<?
	if ($AUTH[id] <= 0 || !strlen($AUTH[parent])) return;

	$sql = "SELECT ko_rez_data, ko_rez_nr FROM koszyk 
			WHERE ko_su_id = ".$AUTH[parent]." 
			AND ko_rez_data IS NOT NULL AND (ko_deadline > $NOW OR ko_deadline IS NULL)
			GROUP BY ko_rez_data, ko_rez_nr ORDER BY ko_rez_data DESC, ko_rez_nr";


	$res = $adodb->execute($sql);

	if (!$res->RecordCount())
	{
		echo sysmsg("No reservations in database.","system");
		return;
	}
	
	$table = "
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>		
		<Th>".sysmsg("Lp.","system")."</Th>
		<Th>".sysmsg("Reservation number","system")."</Th>
		<Th>".sysmsg("Reservation","system")."</Th>
		<Th>".sysmsg("Articles count","system")."</Th>
		<Th></th>
	</TR>
	</thead>
	<tbody>
	";


	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		
		$data_rez = "";
		if (strlen($ko_rez_data)) $data_rez = date("d-m-Y H:i",$ko_rez_data);

		$sql = "SELECT COUNT(*) AS total_count FROM koszyk
				WHERE ko_rez_nr = '$ko_rez_nr' 
				AND (ko_deadline > $NOW OR ko_deadline IS NULL) 
				AND ko_rez_data = $ko_rez_data	
				AND ko_su_id = ".$AUTH[parent];
		
		parse_str(ado_query2url($sql));
		
		$buttons = "<A HREF=\"$next${next_char}list[ko_rez_data]=$ko_rez_data\"><img align=\"absmiddle\" src=\"$UIMAGES/autoryzacja/i_tree_n.gif\" border=\"0\"></A>";
		if (!$za_status)
			$buttons.= "<img align=\"absmiddle\" src=\"$UIMAGES/autoryzacja/i_delete_n.gif\" border=\"0\" onClick=\"deleteItem('$ko_rez_data')\" style=\"cursor:hand\">";

		$table.= "
		<TR>		
			<td class=\"c2\">".($i+1)."</td>
			<td class=\"c2\">$ko_rez_nr</td>
			<td class=\"c2\">$data_rez</td>
			<td class=\"c2\">$total_count</td>
			<td class=\"c2\">$buttons</td>
		</TR>
		";
	}

	$table.= "</tbody></table>
	<FORM METHOD=POST ACTION=\"$self\" id=\"deleteRezForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"RezerwacjaUsun\">
	<INPUT TYPE=\"hidden\" id=\"ko_rez_data\" name=\"form[ko_rez_data]\">
	</FORM>	
	<script src=\"$SKLEP_INCLUDE_PATH/js/scripts.js\"></script>
	";

	echo $table;
?>
<script>
	var zam_input = getObject('ko_rez_data');
	var delForm = getObject('deleteRezForm');

	function deleteItem(id)
	{
		if (confirm('<? echo sysmsg("Are You sure, You want to delete this reservation ?","order") ?>'))
		{
			zam_input.value = id;
			delForm.submit();
		}
	}
</script>
