<?
	include("$SKLEP_INCLUDE_PATH/raporty/daty.php");
	include("$SKLEP_INCLUDE_PATH/raporty/where.php");

	if (!strlen($LIST[sort_f])) 
	{
		$LIST[sort_f]="ile";
		$LIST[sort_d]=1;
	}

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$FROMWHERE="FROM zampoz, zamowienia, towar_sklep, towar
				WHERE ts_to_id = to_id AND zp_za_id = za_id 
				AND ts_id = zp_ts_id AND za_data>=$od AND za_data<=$do
				$TOWAR_SKLEP_WHERE $ZAMOWIENIE_WHERE";

	

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}

	$navi=$size?navi($self,$LIST,$size):"";
	
	$query="SELECT COUNT(zp_ts_id) as ile , to_indeks
			$FROMWHERE GROUP BY to_indeks ORDER BY $sort";
	if (strlen($navi))
		$result = $projdb->SelectLimit($query,$size,$LIST[start]+0);
	else
		$result = $projdb->Execute($query);	


	if (!$result->RecordCount()) return;
	echo $navi;
?>

<table id="wydruk" cellspacing=0 cellpadding=0 class="list_table">
<tr>
	<th>Lp</th>
	<th sort="to_indeks">Indeks towaru</th>
	<th sort="ile">IloЖц</th>
</tr>
<?
	$total = 0;
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$lp=$i+1+$LIST[start];
		$data=date("d-m-Y, H:i",$za_data);

		echo "<tr dbid=\"$to_indeks\"><td>$lp</td>
				<td>$to_indeks</td>			
				<td>$ile</td>			
		      </tr>";
		$total+=$ile;
	}
	echo "<tr>
			<th colspan=2 align=\"right\">".sysmsg("Total","system").":</th>			
			<th align=\"left\">$total</th>			
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
