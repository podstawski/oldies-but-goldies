<?
	$nazwa = $FORM[new_indx];

	if (!strlen($nazwa)) return;

	$sql = "SELECT COUNT(*) AS jest FROM towar WHERE to_indeks = '$nazwa'";
	parse_str(ado_query2url($sql));

	if (!$jest)
	{
		$sql = "INSERT INTO towar (to_indeks) VALUES ('$nazwa')";
		$projdb->execute($sql);
		$sql = "SELECT MAX(to_id) AS action_id FROM towar";
		parse_str(ado_query2url($sql));
		if (strlen($CIACHO[kateg]))
		{
			$sql = "INSERT INTO towar_kategoria (tk_to_id, tk_ka_id)
					VALUES ($action_id,".$CIACHO[kateg].")";
			$projdb->execute($sql);
		}
	}
	else
	{
		$error = "Podany indeks jest w uПyciu !";
	}

	$LIST[szukaj] = $nazwa;
?>
