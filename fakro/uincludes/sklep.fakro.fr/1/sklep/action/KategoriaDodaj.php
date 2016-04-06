<?
	$nazwa = addslashes(stripslashes(trim($FORM[new_indx])));
	$parent = $FORM[new_parent];

	if (!strlen($nazwa)) return;
	
	if (!strlen($parent))
		$sql = "INSERT INTO kategorie (ka_nazwa) VALUES ('$nazwa')";
	else
		$sql = "INSERT INTO kategorie (ka_nazwa,ka_parent) VALUES ('$nazwa',$parent)";

	$projdb->execute($sql);

	$sql = "SELECT MAX(ka_id) AS action_id FROM kategorie";
	parse_str(ado_query2url($sql));
?>
