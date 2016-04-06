<?

if ($AUTH[id]>0)
{
	$query="SELECT su_email,su_telefon,su_gsm FROM system_user WHERE su_id=".$AUTH[id];
	parse_str(ado_query2url($query));

	$za_email=$su_email;
	$za_telefon=$su_telefon;
	if (strlen($za_telefon)) $za_telefon=$su_gsm;

	if (strlen($za_telefon))
	{
		$query="SELECT su_telefon,su_gsm FROM system_user WHERE su_id=".$AUTH[parent];
		parse_str(ado_query2url($query));

		$za_telefon=$su_telefon;
		if (strlen($za_telefon)) $za_telefon=$su_gsm;
	}

}

?>
