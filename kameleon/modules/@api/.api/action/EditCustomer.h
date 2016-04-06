<?
	if (!$_AUTH[c_id])
	{
		if (!isset($auth_required)) $auth_required=1;
		return;
	}



	if (strlen($AUTH[username]))
	{
		$query="SELECT count(*) AS c FROM crm_customer 
				WHERE c_username='$AUTH[username]' 
				AND c_server=$SERVER_ID
				AND c_id<>$_AUTH[c_id]";
		parse_str(query2url($query));
		if ($c) $error=label("Username exists")." ($AUTH[username])";
	}

	if (strlen($AUTH[c_email]))
	{
		$query="SELECT count(*) AS c FROM crm_customer 
				WHERE c_email='$AUTH[c_email]' 
				AND c_server=$SERVER_ID
				AND c_id<>$_AUTH[c_id]";
		parse_str(query2url($query));
		if ($c) $error=label("Email exists")." ($AUTH[c_email])";
	}


	if (strlen($error)) return;




	push ($INCLUDE_PATH);
	$INCLUDE_PATH="";
	include("include/modules.h");
	$INCLUDE_PATH=pop();	


	if (strlen(trim($AUTH[password]))) 
	{
		$pass=trim($AUTH[password]);
		$JScript="document.cookie = \"CAUTH[password]=$pass\"; ";
		$AUTH[c_password]=$pass;
	}

	
	//$adodb->debug=1;

	module_update($MODULES->api->files->login,"c_id=$_AUTH[c_id] AND c_server=$SERVER_ID");

?>