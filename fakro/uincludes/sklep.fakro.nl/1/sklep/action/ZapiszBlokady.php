<?
	$blokady="";
	if (is_array($FORM)) $blokady=":".implode(":",array_keys($FORM)).":";

	$query="UPDATE system_user SET su_blokady='$blokady' WHERE su_id=$LIST[id]";
	$projdb->Execute($query);
	$action_id = $LIST[id];
?>
