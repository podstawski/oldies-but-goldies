<?
	$u=addslashes(stripslashes($K_USERS[name]));
	$g=addslashes(stripslashes($K_USERS[group]));



	$query="SELECT count(*) AS c FROM kameleon_acl_users
			WHERE kau_server=$SERVER_ID AND kau_username='$g'";
	parse_str(ado_query2url($query));
	if (!$c) return;



	$query="SELECT kau_inherits FROM kameleon_acl_users
			WHERE kau_server=$SERVER_ID AND kau_username='$u'";
	parse_str(ado_query2url($query));
	if (strstr($kau_inherits,":$g:"))
	{

		$kau_inherits=ereg_replace(":$g:",":",$kau_inherits);
	
		$query="UPDATE kameleon_acl_users SET kau_inherits='$kau_inherits'
			WHERE kau_server=$SERVER_ID AND kau_username='$u'";

		if ($adodb->Execute($query)) logquery($query);
	}

	
