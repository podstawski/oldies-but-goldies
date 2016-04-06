<?
	$zam_count = 0;
	if ($AUTH[id] > 0 && strlen($AUTH[parent]))
	{
		$sql = "SELECT COUNT(*) AS zam_count FROM zamowienia WHERE
				za_su_id = ".$AUTH[parent];
		parse_str(ado_query2url($sql));
	}

	echo $zam_count." ";
?>
