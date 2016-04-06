<?
	global $AUSER, $SERVER_ID, $GRUPY;

	if (strlen($AUSER[pass]))
		$pass_update = ", su_pass = '$AUSER[pass]'";
	
	$sql = "SELECT COUNT(*) AS jest FROM system_user
			WHERE su_login = '$AUSER[login]' ";
	parse_str(query2url($sql));

	if (!strlen($AUSER[aktywny])) $AUSER[aktywny]=0;

	if ($AUSER[id])
	{
		$sql = "SELECT su_login AS old_login 
				FROM system_user
				WHERE su_id = $AUSER[id]";
		parse_str(query2url($sql));
		if ($old_login != $AUSER[login])
			if ($jest)
			{
				echo "
				<script>
					alert('Login \'".$AUSER[login]."\' jest juП zajъty !');
					history.go(-1);
				</script>
				";
				return;
			}

		$sql = "UPDATE system_user SET
				su_imiona  = '$AUSER[imiona]',
				su_nazwisko  = '$AUSER[nazwisko]',
				su_email  = '$AUSER[email]',
				su_login  = '$AUSER[login]',
				su_ip  = '$AUSER[ip]',
				su_aktywny = $AUSER[aktywny],
				su_parent = $AUSER[parent]
				$pass_update
				WHERE su_id = $AUSER[id]";
		pg_exec($db,$sql);
	}
	else
	{
		if ($jest)
		{
			echo "
			<script>
				alert('Login \'".$AUSER[login]."\' jest juП zajъty !');
				history.go(-1);
			</script>
			";
			return;
		}

		$sql = "INSERT INTO system_user 
				(su_imiona,
				 su_nazwisko,
				 su_server,
				 su_email,
				 su_login,
				 su_pass,
				 su_ip,
				 su_aktywny,
				 su_parent)
				VALUES
				('$AUSER[imiona]',
				 '$AUSER[nazwisko]',
				 $SERVER_ID,
				 '$AUSER[email]',
				 '$AUSER[login]',
				 '$AUSER[pass]',
				 '$AUSER[ip]',
				 $AUSER[aktywny],
				 $AUSER[parent]
				 )";
		pg_exec($db,$sql);

		$sql = "SELECT MAX(su_id) AS maxid FROM system_user
				WHERE su_server = $SERVER_ID";
		parse_str(query2url($sql));
	}

	if (!strlen($maxid))
		$maxid = $AUSER[id];

	$sql = "DELETE FROM system_acl_grupa WHERE sag_user_id = $maxid";
	if (strlen($maxid)) pg_exec($db,$sql);

	if (is_array($GRUPY) && strlen($maxid))
	{
		while(list($key,$val) = each($GRUPY))
			if ($val && strlen($key))
			{
				$sql = "INSERT INTO system_acl_grupa
						(sag_server,sag_grupa_id,sag_user_id)
						VALUES
						($SERVER_ID,$key,$maxid)";
				pg_exec($db,$sql);
			}
	}
	
?>