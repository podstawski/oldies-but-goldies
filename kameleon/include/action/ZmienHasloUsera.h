<?
	$u=strtolower(addslashes(stripslashes($K_USERS[name])));
	$p=trim(addslashes(stripslashes($K_USERS[pass])));

	if (strlen($error)) return;


	$query="UPDATE kameleon_acl_users SET kau_password='$p'
			WHERE kau_server=$SERVER_ID AND kau_username='$u'";

	if (strlen($p)) if ($adodb->Execute($query)) logquery($query);

