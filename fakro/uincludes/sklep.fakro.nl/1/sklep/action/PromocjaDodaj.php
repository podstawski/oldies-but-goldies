<?
	$nazwa = $FORM[new_indx];

	if (!strlen($nazwa)) return;

	if (strlen($pm_symbol) > 32)
		$pm_symbol = substr($pm_symbol,0,31);

	$sql = "SELECT COUNT(*) AS jest FROM promocja WHERE pm_symbol = '$nazwa'";
	parse_str(ado_query2url($sql));

	if (!$jest)
	{
		$sql = "INSERT INTO promocja (pm_symbol) VALUES ('$nazwa')";
		$projdb->execute($sql);
		$sql = "SELECT MAX(pm_id) AS action_id FROM promocja";
		parse_str(ado_query2url($sql));
	}

	$LIST[szukaj] = $nazwa;
?>
