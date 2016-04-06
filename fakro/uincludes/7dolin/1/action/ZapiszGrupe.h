<?
	global $ODDZIAL, $SERVER_ID, $GRUPY;

	if ($ODDZIAL[id])
	{
		if (!strlen($ODDZIAL[aktywny])) $ODDZIAL[aktywny] = "NULL";

		$sql = "UPDATE system_user SET
				su_nazwisko = '$ODDZIAL[nazwa]',
				su_ulica = '$ODDZIAL[ulica]',
				su_kod_pocztowy = '$ODDZIAL[kod]',
				su_miasto = '$ODDZIAL[miasto]',
				su_login = '$ODDZIAL[agent]',
				su_aktywny = $ODDZIAL[aktywny]
				WHERE su_id = $ODDZIAL[id]";
		pg_exec($db,$sql);
		$sql = "DELETE FROM system_acl_grupa WHERE
				sag_server = $SERVER_ID AND sag_user_id = $ODDZIAL[id]";
		pg_exec($db,$sql);
	}
	else
	{
		$sql = "INSERT INTO system_user (su_server,su_nazwisko,su_ulica,su_kod_pocztowy,su_miasto,su_login,su_pass,su_aktywny)
				VALUES($SERVER_ID,'$ODDZIAL[nazwa]','$ODDZIAL[ulica]','$ODDZIAL[kod]','$ODDZIAL[miasto]','$ODDZIAL[agent]','".uniqid(rand())."',1)";
		pg_exec($db,$sql);	

		$sql = "SELECT MAX(su_id) AS maxid FROM system_user
				WHERE su_server = $SERVER_ID";
		parse_str(query2url($sql));
	}

	if (!strlen($maxid))
		$maxid = $ODDZIAL[id];

	global $suid;
	$suid = $maxid;

?>