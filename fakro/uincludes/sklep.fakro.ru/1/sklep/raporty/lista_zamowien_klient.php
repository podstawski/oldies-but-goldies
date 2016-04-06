<?
	include("$SKLEP_INCLUDE_PATH/raporty/daty.php");
	include("$SKLEP_INCLUDE_PATH/raporty/where.php");

	if (!strlen($LIST[sort_f])) 
	{
		$LIST[sort_f]="za_numer";
		$LIST[sort_d]=1;
	}

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	


	$FROMWHERE="FROM zamowienia, zampoz
				WHERE za_su_id = ".$AUTH[parent]."
				AND zp_za_id = za_id
				AND za_data>=$od AND za_data<=$do
				$KONTRAHENT_WHERE $ZAMOWIENIE_WHERE";

	

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}

	$navi=$size?navi($self,$LIST,$size):"";
	
	$query="SELECT SUM(zp_cena*zp_ilosc) AS ile, za_id, za_numer, za_status
			$FROMWHERE GROUP BY za_id, za_numer, za_status ORDER BY $sort";
//	$projdb->debug = 1;
	if (strlen($navi))
		$result = $projdb->SelectLimit($query,$size,$LIST[start]+0);
	else
		$result = $projdb->Execute($query);	
//	$projdb->debug = 0;

	if (!$result->RecordCount()) return;
	echo $navi;
?>

<table id="wydruk" cellspacing=0 cellpadding=0 class="list_table">
<tr>
	<th>Lp</th>
	<th sort="za_numer">Numer zamѓwienia</th>
	<th sort="za_status">Status</th>
	<th sort="ile">WartoЖц netto</th>
</tr>
<?
	$total_zamowien = 0;
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$lp=$i+1+$LIST[start];
		$data=date("d-m-Y, H:i",$za_data);

		$query="SELECT su_nazwisko AS kontrahent, su_miasto, su_kod_pocztowy, su_ulica FROM system_user WHERE su_id=$za_su_id";
		parse_str(ado_query2url($query));

		echo "<tr dbid=\"$za_su_id\"><td>$lp</td>
				<td>$za_numer</td>
				<td>".sysmsg("status_$za_status","status")."</td>			
				<td>".u_cena($ile)."</td>			
		      </tr>";
		$total_zamowien+=$ile;
	}
	echo "<tr>
			<th colspan=3 align=\"right\">".sysmsg("Total","system").":</th>			
			<th align=\"left\">".u_cena($total_zamowien)."</th>			
	      </tr>";

?>

</table>


<?
	include("$SKLEP_INCLUDE_PATH/js.h");
	include("$SKLEP_INCLUDE_PATH/list.h");
	echo $navi;
?>

<script>
	list_table_init('wydruk','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);

	function show_selected_item()
	{
		table=getObject('wydruk');
		if (!table.selectedId) return;

		//document.list_fwd_form.s_id.value=table.selectedId;
		//document.list_fwd_form.submit();
	}

	function list_selected_item()
	{		
	}

</script>
