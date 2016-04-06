<?
	$su_id=0;
	$email=strtolower($FORM[su_email]);
	$query="SELECT * FROM system_user WHERE su_email='$email' AND su_parent>0";
	parse_str(ado_query2url($query));

	$WM->sysinfo=sysmsg("Information was sent to the submited email address","system");

?>