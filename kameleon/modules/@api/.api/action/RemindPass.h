<?

	$sql = "SELECT * FROM crm_customer 
			WHERE c_email = '$AUTH[c_email]' 
			AND c_server = $SERVER_ID";
	if (strlen($AUTH[email2])) $sql.=" AND c_email2='$AUTH[email2]'";

	$res=$adodb->Execute($sql);

	if ($res->RecordCount())
	{
		$_AUTH=$res->FetchRow(0);
		$wynik="";
		while ( list($key,$val) = each ($_AUTH) )
		{
			$AUTH[$key]=trim($val);
		}
	}
?>
