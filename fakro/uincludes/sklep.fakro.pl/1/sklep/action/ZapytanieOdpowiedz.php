<?
	if (!$FORM[za_id]) return;
	$action_id=$FORM[za_id];

	$query="UPDATE zapytania SET za_odp='$FORM[za_odp]', za_odp_data=$NOW, za_odp_su_id=$AUTH[id]
			WHERE za_id=$action_id";
	$projdb->execute($query);
?>
