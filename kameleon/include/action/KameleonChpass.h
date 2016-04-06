<?
	if (!strlen($_REQUEST[np]))
	{
		//$error=label("Submit new password");
		return;
	}

	if ($_REQUEST[np]!=$_REQUEST[rp])
	{
		$error=label("The new and retyped password don't match");
		return;
	}




	if (is_object($auth_acl))
	{
		if (!$auth_acl->userPass($_REQUEST[np],$_REQUEST[op]) )
		{
			$error=label("Old password does not match");
		}
		return;
	}


	$PASSWORD=$_REQUEST[op];

	$query="SELECT * FROM passwd WHERE username='".$KAMELEON[username]."'";
	parse_str(ado_query2url($query));

	if ( $password!=$PASSWORD && $password!=crypt($PASSWORD,$password) || !strlen($password) || !strlen($PASSWORD))
	{
		$error=label("Old password does not match");
		return;
	}



	$password=crypt($_REQUEST[np]);
	$adodb->addToSession('login.phash',md5($password),true);
	
	$query="UPDATE passwd SET password='$password' WHERE username='".$KAMELEON[username]."'";
	$adodb->execute($query);

	$dn=dirname($SCRIPT_NAME);
	if (strlen($dn)==1) $dn='';
	SetCookie('wkp','0',time()+365*24*3600,$dn.'/login.php');

