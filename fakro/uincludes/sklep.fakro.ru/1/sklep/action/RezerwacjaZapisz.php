<?	
	if ($AUTH[id] <=0 || !strlen($AUTH[parent])) return;
	
	$sql = "UPDATE koszyk SET 
			ko_su_id = ".$AUTH[parent].",
			ko_rez_data = $NOW,
			ko_rez_nr = '".$FORM[reserv_number]."',
			ko_rez_uwagi = '".$FORM[uwagi]."'
			WHERE ko_su_id = ".$AUTH[id]." 
			AND ko_rez_data IS NULL
			AND (ko_deadline > $NOW OR ko_deadline IS NULL)";
		
	$adodb->execute($sql);
?>
