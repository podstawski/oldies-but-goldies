<?
	include("$SKLEP_INCLUDE_PATH/raporty/daty.php");
	echo "<B>Zakres czasowy:</B> ".humandate($od)." - ".humandate($do)."<br>";


	if (strlen(trim($_REQUEST[raport_indeks])))
	{
		$add_sql = "AND to_indeks ~* '".$_REQUEST[raport_indeks]."' ";
		echo "<B>Indeks towaru:</B> ".$_REQUEST[raport_indeks]."<br>";
	}


	if (!strlen($LIST[sort_f])) 
	{
		$LIST[sort_f]="ru_data";
		$LIST[sort_d]=1;
	}
//	$projdb->debug=1;
	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$FROMWHERE="FROM ruchy, towar_sklep, towar, system_user
				WHERE ru_su_id = su_id
				AND ru_ts_id = ts_id
				AND ts_to_id = to_id
				$add_sql
				AND ru_data>=$od AND ru_data<=$do";

	

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}

	$navi=$size?navi($self,$LIST,$size):"";
	
	$query="SELECT *
			$FROMWHERE ORDER BY $sort";

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
	<th sort="to_indeks">Indeks Artykułu</th>
	<th sort="ru_zmiana">Zmiana</th>
	<th sort="ru_stan">Stan</th>
	<th sort="ru_data">Data</th>
	<th sort="ru_uwagi">Uwagi</th>
</tr>
<?
	$total_z=0;
	$total_s=0;
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$lp=$i+1+$LIST[start];
//		$data=date("d-m-Y, H:i",$za_data);

//		$query="SELECT su_nazwisko AS kontrahent, su_miasto, su_kod_pocztowy, su_ulica FROM system_user WHERE su_id=$su_parent";
//		parse_str(ado_query2url($query));

		echo "<tr dbid=\"$ru_id\"><td>$lp</td>
				<td>$to_indeks</td>			
				<td>$ru_zmiana</td>			
				<td>$ru_stan</td>
				<td>".date("d-m-Y, H:i",$ru_data)."</td>			
				<td>".stripslashes($ru_uwagi)."</td>			
		      </tr>";
		$total_z+=$ru_zmiana;
		$total_s+=$ru_stan;
	}
	echo "<tr>
			<th colspan=2 align=\"right\">".sysmsg("Total","system").":</th>			
			<th align=\"left\">$total_z</th>			
			<th align=\"left\">$total_s</th>			
			<th colspan=2></th>
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
