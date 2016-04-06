<?
	if ($AUTH[id] <= 0) return;

	$sql = "SELECT COUNT(ko_ilosc) AS cart_count FROM koszyk WHERE
			ko_su_id = ".$AUTH[id]." AND ko_rez_data IS NULL 
			AND (ko_deadline > $NOW OR ko_deadline IS NULL)";
	parse_str(ado_query2url($sql));
		
	if ($cart_count)
	{
		echo "<form action=\"$self\" method=\"POST\">
			<input type=\"hidden\" name=\"action\" value=\"KoszykDoUlubionych\">
			<input type=\"submit\" class=\"but\" value=\"".sysmsg("Cart to favourites","system")."\">
			</form>";
	}
	else
	{
		$maycart=1;
	}

	$sql = "SELECT ul_nazwa,count(*) AS ul_count FROM ulubione
		WHERE ul_su_id = ".$AUTH[id]." 
		GROUP BY ul_nazwa";


	$res = $adodb->execute($sql);

	if (!$res->RecordCount())
	{
		return;
	}
	
	$table = "
	<table class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>		
		<Th>".sysmsg("Lp.","system")."</Th>
		<Th>".sysmsg("Favourit name","system")."</Th>
		<Th>".sysmsg("Articles count","system")."</Th>
		<Th></th>
	</TR>
	</thead>
	<tbody>
	";


	$addcart=sysmsg("Add to cart","system");
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		
		$data_rez = "";

		
		parse_str(ado_query2url($sql));
		
		

		$_nazwa=urlencode($ul_nazwa);
		$buttons="";

		if ($maycart) 
		   $buttons.="<A HREF=\"$more${next_char}list[nazwa]=$_nazwa&action=KoszykZUlubionych\"><img
		   align=\"absmiddle\" src=\"$SKLEP_IMAGES/sb/ulubione.gif\" 
		   alt=\"$addcart\" border=0></A>&nbsp;";

		$buttons.="<A HREF=\"$next${next_char}list[nazwa]=$_nazwa\"><img align=\"absmiddle\" src=\"$SKLEP_IMAGES/tree.gif\" border=\"0\"></A>&nbsp;";
		$buttons.="<img align=\"absmiddle\" src=\"$SKLEP_IMAGES/del.gif\" border=\"0\" onClick=\"deleteItem('$ul_nazwa')\" style=\"cursor:hand\">";

		$table.= "
		<TR>		
			<td class=\"c2\">".($i+1)."</td>
			<td class=\"c2\">$ul_nazwa</td>
			<td class=\"c2\">$ul_count</td>
			<td class=\"c2\">$buttons</td>
		</TR>
		";
	}

	$table.= "</tbody></table>

	<FORM METHOD=POST ACTION=\"$self\" id=\"deleteUluForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"UlubioneUsun\">
	<INPUT TYPE=\"hidden\" id=\"ul_nazwa\" name=\"form[ul_nazwa]\">
	</FORM>	
	<script src=\"$SKLEP_INCLUDE_PATH/js/scripts.js\"></script>
	";

	echo $table;
?>
<script>
	var obj_ul_nazwa = getObject('ul_nazwa');
	var delForm = getObject('deleteUluForm');

	function deleteItem(id)
	{
		if (confirm('<? echo sysmsg("Are You sure, You want to delete this favourit ?","order") ?>'))
		{
			obj_ul_nazwa.value = id;
			delForm.submit();
		}
	}
</script>
