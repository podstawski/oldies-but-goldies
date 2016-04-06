<?
	if ($AUTH[id]<=0)
	{
		$error="user ma problem egzystencjalny";
		return;	
	}

	$slash="";
	if (strlen($AUTH[nazwa])) $slash="/";
?>
