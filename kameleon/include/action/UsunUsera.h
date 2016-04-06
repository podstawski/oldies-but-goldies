<?
	$u=addslashes(stripslashes($K_USERS[name]));
	$g=$u;



	$query="DELETE FROM kameleon_acl_users
			WHERE kau_server=$SERVER_ID AND kau_username='$u';
		DELETE FROM kameleon_acl
			WHERE ka_server=$SERVER_ID AND ka_username='$u';";
	if ($adodb->Execute($query)) logquery($query);
	else return;


	$query="SELECT kau_inherits,kau_username AS u FROM kameleon_acl_users
			WHERE kau_server=$SERVER_ID";

	$res=$adodb->Execute($query);
	for ($i=0;$i<$res->RecordCount();$i++ )
	{
		parse_str(ado_explodeName($res,$i));


		if (strstr($kau_inherits,":$g:"))
		{

			$kau_inherits=ereg_replace(":$g:",":",$kau_inherits);
	
			$query="UPDATE kameleon_acl_users SET kau_inherits='$kau_inherits'
				WHERE kau_server=$SERVER_ID AND kau_username='$u'";

			if ($adodb->Execute($query)) logquery($query);
		}

	}


	

?>