<?
	$nazwa = $FORM[new_indx];

	if (!strlen($nazwa)) return;

	$sql = "SELECT COUNT(*) AS jest FROM producent WHERE pr_nazwa = '$nazwa'";
	parse_str(ado_query2url($sql));

	if (!$jest)
	{
		$sql = "INSERT INTO producent (pr_nazwa) VALUES ('$nazwa')";
		$projdb->execute($sql);
		$sql = "SELECT MAX(pr_id) AS action_id FROM producent";
		parse_str(ado_query2url($sql));
	}

	$LIST[szukaj] = $nazwa;
?>
