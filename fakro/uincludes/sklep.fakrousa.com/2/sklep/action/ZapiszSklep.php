<?
	global $SKLEPY;

	$sklepy=implode(",",array_keys($SKLEPY));
	if (!strlen($sklepy)) $sklepy="0";

	$sql = "DELETE FROM kontrahent_sklep WHERE ks_sk_id NOT in ($sklepy) AND ks_su_id = ".$FORM[id].";";
	if (is_array($SKLEPY))
		while (list($key,$val) = each($SKLEPY))
			if ($val) $sql.= "INSERT INTO kontrahent_sklep (ks_sk_id, ks_su_id) 
								SELECT $key,".$FORM[id]." 
								WHERE $key NOT IN (SELECT ks_sk_id FROM kontrahent_sklep WHERE ks_su_id = $FORM[id]);";

	if (!$projdb->Execute($sql)) $error=$dberror;
	$action_id = $FORM[id];
?>
