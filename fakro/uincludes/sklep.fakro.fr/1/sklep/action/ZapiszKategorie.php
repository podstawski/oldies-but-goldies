<?
	
	if (!strlen($FORM[parent_id])) $FORM[parent_id] = "NULL";
	
	$indx = "ka_opis_m_".$lang;

	$sql = "
	UPDATE kategorie SET
	ka_parent = ".$FORM[parent_id].",
	ka_nazwa = '".$FORM[ka_nazwa]."',
	ka_foto_m = '".$FORM[ka_foto_m]."',
	ka_foto_d = '".$FORM[ka_foto_d]."',
	ka_opis_m_$lang = '".$FORM[$indx]."'
	WHERE ka_id = ".$FORM[id];
	$action_id = $FORM[id];
	$projdb->execute($sql);

?>
