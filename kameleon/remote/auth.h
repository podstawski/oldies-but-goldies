<?
	function kameleon_remote_unauthorize($realm)
	{
		Header("WWW-Authenticate: Basic realm=\"$realm\"");
		Header("HTTP/1.0 401 Unauthorized");

		exit();
	}
	

	function kameleon_remote_auth($file,$key,$name,$separator=":")
	{
		global $PHP_AUTH_USER,$PHP_AUTH_PW;
		$PHP_AUTH_USER = $_SERVER["PHP_AUTH_USER"];
        $PHP_AUTH_PW = $_SERVER["PHP_AUTH_PW"];

		global $action;

		if (!function_exists("dbmopen")) 
		{
			echo "<!-- No protection due to lack of dbmopen -->";
			return;
		}
		if (!file_exists($file)) 
		{
			echo "<!-- No protection due to lack of hash file -->";
			return;
		}

		

		$dbm=@dbmopen(ereg_replace("\.db","",$file),"r");
		if (!$dbm) $dbm=@dbmopen($file,"r");	
		if (!$dbm) return;

		if ( dbmexists ($dbm,$key) )
		{
			if (!strlen($PHP_AUTH_USER)) kameleon_remote_unauthorize($name);
			$value=@dbmfetch($dbm,"$key$separator$PHP_AUTH_USER");
			if (!strlen($value)) kameleon_remote_unauthorize($name);
			$value=base64_decode($value);
			$pos=strpos($value,$separator);
			if (!$pos) kameleon_remote_unauthorize($name);
			$rights=substr($value,0,$pos);
			$passwd=substr($value,$pos+1);
			
			if ($passwd!=$PHP_AUTH_PW) kameleon_remote_unauthorize($name);
			if (!strstr($rights,"R")) kameleon_remote_unauthorize($name);
			if (!strstr($rights,"W")) $action="";
		}
		dbmclose($dbm);
	}

?>