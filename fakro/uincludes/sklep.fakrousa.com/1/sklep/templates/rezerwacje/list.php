<?
	if ($i>=$res->RecordCount()) 
	{
		$template_loop=0;
		return;
	}
	parse_str(ado_explodename($res,$i));
	$i++;
	$lp=$i;

	$_tr = " class=t1";
	if (($i)%2) $_tr = " class=t2";

	$data_rez = "";
	$godz_rez = "";
	if (strlen($ko_rez_data))
	{
		$data_rez = date("d-m-Y",$ko_rez_data);
		$godz_rez = date("H:i",$ko_rez_data);
	}
	
	$sql = "SELECT COUNT(*) AS total_count FROM koszyk
		WHERE ko_rez_nr = '$ko_rez_nr'
		AND (ko_deadline > $NOW OR ko_deadline IS NULL)
		AND ko_rez_data = $ko_rez_data
		AND ko_su_id = ".$AUTH[parent];
	parse_str(ado_query2url($sql));


	$display_delete = $za_status ? "none" : "";
	
	include("$SKLEP_INCLUDE_PATH/templates/towar_ikony.php");
?>
