<?
	$query="DELETE FROM ulubione WHERE ul_su_id=$AUTH[id] AND ul_nazwa='$FORM[ul_nazwa]'";

	$projdb->Execute($query);
?>
