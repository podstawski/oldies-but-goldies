<?
	if (!strlen($FORM[id])) return;

	$sql="SELECT count(*) AS c FROM producent WHERE pr_nazwa='$FORM[pr_nazwa]' AND pr_id<>$FORM[id]";
	parse_str(ado_query2url($sql));

	if ($c) $error="Nazwa nie może się powtarzać";

	if (strlen($error)) return;

	$sql ="UPDATE producent SET
			pr_nazwa = '$FORM[pr_nazwa]',
			pr_www = '$FORM[pr_www]',
			pr_kraj = '$FORM[pr_kraj]',
			pr_logo_m = '$FORM[pr_logo_m]',
			pr_logo_d = '$FORM[pr_logo_d]'
			WHERE pr_id = ".$FORM[id];

	$adodb->execute($sql);
	$action_id=$FORM[id];
?>
