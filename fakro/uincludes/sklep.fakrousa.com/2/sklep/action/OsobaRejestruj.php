<?	
	$sg = $SKLEP_SESSION["sg"];
	$adodb->debug=0;
	$FORM[su_nip]=ereg_replace("[^0-9]","",$FORM[su_nip]);
	$FORM[su_email] = strtolower($FORM[su_email]);

//	if (!strlen($FORM[su_firma])) $FORM[su_firma] = $FORM[su_nazwisko]." ".$FORM[su_imiona];


	if (!strlen($FORM[su_firma]))
		$login = ereg_replace("[^a-z0-9]","",strtolower(unPolish($FORM[su_nazwisko].$FORM[su_imiona])));
	else
		$login = ereg_replace("[^a-z0-9]","",strtolower(unPolish($FORM[su_firma])));

	if (strlen($FORM[su_kod_pocztowy]) > 6) $FORM[su_kod_pocztowy] = substr($FORM[su_kod_pocztowy],0,6);

	$adodb->BeginTrans();

	$sql = "SELECT COUNT(*) AS jest_login FROM system_user WHERE su_login = '$login'";
	parse_str(ado_query2url($sql));

	if ($jest_login) 
	{
		$jest_login = 0;
		for ($i=0; $i < 100; $i++)
		{
			$sql = "SELECT COUNT(*) AS jest_login FROM system_user WHERE su_login = '$login".$i."'";
			parse_str(ado_query2url($sql));
			if (!$jest_login)
			{
				$login.=$i;
				break;
			}
		}

		if ($jest_login)
		{
			$error= sysmsg("Login already exists","user");
			$adodb->RollbackTrans();
			return;
		}
	}

	$pola = "";
	$wartosci = "";
	reset($FORM);
	while(list($key,$val) = each($FORM))
		if (substr($key,0,3) == "su_" && $key != "su_login" && $key != "su_firma" && $key != "su_nazwisko" && $key != "su_email")
		{
			$pola.= ",$key";
			$wartosci.= ",'$val'";
		}

	$sql = "INSERT INTO system_user 
			(su_nazwisko,
			 su_server,
			 su_login,
 			 su_data_dodania
			 $pola)
			VALUES 
			('".$FORM[su_firma]."',
			 $SERVER_ID,
			 '$login',
			 $NOW
			 $wartosci
			 ); 
			SELECT MAX(su_id) AS su_parent FROM system_user WHERE su_server = $SERVER_ID";

	parse_str(ado_query2url($sql));
	
	if (!strlen($su_parent)) 
	{
		$adodb->RollbackTrans();
		return;
	}

	unset($jest_login);

	if ($SYSTEM[auth] == 'login')
	{
		$sql = "SELECT COUNT(*) AS jest_login FROM system_user WHERE su_login = '".$FORM[su_login]."'";
		parse_str(ado_query2url($sql));

		if ($jest_login)
		{
			$error= sysmsg("Login already exists","user");
			$adodb->RollbackTrans();
			return;
		}
	}
	else
	{
		$sql = "SELECT COUNT(*) AS jest_login FROM system_user WHERE su_email = '".$FORM[su_email]."'";
		parse_str(ado_query2url($sql));

		if ($jest_login)
		{
			$error= sysmsg("Email already in database","user")." ".$FORM[su_email];
			$adodb->RollbackTrans();
			return;
		}
	}

	$sql = "INSERT INTO kontrahent_sklep (ks_sk_id,ks_su_id) VALUES ($SKLEP_ID,$su_parent)";

	if (!$adodb->execute($sql))
	{
		$adodb->RollbackTrans();
		return;
	}

	$pola = "";
	$wartosci = "";
	reset($FORM);
	while(list($key,$val) = each($FORM))
		if (substr($key,0,3) == "su_" && $key != "su_firma")
		{
			$pola.= ",$key";
			$wartosci.= ",'$val'";
		}

	$sql = "INSERT INTO system_user 
			(su_server,
			 su_parent,
			 su_data_dodania
			 $pola			 
			)
			VALUES 
			($SERVER_ID,
			 $su_parent,
			 $NOW
			 $wartosci			 
			);
			 SELECT MAX(su_id) AS last_id FROM system_user WHERE su_server = $SERVER_ID";
	
	$res = $adodb->execute($sql);
	if (is_object($res))
	{
		parse_str(ado_explodename($res,0));
		/*
			Grupy
		*/
		$grupy = explode(":",$sg);
		$sql = "";
		for ($i=0; $i < count($grupy); $i++)
		{
			if (!strlen(trim($grupy[$i]))) continue;
			$sql.= "INSERT INTO system_acl_grupa 
					(sag_grupa_id, sag_user_id, sag_server)
					VALUES (".$grupy[$i].",$last_id,$SERVER_ID);";
		}
		if ($adodb->execute($sql))
		{
			$adodb->CommitTrans();
//	 		$adodb->RollbackTrans();
			
			if ($SYSTEM[auth] == 'login')
				$AUTH[user] = $FORM[su_login];
			else
				$AUTH[user] = $FORM[su_email];

			$AUTH[password] = $FORM[su_pass];
			$action_id = $last_id;
			include($SKLEP_INCLUDE_PATH."/autoryzacja/auth.h");
		}
		else
			$adodb->RollbackTrans();
	}
	else
 		$adodb->RollbackTrans();
	$adodb->debug=0;
?>		
