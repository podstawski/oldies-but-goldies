<?
	if (!strlen($FORM[id])) return;
	
	$sql = "UPDATE kategorie SET
			ka_opis_d_$lang = '".$FORM["ka_opis_d_$lang"]."'
			WHERE ka_id = ".$FORM[id].";";
	$adodb->execute($sql);
	$action_id=$FORM[id];
?>
