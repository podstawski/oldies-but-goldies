<?
	include("$SKLEP_INCLUDE_PATH/raporty/daty.php");
	include("$SKLEP_INCLUDE_PATH/raporty/where.php");

	if (!strlen($LIST[sort_f])) $LIST[sort_f]="nz_data";

	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$FROMWHERE="FROM nieudane_zakupy,towar,towar_sklep,system_user
				WHERE nz_ts_id=ts_id AND ts_to_id=to_id AND nz_su_id=su_id
				AND nz_data>=$od AND nz_data<=$do
				$TOWAR_SKLEP_WHERE";

	

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}

	$navi=$size?navi($self,$LIST,$size):"";
	
	$query="SELECT * $FROMWHERE ORDER BY $sort";



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
	<th sort="nz_data">Data</th>
	<th sort="to_indeks">Symbol</th>
	<th sort="nz_dostepne">By³o</th>
	<th sort="nz_proba">Próba</th>
	<th sort="su_parent">Kontrahent</th>
</tr>
<?
	$dost = 0;
	$proba = 0;
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$lp=$i+1+$LIST[start];
		$data=date("d-m-Y, H:i",$nz_data);

		$query="SELECT su_nazwisko AS kontrahent FROM system_user WHERE su_id=$su_parent";
		parse_str(ado_query2url($query));

		echo "<tr dbid=\"$nz_id\"><td>$lp</td>
				<td>$data</td>
				<td>$to_indeks</td>
				<td>$nz_dostepne</td>
				<td>$nz_proba</td>
				<td>$kontrahent | $su_imiona $su_nazwisko</td>			
		      </tr>";
		$dost+=$nz_dostepne;
		$proba+=$nz_proba;
	}
	echo "<tr>
			<th colspan=3 align=\"right\">".sysmsg("Total","system").":</th>
			<th align=\"left\">$dost</th>
			<th align=\"left\">$proba</th>
			<th></th>			
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
