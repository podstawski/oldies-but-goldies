<?
	$sql = "SELECT COUNT(DISTINCT(ul_nazwa)) AS fav FROM ulubione WHERE
		ul_su_id = ".$AUTH[id];
	parse_str(ado_query2url($sql));

	while(1)
	{
		$name=sysmsg("Set","system")." ".($fav+1);
		$c=0;
		$sql="SELECT count(*) AS c FROM ulubione WHERE ul_nazwa='$name'
			AND ul_su_id=$AUTH[id]";
		parse_str(ado_query2url($sql));
		if (!$c) break;
		$fav++;
	}


	$query="INSERT INTO ulubione (ul_su_id,ul_to_id,ul_ilosc,ul_nazwa)
		SELECT ko_su_id,ts_to_id,ko_ilosc,'$name'
		FROM koszyk,towar_sklep WHERE ko_su_id=$AUTH[id]
		AND (ko_deadline > $NOW OR ko_deadline IS NULL) AND ko_ts_id=ts_id";


	$projdb->Execute($query);

?>
