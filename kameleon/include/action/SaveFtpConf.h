<?
	
	if ( !$kameleon->checkRight('publish','page'))
	{
		$error=$norights;
		return;
	}	

	$query="SELECT count(*) AS editor_count FROM rights 
			WHERE server=$SERVER_ID AND (nexpire IS NULL OR nexpire<=".time().")";
	parse_str(ado_query2url($query));



	if ($editor_count!=1 && !$ADMIN_RIGHTS) return;

	$s=explode(":",$ftp_server);
	$_ftp_server=$s[0];
	$_ftp_port=$s[1]+0;
	if (!$ftp_port) $ftp_port=21;

	$result=@ftp_connect($_ftp_server,$_ftp_port);	
	$FTP_CONN=$result;
	if (!$result) $error=label("Server")." $_ftp_server:$_ftp_port ".label("does not respond.");

	if (strlen($error)) return;

	if (strlen($ftp_user) && strlen($ftp_pass) )
	{
		$result=@ftp_login($FTP_CONN, $ftp_user, $ftp_pass);
		if (!$result) $error=label("Username or password seems to be incorrect")." !";
	}

	ftp_quit($FTP_CONN);

	$query="UPDATE servers SET ftp_server='$ftp_server', ftp_user='$ftp_user', ftp_dir='$ftp_dir'";
	if (strlen($ftp_user) && strlen($ftp_pass) ) $query.=", ftp_pass='$ftp_pass'";
	$query.="\nWHERE id=$SERVER_ID";

	//$server_configuration=1;
	//echo nl2br($query);return;

	if ($adodb->Execute($query)) logquery($query);
	

