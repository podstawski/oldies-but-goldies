<?
	$producent = $FORM[producent];
	$towar = $FORM[towar];
	$action_id = $towar;
	if (!strlen($producent)) $producent = "NULL";
	if (!strlen($towar)) return;

	$sql = "UPDATE towar SET to_pr_id = $producent
			WHERE to_id = $towar";

	$adodb->execute($sql);
		
?>
