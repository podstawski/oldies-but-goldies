<?
	include("$SKLEP_INCLUDE_PATH/raporty/daty.php");
	include("$SKLEP_INCLUDE_PATH/js.h");

	if (!strlen($AUTH[parent])) return;

	//$adodb->debug=1;
	if (!strlen($LIST[sort_f])) 
	{
		$LIST[sort_f]="za_data";
		$LIST[sort_d]=1;
	}

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$FROMWHERE="FROM zamowienia WHERE za_su_id = ".$AUTH[parent]." 
				AND za_data >= $od AND za_data <= $do";

	$sql = "SELECT * $FROMWHERE ORDER BY $sort";
	
	$res = $adodb->execute($sql);

	include("$SKLEP_INCLUDE_PATH/list.h");


	if (!$res->RecordCount())
	{
		echo sysmsg("No orders in database.","system");
		return;
	}

	$table = "
	<table id=\"tarch\" class=\"list_table\" cellspacing=0 cellpadding=0 border=0>
	<TR>		
		<Th >".sysmsg("Lp.","system")."</Th>
		<Th sort=\"za_numer_obcy\">".sysmsg("Order number.","system")."</Th>
		<Th sort=\"za_data\">".sysmsg("Order","system")."</Th>
		<Th sort=\"za_status\">".sysmsg("Status","system")."</Th>
		<Th>".sysmsg("Articles count","system")."</Th>
		<Th>&nbsp;</th>
	</TR>
	";

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		
		$data_zam = "";
		if (strlen($za_data)) $data_zam = date("d-m-Y",$za_data);
		$data_sts = "";
		if (strlen($za_data_przyjecia) && $za_status == 1) $data_sts = date("d-m-Y H:i",$za_data_przyjecia);
		else if (strlen($za_data_realizacji)) $data_sts = date("d-m-Y",$za_data_realizacji);

		$sql = "SELECT COUNT(*) AS total_count FROM zampoz 
				WHERE zp_za_id = $za_id";
		
		parse_str(ado_query2url($sql));
		$buttons = "<a id=\"a_$za_id\" href=\"$self${next_char}action=PDF&list[pdf]=zamowienie&list[prn]=1$_more&list[id]=$za_id\" target=\"pdf\"><img src=\"$SKLEP_IMAGES/sb/pdfprint.gif\" alt=\"$altp\" border=0></a>";
		$table.= "
		<TR dbid=\"$za_id\">		
			<td class=\"c2\">".($i+1+$LIST[start])."</td>
			<td class=\"c2\">$za_numer_obcy</td>
			<td class=\"c2\">$data_zam</td>
			<td class=\"c2\">".sysmsg("status_$za_status","status")." $data_sts</td>
			<td class=\"c2\">$total_count</td>
			<td class=\"c4\">$buttons</td>
		</TR>
		";
	}

	$table.= "</table>";

	echo $table;

?>
<script>
	list_table_init('tarch','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);

	function show_selected_item()
	{
		table=getObject('tarch');
		if (!table.selectedId) return;
		obj = getObject('a_'+table.selectedId);
		obj.click();
	}

	function list_selected_item()
	{		
	}

</script>
