<?
	include("$SKLEP_INCLUDE_PATH/list.h");
	include ($SKLEP_INCLUDE_PATH."/raporty/daty.php");

	if (!strlen($LIST[sort_f])) $LIST[sort_f]="za_pyt_data";
	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$ZNAK = $cos ? "<>" : "=";
	$FROMWHERE = "FROM towar,towar_sklep,zapytania 
					WHERE to_id=ts_to_id AND za_ts_id=ts_id
					AND za_pyt_data >= $od AND za_pyt_data <= $do
					AND za_odp_su_id $ZNAK ".$SYSTEM[master];
	$sql = "SELECT * $FROMWHERE ORDER BY ".$sort;

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}
	$navi=$size?navi($self,$LIST,$size):"";
		
	if (strlen($navi))
		$result = $projdb->SelectLimit($sql,$size,$LIST[start]+0);
	else
		$result = $projdb->Execute($sql);	
	
	$table ="
	$navi
	<table id=\"wydruk\" class=\"list_table\">
	<TR>		
		<th >".sysmsg("Lp","system")."
		<th sort=\"to_nazwa\">Towar
		<th sort=\"za_email\">E-mail 
		<th sort=\"za_pyt_data\">Data
		<th sort=\"za_odp_data\">Odp.
		<th sort=\"za_odp_data\">Akcja
		";


	for ($i=0; $i < $result->RecordCount(); $i++)
	{
		parse_str(ado_explodename($result,$i));

		$query="SELECT * FROM system_user WHERE su_id=$za_odp_su_id";
		parse_str(ado_query2url($query));

		$odp=$za_odp_data ? "$su_imiona $su_nazwisko<br>".date("d-m-Y H:i",$za_odp_data) : "";
		$pyt=date("d-m-Y H:i",$za_pyt_data);

		$table.="<tr dbid=\"$za_id\">
			<td>".($i+1+$LIST[start])."
			<td title=\"$to_indeks\"><a href=\"javascript:show_item('$to_id')\">".stripslashes($to_nazwa)."</a>
			<td>$za_email
			<td>$pyt
			<td>$odp
			<td><img src=\"$SKLEP_IMAGES/i_zobacz.gif\" alt=\"Zobacz\" style=\"cursor:hand\" onClick=\"showDetails('$za_id')\">";
	}

	$table.="</table>";
	echo $table;

	//include("$SKLEP_INCLUDE_PATH/js.h");
?>
<script>
	list_table_init('wydruk','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);

	function show_selected_item()
	{
		table=getObject('wydruk');
		if (!table.selectedId) return;
		document.list_fwd_form.s_id.value=table.selectedId;
		document.list_fwd_form.submit();		

	}
	function showDetails(id)
	{
		document.list_fwd_form.s_id.value=id;
		document.list_fwd_form.submit();		
	}

	function show_item(seldId)
	{
		document.cookie='ciacho[admin_to_id]='+seldId;
		kartoteka_popup(TOWAR_NEXT,'towar');
	}


	function list_selected_item()
	{		
	}

	function usunTow(id)
	{		
		if (confirm('Na pewno usunБц ten produkt ?'))
		{
			document.killTow.killId.value = id;
			document.killTow.submit();
		}
	}


</script>
