<?
	$u=strtolower(addslashes(stripslashes($K_USERS[name])));

	if (strstr($u,":")) $error=label("Invalid characters submited");

	$query="SELECT count(*) AS c FROM kameleon_acl_users
			WHERE kau_server=$SERVER_ID AND kau_username='$u'";
	parse_str(ado_query2url($query));
	if ($c)  $error=label("Username exists");

	if (strlen($error)) return;


	$query="INSERT INTO kameleon_acl_users (kau_server,kau_username)
			VALUES ($SERVER_ID,'$u')";

	if ($adodb->Execute($query)) logquery($query);


?>