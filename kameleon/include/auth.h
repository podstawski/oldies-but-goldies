<?php

if (!function_exists('unauthorize'))
{
	function unauthorize($info=null)
	{
		global $adodb,$auth_acl;
		global $ADMIN_MODE;

		if ($ADMIN_MODE==-1) return;

		$path = $_SERVER['REQUEST_URI'];
		
		//Komunikat do formularza logowania idzie przez sesje
		$adodb->addToSession('login.info',$info,true);
		$adodb->addToSession('login.path',$path,true);
		$adodb->delSessionVar('login.alreadyLogedIn');
		//Jak nie ma loginu ma sie za³adowaæ formularz logowania
		$login='login.php';
		$limit=5;
		while ($limit--)
		{
			if (file_exists($login)) break;
			$login="../$login";
		}

		header("Location: $login");
		echo "<script language=\"javascript\">location.href='$login'</script>";
		$adodb->Close();

		if (is_object($auth_acl)) 
		{
			$auth_acl->puke_debug_as_comment();
			$auth_acl->close();
		}
		die();
	}
}

if (!function_exists('login_time_server'))
{
	function login_time_server($USERNAME)
	{
		global $adodb;

		$query="SELECT server,tin 
				FROM login WHERE username='$USERNAME'
				AND server IN (SELECT id FROM servers WHERE id=login.server)
				ORDER BY tin DESC LIMIT 1";
		parse_str(ado_query2url($query));


		if ($tin)
		{
			$tin-=24*3600;
			$query="SELECT sum(tout-tin) AS total FROM login WHERE username='$USERNAME' AND tin<$tin";
			parse_str(ado_query2url($query));
			$total+=0;
			$query="
				UPDATE passwd SET total_time=total_time+$total WHERE total_time IS NOT NULL AND username='$USERNAME';
				UPDATE passwd SET total_time=$total WHERE total_time IS NULL AND username='$USERNAME';
				INSERT INTO login_arch SELECT * FROM login WHERE tin<$tin AND username='$USERNAME';
				DELETE FROM login WHERE tin<$tin AND username='$USERNAME';";
			$adodb->Execute($query);

		}

		return $server;
	}
}


if ($_REQUEST['ADMIN_MODE']) die();

