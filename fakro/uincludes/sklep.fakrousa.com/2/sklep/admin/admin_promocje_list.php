<?
	include("$SKLEP_INCLUDE_PATH/js.h");

	//$adodb->debug=1;
	if (!strlen($LIST[sort_f])) 
	{
		$LIST[sort_f]="pm_symbol";
		$LIST[sort_d]=0;
	}

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$FROMWHERE="FROM promocja ";
	
	$sql = "SELECT * $FROMWHERE ORDER BY $sort";

	$res = $projdb->execute($sql);

	include("$SKLEP_INCLUDE_PATH/list.h");

	$table = "
	<table id=\"tprom\" class=\"list_table\">
	<tr>		
		<th class=\"c1\">".sysmsg("Lp.","system")."
		<th sort=\"pm_symbol\">".sysmsg("Promotion name","system")."
		<th>".sysmsg('Items','admin')."
		<th class=\"co\">".sysmsg('Actions','admin')."";

	for($i=0; $i< $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		
		$buttons = "
			<img src=\"$SKLEP_IMAGES/i_zobacz.gif\" alt=\"".sysmsg('Look','admin')."\" style=\"cursor:hand\" onClick=\"show_item('$pm_id');\">
			<img src=\"$SKLEP_IMAGES/i_delete.gif\" style=\"cursor:hand\" onClick=\"usunProm('$pm_id')\" alt=\"".sysmsg('Delete','admin')."\">";

		$sql = "SELECT COUNT(pt_id) AS pm_count 
				FROM promocja_towaru WHERE pt_pm_id = $pm_id";
		parse_str(ado_query2url($sql));
		$table.= "
		<TR dbid=\"$pm_id\" >		
			<td class=\"c1\">".($i+1+$LIST[start])."
			<td>$pm_symbol
			<td>$pm_count
			<td class=\"co\">$buttons";

	}
	$table.="</table>
	<FORM METHOD=POST ACTION=\"$self\" name=\"deleteProm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"PromocjaUsun\">	
	<INPUT TYPE=\"hidden\" name=\"form[delid]\" id=\"prom_id\">	
	</FORM>
	";
	
	if (!$res->RecordCount()) return;
		echo $table;
?>

<script language="JavaScript">
	list_table_init('tprom','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);
	
	function show_item(seldId)
	{
		table=getObject('tprom');
		table.selectedId = seldId;
		showPromDet(table.selectedId);
	}	

	function show_selected_item()
	{
		table=getObject('tprom');
		if (!table.selectedId) return;
		showPromDet(table.selectedId);
	}
	
	function usunProm(id)
	{
		if (confirm('<? echo sysmsg('Are you sure you want to delete','admin')?> ?'))
		{
			document.deleteProm.prom_id.value = id;
			document.deleteProm.submit();
		}
	}

	function showPromDet(id)
	{
		document.cookie='ciacho[admin_pm_id]='+id;
		kartoteka_popup('<? echo $next ?>','promocja');
	}

	function list_selected_item()
	{		
	}

</script>
