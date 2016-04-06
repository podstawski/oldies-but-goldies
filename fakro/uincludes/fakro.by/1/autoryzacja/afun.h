<?

if (!function_exists("system_print_dirlist"))
{
	function system_print_dirlist($dirlist,$namelist,$lista_praw,$depth=0,$last="main")
	{
		static $first = 1;
		if (!is_array($dirlist) || !count($dirlist)) return;
//		$last = "main";
		for ($i=0; $i < count($dirlist); $i++)
		{			
			if (!is_array($dirlist[$i]))
			{
				$chck = "";
				if (in_array($namelist[$i],$lista_praw)) $chck = "checked";
				switch ($last)
				{
					case "main":
						for ($k=0; $k<$depth;$k++)
							$res.="&nbsp;&nbsp;";
						$res.="<INPUT TYPE=\"checkbox\" $chck NAME=\"KATALOGI[".$namelist[$i]."]\" value=\"1\">".$dirlist[$i]."<br>";
						break;
					case "sub":
						$depth-=2;
						if ($depth < 0) $depth = 0;
						for ($k=0; $k<$depth;$k++)
							$res.="&nbsp;&nbsp;";
						$res.="<INPUT TYPE=\"checkbox\" $chck NAME=\"KATALOGI[".$namelist[$i]."]\" value=\"1\">".$dirlist[$i]."<br>";
						$last = "main";
						break;
				}
			}
			else
			{
				switch ($last)
				{
					case "main":
						$depth+=2;
						for ($k=0; $k<$depth;$k++)
							$res.="&nbsp;&nbsp;";
						$res.= system_print_dirlist($dirlist[$i],$namelist[$i],$lista_praw,$depth,"main")."<br>";
						$last = "sub";
						break;
					case "sub":
						if ($depth < 0) $depth = 0;
						for ($k=0; $k<$depth;$k++)
							$res.="&nbsp;&nbsp;";
						$res.= system_print_dirlist($dirlist[$i],$namelist[$i],$lista_praw,$depth,"main")."<br>";
						break;
				}
			}
			if ($first)
			{
				$depth+=2;
				$first=0;
			}
		}
		return $res;
	}
}

if (!function_exists("system_list_dir"))
{
	function system_list_dir($dir,$fullname=1,$first=1)
	{
		global $UFILES;
		if (is_dir($dir))
		{
			if ($first)
			{
				if ($fullname)
					$res[] = "a_.";
				else
					$res[] = "zaГБczniki";
				$first = 0;
			}
			
			if ($dh = opendir($dir)) 
				while (($file = readdir($dh)) !== false) 
					if (is_dir($dir."/".$file) && $file != "." && $file != ".." && strlen($file))
					{
						if ($fullname)
							$res[] = "a".ereg_replace("/","_",ereg_replace("$UFILES","",$dir))."_".$file;
						else
							$res[] = $file;
						$add = system_list_dir($dir."/".$file,$fullname,0);
						if (is_array($add) && count($add))
							for ($i=0; $i < count($add); $i++)
								if (strlen(trim($add[$i]))) 
								{									
									$res[] = $add;
									break;
								}
					}
		}
		closedir($dh);
		return $res;
   }
}


if (!function_exists("system_auth_user"))
{
	function system_auth_user($obj, $db=null)
	{

		$CAUTH = $obj;

		$sql = "SELECT su_pass, su_id FROM system_user WHERE su_login = '$CAUTH[user]' AND su_aktywny = 1";
		$su_pass = "";
		parse_str(query2url($sql));

		if (!strlen($su_pass) || ($su_pass != $CAUTH[password] && $su_pass != crypt($CAUTH[password],$su_pass)))
			return 0;
		else
			return $su_id;		
	}
}
 
if (!function_exists("system_user_additional"))
{
	function system_user_additional($obj)
	{
		if ($obj[projekt]) return $obj; // ustawiam tylko raz na sesjъ

		global $SERVER_ID;
		
		$query="SELECT * FROM system_user WHERE su_id=$obj[id]";
		parse_str(query2url($query));

		$obj[user]=$su_login;
		$obj[imiona]=$su_imiona;
		$obj[nazwisko]=$su_nazwisko;
		$obj[projekt]=$su_server;
		$obj[parent]=$su_parent;
		$obj[email]=$su_email;
		$query="SELECT su_nazwisko FROM system_user WHERE su_id=$obj[parent]";
		parse_str(query2url($query));
		$obj[nazwa]=$su_nazwisko;				

		return $obj;
	}
}

?>