<?
	$KATS = $SKLEP_SESSION[kategorie];

	$qs = $LIST[szukaj];

	$qs = trim($qs);
	if (!strlen($qs)) 
	{
		$error="&nbsp;";
		return;
	}
	$qs = ereg_replace("  "," ",$qs);
	$qs = strtolower($qs);
	$slowa = explode(" ",$qs);

	$first_pass = array();
	while (list($key,$val) = each($KATS))
	{
		for ($i=0; $i < count($slowa); $i++)
		{
			if (!strlen(trim($slowa[$i]))) continue;
			if (!strlen(trim($slowa[$i])) == "+") continue;
			if (substr($slowa[$i],0,1) == "+") continue;
			if (strpos($key,strtolower($slowa[$i])))
				$first_pass[$key] = $val;
		}
	}
	$last_pass = array();
	$any = 0;
	reset($first_pass);
	while (list($key,$val) = each($first_pass))
	{
		for ($i=0; $i < count($slowa); $i++)
		{
			if (!strlen(trim($slowa[$i]))) continue;
			if (!strlen(trim($slowa[$i])) == "+") continue;
			if (substr($slowa[$i],0,1) != "+") continue;
			$slowa[$i] = substr($slowa[$i],1);
			$any = 1;
			if (strpos($key,strtolower($slowa[$i])))
				$last_pass[$key] = $val;
		}
	}

	if (!$any) $last_pass = $first_pass;
	reset($last_pass);

	if (!count($last_pass)) $error="&nbsp;";
	
?>
