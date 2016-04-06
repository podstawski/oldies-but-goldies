<?
	include("$SKLEP_INCLUDE_PATH/raporty/daty.php");
	echo "<B>Zakres czasowy:</B> ".humandate($od)." - ".humandate($do)."<br>";


	if (strlen(trim($_REQUEST[raport_indeks])))
	{
		$ri=$_REQUEST[raport_indeks]+0;
		if ($ri>0)
		{
			$add_sql = "AND sal_klucz = '".$_REQUEST[raport_indeks]."' ";
			echo "<B>Indeks obiektu:</B> ".$_REQUEST[raport_indeks]."<br>";
		}
		else
		{
			$add_sql = "AND sal_action = '".$_REQUEST[raport_indeks]."' ";
			echo "<B>Nazwa akcji:</B> ".$_REQUEST[raport_indeks]."<br>";
		}
	}


	if (!strlen($LIST[sort_f])) 
	{
		$LIST[sort_f]="sal_data";
		$LIST[sort_d]=1;
	}
//	$projdb->debug=1;
	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$FROMWHERE="FROM system_action_log, system_user
				WHERE sal_user_id = su_id
				$add_sql
				AND sal_data>=$od AND sal_data<=$do";

	

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
	<th sort="sal_klucz">Indeks Obiektu</th>
	<th sort="su_nazwisko">Osoba</th>
	<th sort="su_parent">Firma</th>
	<th sort="sal_data">Data</th>
	<th sort="sal_action">Akcja</th>
</tr>
<?
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$lp=$i+1+$LIST[start];
		$data=date("d-m-Y, H:i",$sal_data);

		$query="SELECT su_nazwisko AS kontrahent FROM system_user WHERE su_id=$su_parent";
		parse_str(ado_query2url($query));

		echo "<tr dbid=\"$sal_id\"><td>$lp</td>
				<td>$sal_klucz</td>			
				<td>$su_nazwisko $su_imiona</td>			
				<td>$kontrahent</td>
				<td>$data</td>			
				<td>$sal_action</td>			
		      </tr>";
	}

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

		document.list_fwd_form.s_id.value=table.selectedId;
		document.list_fwd_form.submit();
	}

	function list_selected_item()
	{		
	}

</script>
