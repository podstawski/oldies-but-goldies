<?	

	if ($SYSTEM[auth] == 'login')		
	{
		if (strlen($FORM[su_login]))
		{
			$sql = "SELECT COUNT(*) AS jest_login FROM system_user WHERE
					su_login = '".$FORM[su_login]."' AND su_id <> ".$AUTH[id];

			parse_str(ado_query2url($sql));
			if ($jest_login) 
			{
				$error= sysmsg("Login already exists","user");
				return;
			}
		}
	}
	else 
	{
		if (strlen($FORM[su_email]))
		{
			$sql = "SELECT COUNT(*) AS jest_email FROM system_user WHERE
					su_email = '".$FORM[su_email]."' AND su_id <> ".$AUTH[id];

			parse_str(ado_query2url($sql));
			if ($jest_email) 
			{
				$error= sysmsg("Email already in database","user");
				return;
			}
		}
	}

	if (strlen($FORM[su_pass]))
	{
		$new_pass = "su_pass = '".$FORM[su_pass]."',";
		if (!headers_sent()) session_start();

		$AUTH[user]=$FORM[su_login];
		$AUTH[password]=$FORM[su_pass];

		$CAUTH = $SKLEP_SESSION["CAUTH"];
		$CAUTH[password] = $FORM[su_pass];
		$SKLEP_SESSION["CAUTH"] = $CAUTH;
	}

	//$FORM[su_wyroznik3] = substr($FORM[su_wyroznik3],0,1);

	$pola = "";
	reset($FORM);
	while(list($key,$val) = each($FORM))
	{
		if ($key != "zgoda" && $key != "do_commit" && $key != "su_pass" && $key != "su_firma" && $key != "su_nazwisko" && $key != "su_email" && $key != "su_login")
		{
			$pola.= ",$key = '$val'";
		}	
	}


	if ($SYSTEM[auth] == 'login')		
		$email_mod = ",su_email = '".$FORM[su_email]."'";
	else
		if (strlen($FORM[su_login]))
			$email_mod = ",su_login = '".$FORM[su_login]."'";

	$sql = "UPDATE system_user SET
			 $new_pass
			 su_nazwisko = '".$FORM[su_nazwisko]."',
			 su_data_modyfikacji = ".time()."
			 $email_mod
			$pola			 
			WHERE su_id = ".$AUTH[id].";
			 UPDATE system_user SET
			 su_nazwisko = '".$FORM[su_firma]."',
			 su_data_modyfikacji = ".time()."
			$pola			 
			WHERE su_id = (SELECT su_parent FROM system_user WHERE su_id = ".$AUTH[id].")";

	//$adodb->debug=1;
	$adodb->execute($sql);
	//$adodb->debug=0;

	$AUTH=system_user_additional($AUTH);

?>		