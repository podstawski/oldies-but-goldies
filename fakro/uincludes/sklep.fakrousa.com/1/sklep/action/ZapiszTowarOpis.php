<?
	if (!strlen($FORM[id])) return;

	$sql = "UPDATE towar SET
			to_opis_d_$lang = '".$FORM["to_opis_d_$lang"]."',
			to_att = '$FORM[to_att]'
			WHERE to_id = ".$FORM[id].";";
	$adodb->execute($sql);
	$action_id=$FORM[id];
?>
