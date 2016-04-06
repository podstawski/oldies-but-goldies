<?
	include("$SKLEP_INCLUDE_PATH/js.h");

	$PARENT_KAT = $FORM[parent_id];
	
//	if (!strlen($AUTH[parent])) return;

	//$adodb->debug=1;
	if (!strlen($LIST[sort_f])) 
	{
		$LIST[sort_f]="ka_nazwa";
		$LIST[sort_d]=0;
	}

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	if (!$PARENT_KAT)
		$where = "ka_parent IS NULL";
	else
		$where = "ka_parent = $PARENT_KAT";

	$FROMWHERE="FROM kategorie WHERE $where";
	
	$sql = "SELECT * $FROMWHERE ORDER BY $sort";

	$res = $projdb->execute($sql);

	include("$SKLEP_INCLUDE_PATH/list.h");

	$table = "
	<table id=\"tcat\" class=\"list_table\">
	<TR>		
		<Th class=\"c1\">".sysmsg("Lp.","system")."
		<Th sort=\"ka_nazwa\">".sysmsg("Category name.","system")."
		<Th sort=\"ka_kod\">".sysmsg("Category code","system")."
		<Th class=\"cw\" sort=\"ka_to_c\" title=\"".sysmsg("title_ka_to_c","system")."\">".sysmsg("T","system")."
		<Th class=\"cw\" title=\"".sysmsg("Subcategories","system")."\">S
		<Th>Akcje";

	if ($PARENT_KAT)
	{
		$s_path = explode(";",getFullPath($PARENT_KAT));
		$s_path = array_reverse($s_path);
		$s_path = implode("->",$s_path);
		
		$sql = "SELECT ka_parent AS goup FROM kategorie WHERE ka_id = $PARENT_KAT";
		parse_str(ado_query2url($sql));
		if (!strlen($goup)) $goup = 0;
		$table.= "
		<TR dbid=\"\">		
			<td colspan=\"3\" onClick=\"showKatDet('$goup')\"><img src=\"$SKLEP_IMAGES/prev.gif\" align=\"absmiddle\"> ".sysmsg("Go up")." ($s_path)
			<td>&nbsp;
			<td>&nbsp;
			<td>$buttons
		</TR>";

	}

	for($i=0; $i< $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$sub_count = "&nbsp;";
		$sql = "SELECT COUNT(*) AS sub_count FROM kategorie WHERE ka_parent = $ka_id";
		parse_str(ado_query2url($sql));
		$buttons = "
			<img src=\"$SKLEP_IMAGES/i_zobacz.gif\" alt=\"".sysmsg('Look','admin')."\" style=\"cursor:hand\" onClick=\"show_item('$ka_id');\">
			<img src=\"$SKLEP_IMAGES/i_next.gif\" style=\"cursor:hand;\" onClick=\"showKatDet('$ka_id')\" alt=\"".sysmsg('Forward','admin')."\">
			<img src=\"$SKLEP_IMAGES/i_delete.gif\" style=\"cursor:hand;\" onClick=\"usunKat('$ka_id')\" alt=\"".sysmsg('Delete','admin')."\">";
		$table.= "
		<TR dbid=\"$ka_id\">		
			<td class=\"c1\">".($i+1+$LIST[start])."
			<td>$ka_nazwa
			<td class=\"cw\">$ka_kod
			<td class=\"cw\">$ka_to_c
			<td class=\"cw\">$sub_count
			<td class=\"co\">$buttons";

	}
	$table.="</table>
	<FORM METHOD=POST ACTION=\"$self\" name=\"deleteKat\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KategoriaUsun\">	
	<INPUT TYPE=\"hidden\" name=\"form[delid]\" id=\"kat_id\">	
	<INPUT TYPE=\"hidden\" name=\"form[parent_id]\" value=\"".$FORM[parent_id]."\">	
	".sort_navi_options($LIST)."
	</FORM>
	<FORM METHOD=POST ACTION=\"$self\" name=\"changeKat\">
	<INPUT TYPE=\"hidden\" name=\"form[parent_id]\" id=\"kat_id\">	
	".sort_navi_options($LIST)."
	</FORM>
	";

	echo $table;

?>

<script>
	list_table_init('tcat','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);

	function show_item(seldId)
	{
		table=getObject('tcat');
		table.selectedId = seldId;
		document.cookie='ciacho[admin_ka_id]='+table.selectedId;
		kartoteka_popup('<? echo $next ?>','kategoria');
	}	

	function show_selected_item()
	{
		table=getObject('tcat');
		if (!table.selectedId) return;
		document.cookie='ciacho[admin_ka_id]='+table.selectedId;
		kartoteka_popup('<? echo $next ?>','kategoria');

	}
	
	function usunKat(id)
	{
		if (confirm('<? echo sysmsg('Are you sure you want to delete','admin')?> ?'))
		{
			document.deleteKat.kat_id.value = id;
			document.deleteKat.submit();
		}
	}

	function showKatDet(id)
	{
		document.changeKat.kat_id.value=id;
		document.changeKat.submit();
	}

	function list_selected_item()
	{		
	}

</script>
