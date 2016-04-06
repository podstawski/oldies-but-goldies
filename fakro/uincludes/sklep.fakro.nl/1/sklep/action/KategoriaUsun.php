<?
	$killkat = $FORM[delid];

	if (strlen($killkat))
	{
		$sql = "SELECT ka_parent FROM kategorie WHERE ka_id = $killkat";
		parse_str(ado_query2url($sql));

		if (!strlen($ka_parent)) $ka_parent = "NULL";

		$sql = "UPDATE kategorie SET 
				ka_parent = $ka_parent 
				WHERE ka_parent = $killkat";

		$projdb->execute($sql);

		$sql = "DELETE FROM kategorie
				WHERE ka_id = $killkat";
		$projdb->execute($sql);

	}
	$action_id=$FORM[delid];
?>
