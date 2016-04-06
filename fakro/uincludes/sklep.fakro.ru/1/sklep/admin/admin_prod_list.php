<?
	include("$SKLEP_INCLUDE_PATH/js.h");

	//$adodb->debug=1;
	if (!strlen($LIST[sort_f])) 
	{
		$LIST[sort_f]="pr_nazwa";
		$LIST[sort_d]=0;
	}

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$FROMWHERE="FROM producent ";
	
	$sql = "SELECT * $FROMWHERE ORDER BY $sort";

	$res = $projdb->execute($sql);

	include("$SKLEP_INCLUDE_PATH/list.h");

	$table = "
	<table id=\"tprod\" class=\"list_table\">
	<TR>		
		<Th class=\"c1\">".sysmsg("Lp.","system")."
		<Th sort=\"pr_nazwa\">".sysmsg("Producer name.","system")."
		<Th class=\"co\">".sysmsg('Actions','admin')."</th></tr>";

	for($i=0; $i< $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		
		$buttons = "
			<img src=\"$SKLEP_IMAGES/i_zobacz.gif\" alt=\"Zobacz\" style=\"cursor:hand\" onClick=\"show_item('$pr_id');\">
			<img src=\"$SKLEP_IMAGES/i_delete.gif\" style=\"cursor:hand\" onClick=\"usunProd('$pr_id')\" alt=\"Usuё\">";
		$table.= "
		<TR dbid=\"$pr_id\">		
			<td class=\"c1\">".($i+1+$LIST[start])."
			<td>$pr_nazwa
			<td class=\"co\">$buttons";

	}
	$table.="</table>
	<FORM METHOD=POST ACTION=\"$self\" name=\"deleteProd\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ProducentUsun\">	
	<INPUT TYPE=\"hidden\" name=\"form[delid]\" id=\"prod_id\">	
	</FORM>
	";
	
	if (!$res->RecordCount()) return;
		echo $table;

?>

<script>
	list_table_init('tprod','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);

	function show_item(seldId)
	{
		table=getObject('tprod');
		table.selectedId = seldId;
		showProdDet(table.selectedId);
	}	

	function show_selected_item()
	{
		table=getObject('tprod');
		if (!table.selectedId) return;
		showProdDet(table.selectedId);
	}
	
	function usunProd(id)
	{
		if (confirm('Na pewno usunБц podanego producenta ?'))
		{
			document.deleteProd.prod_id.value = id;
			document.deleteProd.submit();
		}
	}

	function showProdDet(id)
	{
		document.cookie='ciacho[admin_pr_id]='+id;
		kartoteka_popup('<? echo $next ?>','producent');
	}

	function list_selected_item()
	{		
	}

</script>
