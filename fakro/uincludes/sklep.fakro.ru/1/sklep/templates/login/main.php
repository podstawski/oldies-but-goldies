<?
	if (($login_form_displayed || $AUTH[id]>0) && !$err) 
	{
		$error="&nbsp;";
		return;
	}
	$login_form_displayed=1;

	$sql = "SELECT COUNT(*) AS ile FROM system_user";
	parse_str(query2url($sql));

	if (!$ile) 
		$no_check = "true";
	else
		$no_check = "false";

	if ($cos) 
		$do_focus = "true";
	else
		$do_focus = "false";
	

	$err_log_req = "none";
	
	$err_log_wrg = "none";

	if ($err)
		$err_log_wrg = "inline";

	if (strlen($goto))
	{
		$err_log_req = "inline";
		$next = $goto;
	}

	$sysmsg_user_code = sysmsg("user code","system");
	$sysmsg_password = sysmsg("password","system");
	$sysmsg_login = sysmsg("login","system");

	if ($page == 31)
	{
		$email = "<td>E-mail&nbsp;&nbsp;&nbsp;</td>";
		$pass = "<td>HasГo&nbsp;&nbsp;&nbsp;</td>";
		$txt = "JeЖli jesteЖ juП naszym Klientem, prosimy o zalogowanie siъ.<br>";
	}
?>
