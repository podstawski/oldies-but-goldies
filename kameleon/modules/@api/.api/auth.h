<?
	if (!strlen($xml)) $xml=$costxt;

	if (strlen($xml))
	{
		$a=xml2obj($xml);
		$xml=$a->xml;
	}

	switch ($xml->method)
	{
		case "username":
			$c_token="c_username";
			break;
		case "email":
			$c_token="c_email";
			break;
		default:
			$c_token=$CAUTH[token];
			break;
	}


	if (!strlen($c_token)) return;


	if (strlen($AUTH[username]) && !$action_progress)
	{
		$CAUTH[username]=$AUTH[username];
	}

	if (strlen($AUTH[password]) && !$action_progress)
	{
		$CAUTH[password]=$AUTH[password];
	}


	$AND="";
	$xml->email2=trim($xml->email2);
	if (!strlen($xml->email2)) $xml->email2 = $AUTH[email2];

	if (strlen($xml->email2)) $AND.=" AND c_email2='$xml->email2'";

	$sql = "SELECT * FROM crm_customer 
			WHERE $c_token = '$CAUTH[username]' 
			AND c_server = $SERVER_ID
			$AND";

	$c_password = "";
	$res=$adodb->Execute($sql);



	$_AUTH=array();
	if ($res->RecordCount())
	{
		$_AUTH=$res->FetchRow(0);
		$wynik="";
		while ( list($key,$val) = each ($_AUTH) )
		{
			$_AUTH[$key]=trim($val);
			if (strlen($_AUTH[$key]))
			{
				if (strlen($wynik)) $wynik.="&";
				$wynik.="AUTH[".$key."]=".urlencode($_AUTH[$key]);
			}
		}
		$c_password = $_AUTH["c_password"];
	}



	while ($_AUTH["c_parent"])
	{
		$sql="SELECT * FROM crm_customer WHERE c_id=".$_AUTH[c_parent];
		$res=$adodb->Execute($sql);
		if (!$res->RecordCount()) break;
		$_AUTH2=$res->FetchRow(0);
		$_AUTH["c_parent"]=$_AUTH2["c_parent"];

		while ( list($key,$val) = each ($_AUTH2) )
		{
			$_AUTH2[$key]=trim($val);
			if (!strlen($_AUTH2[$key])) continue;
			if ($key=="c_id" && !$AUTH2["c_parent"]) $key="c_master_id";
			if (strlen($_AUTH[$key])) continue;
			if ($key=="c_parent") continue;

			$_AUTH[$key]=trim($val);
			if (strlen($wynik)) $wynik.="&";
			$wynik.="AUTH[".$key."]=".urlencode($_AUTH[$key]);
		}
		
	}


	if (!strlen($c_password) || ( $c_password != $CAUTH[password] && $c_password != crypt($CAUTH[password],$c_password)) )	$error = label("no access");

	if (strlen($error)) 
	{
		if (!$action_progress) echo "$error";
	}
	else
	{
		$wynik.="&AUTH[username]=".urlencode($CAUTH[username]);
		$wynik.="&AUTH[password]=".urlencode($CAUTH[password]);
		$wynik.="&AUTH[token]=$c_token";
		if (!$action_progress) echo label("access granted").":$wynik";
	}

?>