if ($KAMELEON_MODE)
{
	$k_version=0;
	$query="SELECT max(version) AS k_version FROM kameleon ";
	parse_str(ado_query2url($query));


	/*
	if (!$_COOKIE["SERVER_ID"])
	{
		$c=0;
		$query="SELECT count(*) AS c FROM passwd";
		parse_str(ado_query2url($query));
		if (!$c)
		{
			Header("Location: admin/");
			exit();
		}
	}
	*/

	$_auth=explode("@",$_POST['u']);
	
	if ( $adodb->checkSessionValue('login.alreadyLogedIn') == true )
	{
		$_auth[0] = $adodb->getFromSession('login.login');
		$_auth[1] = $adodb->getFromSession('login.server');
	}
	else if ( empty($_POST[u]) || empty($_POST[p]) )
	{
		unauthorize();
		unauthorize("Enter user and password");
	}

	
	if ($k_version<$KAMELEON_VERSION ) 
	{
		include ("include/kameleon_version.h"); 
	}

	if (isset($SetServer)) 
	{
		$SERVER=$SetServer;
		$SERVER_ID=0;
	}

	if (strlen($SERVER) || $SERVER_ID )
	{
		if ($SERVER_ID) $query="SELECT * FROM servers WHERE id=$SERVER_ID";
		else $query="SELECT * FROM servers WHERE nazwa='$SERVER'";
		$_server=ado_ObjectArray($adodb,$query);
	}

	if (!$SERVER_ID) $langmustbeset=1;

	if ($AUTH_BY_ACL_PLUGIN && file_exists(dirname(__FILE__).'/../plugins/acl/kameleon/auth.php') )
	{
		include(dirname(__FILE__).'/../plugins/acl/kameleon/auth.php');
		$kameleon->initAcl($auth_acl);
	}
	else
	{


		$USERNAME=$_auth[0];
		$PASSWORD=$_POST['p'];
		$SERVER = $_auth[1];

		$query="SELECT * FROM passwd WHERE username='$USERNAME'";
		parse_str(ado_query2url($query));


		if (strlen($nlicense_agreement_date))
			$LICENSE_AGREEMENT=1;
		else
			$LICENSE_AGREEMENT=0;

		if ( $adodb->checkSessionValue('login.alreadyLogedIn') !== true  
			|| $adodb->checkSessionValue('login.phash') != md5($password) )
		{
			if ( $password!=$PASSWORD
				&& $password!=crypt($PASSWORD,$password) 
				|| !strlen($password) ) 
			{
				unauthorize("User and password don't match");
			}
			else
			{
				$adodb->addToSession('login.alreadyLogedIn', true, true);
				$adodb->addToSession('login.login',$USERNAME,true);
				$adodb->addToSession('login.server',$SERVER,true);
				$adodb->addToSession('login.phash',md5($password),true);

				$dn=dirname($SCRIPT_NAME);
				if (strlen($dn)==1) $dn='';

				

				if ($_POST['p']==$PASSWORD && $_POST['r'])
				{
					SetCookie('wku',$USERNAME,time()+365*24*3600,$dn.'/login.php');
					SetCookie('wkp',$password,time()+365*24*3600,$dn.'/login.php');
				}
				if ($_POST['p']==$password && !$_POST['r'])
				{
					SetCookie('wku','',time()+365*24*3600,$dn.'/login.php');
					SetCookie('wkp','0',time()+365*24*3600,$dn.'/login.php');
				}
			}
		}
		
		if ($limit_time && $limit_time<$total_time) unauthorize("Time limit exceeded.");

		if (strlen($ulang)>0) $kameleon->setlang($ulang);

		if ($toggleeditormode)
		{
			$oldeditormode=$oldeditormode?0:1;
			$sql="UPDATE passwd SET oldeditormode=$oldeditormode WHERE username='$USERNAME'";
			$adodb->execute($sql);
		}

		if (!$ADMIN_MODE)
		{
		
			if (!is_array($_server) || !count($_server) ) 
			{
				$server=login_time_server($USERNAME);

				if (!$server)
				{
					$query="SELECT server FROM rights WHERE username='$USERNAME'
						 AND server IN (SELECT id FROM servers WHERE id=rights.server)
						 ORDER BY server LIMIT 1";
					parse_str(ado_query2url($query));
					
				}

				$server+=0;
				$query="SELECT * FROM servers WHERE id=$server";
				$_server=ado_ObjectArray($adodb,$query);
			}


			
			
			if (!is_array($_server) || !count($_server) ) unauthorize("Server not found");
			$SERVER=$_server[0];

		

			$SERVER_ID=$SERVER->id;
			
			$query="SELECT pages , menus , server AS s, ftp AS r_ftp, class AS r_class, basic AS r_basic, 
					proof AS r_proof, acl AS r_acl, nexpire, nexpire-".time()." AS days_to_expire,accesslevel AS server_al,
					template AS r_template
					FROM rights 
					WHERE username='$USERNAME' AND server=$SERVER_ID AND (nexpire>=".time()." OR nexpire IS NULL)";

			parse_str(ado_query2url($query));

			$SERVER->accesslevel=$server_al+0;


			if (!$s) unauthorize("Insufficient rights");

			if ( strlen($nexpire) && $days_to_expire < $DT->getDays(30) )
			{
				$expiration_warrning=label("License expires on")." ".FormatujDate($nexpire);
			}


			
			if (!strlen($SERVER_PROOF) || isset($SetServer) )
			{
				$sql="SELECT count(*) AS c FROM webpage WHERE server=$SERVER_ID AND noproof IS NOT NULL";
				parse_str(ado_query2url($sql));
				$adodb->SetCookie("SERVER_PROOF",$c);
				$SERVER_PROOF=$c;
			}


			$PAGE_RIGHTS=$pages;
			$MENU_RIGHTS=$menus;
			$FTP_RIGHTS=$r_ftp;
			$CLASS_RIGHTS=$r_class;
			$BASIC_RIGHTS=$r_basic;

			$PROOF_RIGHTS=$r_proof;
			$ACL_RIGHTS=$r_acl;
			$TEMPL_RIGHTS=$r_template;
			
			$kameleon->initAcl(null,$pages,$menus,$r_proof,$r_template,$r_class,$r_ftp,$admin);

		}

		//$ACL_RIGHTS=1;

		$ADMIN_RIGHTS=$admin;

		$KAMELEON['username']=$USERNAME;
		$KAMELEON['fullname']=$fullname;
		$KAMELEON['email']=$email;
		$KAMELEON['password']=$password;

		if (!strlen($skin)) $skin="kameleon";

		$kameleon->user=$KAMELEON;
		$kameleon->user[pages]=$pages;
		$kameleon->user[admin]=$admin;
		$kameleon->user[menus]=$menus;
		$kameleon->user[skin]=$skin;
		$kameleon->user[skinpath]=$CONST_SKINS_DIR."/".$skin;
		$kameleon->user[svn]=$svn_pass;

		$kameleon->current_server=$SERVER;
	}


	if ($langmustbeset) $setlang=$SERVER->lang; 

	
	if (isset($SetServer)) 
	{
		$version=$SERVER->ver;
		$setlang=$SERVER->lang;
	}


	$SERVER_ID=$SERVER->id;
	$SERVER_NAME=$SERVER->nazwa;

	if (strlen($SERVER->trans)) $SERVER->trans=unserialize($SERVER->trans);
	if (!strlen($SERVER->file_ext)) $SERVER->file_ext="$KAMELEON_EXT";




	


	$kameleon->plugins=$adodb->getFromSession('plugins');

	if (!is_array($kameleon->plugins) && file_exists('plugins') )
	{
		$kameleon->plugins=array();

		$handle=@opendir('plugins');

		$lp=0;
		$wgrany="";
		while (($file = @readdir($handle)) !== false) 
		{
			if ($file[0]==".") continue;
			if (!is_dir("plugins/".$file)) continue;
			$label=array();

			$lng=strlen($ulang)?$ulang:$lang;
			if (!file_exists('plugins/'.$file.'/lang/'.$lng.'.php')) $lng='en';
			@include('plugins/'.$file.'/lang/'.$lng.'.php');
			$kameleon->plugins[$file]['name']=$label['kameleon_plugin_name'];
			$kameleon->plugins[$file]['link']='plugins/'.$file;
			if (file_exists('plugins/'.$file.'/img/logo.gif')) $kameleon->plugins[$file]['logo']='plugins/'.$file.'/img/logo.gif';

		}
		@closedir($handle); 
		if (count($kameleon->plugins)>1) ksort($kameleon->plugins);
		$adodb->addToSession('plugins', $kameleon->plugins);
		
	}


	$adodb->SetCookie("SERVER_ID",$SERVER_ID);
	$PHP_AUTH_USER = $USERNAME;
	

	if (!headers_sent() && basename($_SERVER['PHP_SELF'])=='index.php') setCookie('wkserver',$SERVER_NAME,0,'/');

	if ($editmode)
	{
		$t=time();
		$query="SELECT max(tout) AS tout FROM login WHERE username='$USERNAME' AND ip='$REMOTE_ADDR'";
		parse_str(ado_query2url($query));

		if ($tout+600 > $t)
			$query="UPDATE login SET tout=$t,server=$SERVER_ID WHERE tout=$tout AND username='$USERNAME' AND ip='$REMOTE_ADDR'";
		else
		{
			$query="INSERT INTO login (tin,tout,username,server,ip) VALUES ($t,$t,'$USERNAME',$SERVER_ID,'$REMOTE_ADDR')";


			$sql="SELECT total_time FROM passwd WHERE username='$USERNAME'";
			parse_str(ado_query2url($sql));

			if ($total_time<=$CONST_MAX_TIME_REQUIRED_FOR_HELP && !isset($helpmode) && !$forget_help) $sethelpmode=1;

			$first_entrence=1;
		}
		$adodb->Execute($query);

		if (!isset($helpmode) && !$forget_help && strlen($forget_help) ) $sethelpmode=1;
	}

	
}
else
{
	$query="SELECT * FROM servers WHERE id=$SERVER_ID";
	$_server=ado_ObjectArray($adodb,$query);
	$SERVER=$_server[0];
	if (!strlen($SERVER->file_ext)) $SERVER->file_ext="$KAMELEON_EXT";
	$SERVER_NAME=$SERVER->nazwa;
	$kameleon->current_server=$SERVER;
}

if (isset($sethelpmode))
{

	$query="UPDATE passwd SET forget_help=";
	$query.=$sethelpmode?0:1;
	$query.=" WHERE username='$USERNAME'";
	$adodb->Execute($query);
	
	if ($AUTH_BY_ACL_PLUGIN)
	{
		$query="UPDATE acl_user SET au_forget_help=";
		$query.=$sethelpmode?0:1;
		$query.=" WHERE au_login='$USERNAME'";
		$auth_acl->adb->execute($query);
		
	}
}

if (!strlen($adodb->session[system_parameters][sql_replace]))
{
	$sql="SELECT replace('kameleon','a','e');";
	$adodb->session[system_parameters][sql_replace]=$adodb->execute($sql)?1:0;
}


