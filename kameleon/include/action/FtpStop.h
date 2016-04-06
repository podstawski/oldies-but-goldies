<?
	$ftpid+=0;
	$username="";
	$query="SELECT * from FTP WHERE id=$ftpid";
	parse_str(ado_query2url($query));

	if ($username!=$PHP_AUTH_USER && !$ADMIN_RIGHTS) $error=label("You did not start the process")." !";

	if (strlen($error)) return;



	if ($pid) if (function_exists('posix_kill')) posix_kill($pid,9);


	$rozkaz=label("Process terminated");

	$query="";
	if (!$t_begin) 	$query.="\nUPDATE ftp SET t_begin=".time()." WHERE id=$ftpid;";


	$query.="\nINSERT INTO ftplog(ftp_id,nczas,rozkaz,wynik) VALUES ($ftpid,".time().",'$rozkaz','OK');";

	$query.="\nUPDATE ftp SET t_end=".time().",killed='1' WHERE id=$ftpid;";

	//echo nl2br($query); return;
	$adodb->Execute($query);
?>