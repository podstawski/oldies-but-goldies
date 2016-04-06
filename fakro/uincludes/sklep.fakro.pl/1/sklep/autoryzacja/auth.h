<?
	include ("$SKLEP_INCLUDE_PATH/autoryzacja/config.inc.php");
	global $page,$db;
	global $AUTH;
	
	$CAUTH = '';
	
	if (strlen($AUTH['user']))
	{
		$CAUTH['user']=$AUTH['user'];
		$CAUTH['password']=$AUTH['password'];
	}
	else
		$CAUTH = $SKLEP_SESSION["CAUTH"];


	$obj_array = $SKLEP_SESSION["obj_array"];
	$SKLEP_SESSION["obj_array"] = $obj_array;

	$sql = "SELECT count(*) AS c FROM system_user WHERE su_parent IS NOT NULL";
	parse_str(ado_query2url($sql,true));
	if (!$c) return;
	$user_count=$c;
	
	if ($KAMELEON_MODE)
	{
		$AUTH_PAGES = $SKLEP_SESSION["AUTH_PAGES"];
		$SKLEP_SESSION["AUTH_PAGES"] = $AUTH_PAGES;
	}
	else
		$SKLEP_SESSION["AUTH_PAGES"] = $AUTH_PAGES;

	if (!is_array($AUTH_PAGES))
	{
		$sql = "SELECT DISTINCT(sao_klucz) FROM system_acl_obiekt";
		$res = $projdb->execute($sql);
		$AUTH_PAGES = array();
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			$AUTH_PAGES[] = trim($sao_klucz);
		}

		$SKLEP_SESSION["AUTH_PAGES"] = $AUTH_PAGES;

	}

	if (!strlen($CAUTH['user']))
		if (!in_array("p_$page",$AUTH_PAGES))
		{
			$AUTH[id] = -1;
			return;
		}

	if (function_exists("system_auth_user") && $CAUTH['id'] < 1) $is_good = system_auth_user($CAUTH,$db);	
//	print_r($CAUTH);
	if ($CAUTH[id] > 0) $is_good = $CAUTH[id];
	
	if (!$is_good)
	{
		global $_SERVER;
		$where_goto = urlencode($_SERVER[REQUEST_URI]);
		$SKLEP_SESSION["CAUTH"] = array();
		if (is_array($CAUTH)) 
			$err=1;
		else
			$err=0;
		
		if ($KAMELEON_MODE)
		{
			echo "
			<script>
				location.href('".kameleon_href("","goto=".$where_goto."&err=".$err,$_AUTH_LOGIN_PAGE)."');
			</script>
			";
		}	
		else if (!headers_sent())
		{
			if (!strlen($LOGIN_PHP)) $LOGIN_PHP='/login.php';
			$sign=strpos($LOGIN_PHP,'?')?'&':'?';
			Header("Location: $LOGIN_PHP${sign}goto=".$where_goto."&err=".$err);
		}
		return;
	}
	if (strlen($error))
		echo "<script>alert(\"$error\");history.go(-1);</script>";

	$CAUTH[id] = $is_good;

		
	$SKLEP_SESSION["CAUTH"] = $CAUTH;

	$AUTH['user']=$CAUTH['user'];
	$AUTH['password']=$CAUTH['password'];
	$AUTH['sesid']=$CAUTH['sesid'];
	$AUTH['id'] = $CAUTH['id'];
	$AUTH['c_id'] = $CAUTH['id'];

	if (function_exists("system_user_additional")) $AUTH=system_user_additional($AUTH,$db);	
	if ($user_count==1) $AUTH['p_admin']=1;

	$su_id = "";
	$su_pass = "";

	$t=time();

	global $REMOTE_ADDR;	

	$query="SELECT max(sl_tout) AS tout , sl_session AS sesid FROM system_log 
			WHERE sl_user_id= $AUTH[id] 
			AND sl_ip='$REMOTE_ADDR'
			AND sl_session = '".session_id()."'
			GROUP BY sl_session";

	parse_str(ado_query2url($query));

	if ($tout + $_AUTH_TIME_OUT > $t && $sesid == session_id())
	{
		$query="UPDATE system_log  
				SET sl_tout=$t,
				sl_server=$SERVER_ID,
				sl_lastpage=$page
				WHERE sl_tout=$tout 
				AND sl_user_id= $AUTH[id] 
				AND sl_ip='$REMOTE_ADDR'";
	}
	else
	{
		$query="INSERT INTO system_log (sl_tin,sl_tout,sl_user_id,sl_server,sl_ip,sl_lastpage,sl_session) 
				VALUES ($t,$t,$AUTH[id],$SERVER_ID,'$REMOTE_ADDR',$page,'".session_id()."')";
	}
	if (strlen($AUTH[id]))
		$projdb->execute($query);

	$WM->auth=&$AUTH;
?>