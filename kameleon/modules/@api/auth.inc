<?
	global $AUTH, $CAUTH;

	if (is_array($AUTH)) $API_VARS[]="AUTH";
	if (is_array($CAUTH)) $API_VARS[]="CAUTH";


	if (!$_API_MODULE_MODE)
	{

		//if ($cos && !strlen($CAUTH[password])) return;
		$PreferServerToFetch=1;
		ob_start();
		include("$INCLUDE_PATH/api.inc");
		$result=ob_get_contents();
		ob_end_clean();	
		$PreferServerToFetch=0;

		$AUTH[c_id]=0;
		$result=explode(":",$result);
		parse_str($result[1]);

		if (!$AUTH[c_id])
		{
			if (!$cos)
			{
				$error=$result[0];
				if (!$editmode)
					echo "<script>alert('$error');</script>";
				else
					echo "<font color=Red>$error</font>";

				if (!$KAMELEON_MODE)
				{
					echo "<script>history.go(-1);</script>";
					exit();
				}
			}
		}
		else
		{

			if ($editmode) echo "<font color=Green>$result[0]</font>";
			if ($CAUTH[username]!=$AUTH[username] || $CAUTH[password]!=$AUTH[password])
			{		
				echo "<script> 
					document.cookie = \"CAUTH[password]=$AUTH[password]\"; 
					document.cookie = \"CAUTH[username]=$AUTH[username]\";
					document.cookie = \"CAUTH[token]=$AUTH[token]\"; 
					</script>\n";			
			}
		}
	}
	else
	{
		include("$INCLUDE_PATH/.api/auth.h");
	}
?>
