<?
	$t=getdate(time());
 	$dzis=sprintf("%02d-%02d-%04d",$t["mday"],$t["mon"],$t["year"]);

	if (!strlen($lang)) 
	{
		switch ($HTTP_ACCEPT_LANGUAGE)
		{
			case "pl":
				$SetLang="pl";
				break;

			default:
				$SetLang="en";
				break;
		}
	}

	if (strlen($SetAdmMenu))
	{
		$adodb->SetCookie("admmenu","$SetAdmMenu");
		$admmenu=$SetAdmMenu;
	}

	if (strlen($SetLang))
	{
		$adodb->SetCookie("alang","$SetLang");
		$alang=$SetLang;
	}
	if (strlen($alang)) $lang=$alang;	

	if (strlen($SetGroup))
	{
		$adodb->SetCookie("grupa","$SetGroup");
		$grupa=$SetGroup;
		$login="";
		$adodb->SetCookie("login");
	}

	if (strlen($SetLogin))
	{
		$adodb->SetCookie("login","$SetLogin");
		$login=$SetLogin;
	}




