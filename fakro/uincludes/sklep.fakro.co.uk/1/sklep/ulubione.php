<?
	$fav = 0;
	if ($AUTH[id] > 0)
	{
		$sql = "SELECT COUNT(DISTINCT(ul_nazwa)) AS fav FROM ulubione WHERE
				ul_su_id = ".$AUTH[id];
		parse_str(ado_query2url($sql));
	}

	echo $fav." ";
?>
