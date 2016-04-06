<?
	if ($AUTH[id] <=0 || !strlen($AUTH[parent])) return;

	$ko_rez_data = $FORM[ko_rez_data];
	if (!strlen($ko_rez_data)) return;
	
	$sql = "DELETE FROM koszyk 
			WHERE ko_su_id = ".$AUTH[parent]." 
			AND ko_rez_data = $ko_rez_data";
	
	$adodb->execute($sql);
?>
