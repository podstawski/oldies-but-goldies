<?
	$za_id = $FORM["za_id"];
	
	if (!strlen($za_id) || $AUTH[id] < 0 || !strlen($AUTH[parent])) return;
	
	$sql = "DELETE FROM zamowienia WHERE za_id = $za_id 
			AND za_su_id = ".$AUTH[parent]." AND za_status = 0";
	
	$adodb->execute($sql);
	$action_id=$FORM[za_id];
?>
