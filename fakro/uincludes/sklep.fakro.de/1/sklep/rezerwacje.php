<?
	$res_count = 0;
	if ($AUTH[id] > 0 && strlen($AUTH[parent]))
	{
		$sql = "SELECT COUNT(DISTINCT(ko_rez_data)) AS res_count FROM koszyk WHERE
				ko_su_id = ".$AUTH[parent]." AND ko_rez_data IS NOT NULL 
				AND (ko_deadline > $NOW OR ko_deadline IS NULL)";
		parse_str(ado_query2url($sql));
	}

	echo $res_count." ";
?>
