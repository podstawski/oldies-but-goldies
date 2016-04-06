<?
	$exclude_warunek='';
	if (is_array($mailing_exclude) )
	{
		reset($mailing_exclude);
		while ( list( $key, $val ) = each( $mailing_exclude )	)
			$exclude_warunek.=" AND c_email2<>'$key'";
	}


	$query="SELECT c_email, c_person FROM crm_customer
			WHERE c_email IS NOT NULL AND c_email <>'' 
			AND c_email LIKE '%@%' $exclude_warunek
			ORDER BY c_email";

	$res=$adodb->execute($query);

	unset ($mailbcc);
	for ($i=0;$i<$res->recordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));
		if (!$mailing[test]) $mailbcc[]=trim("$c_person <$c_email>");
 	}

	$sysinfo="Mail zostaГ rozesГany do ".count($mailbcc). " osѓb.";
?>
