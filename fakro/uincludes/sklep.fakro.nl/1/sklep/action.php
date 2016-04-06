<?php

	global $action, $HTTP_REFERER;
	
	if (!strlen($action)) return;

	if (!$KAMELEON_MODE && strlen($action) && $action != "OsobaRejestruj")
	{
		if (!is_array($AUTH) && strlen($action)) include_once("$SKLEP_INCLUDE_PATH/autoryzacja/auth.h");
		if (!is_array($AUTH) && $page) return;
//		if (!haveRight("p_$page",$AUTH[id]+0,"",$projdb)) $error=sysmsg("No right to page","action");
	}

	if (strlen($action) && file_exists("$SKLEP_INCLUDE_PATH/action/$action.php"))
	{
		$sql="SELECT count(*) AS c FROM system_action WHERE sa_server=$SERVER_ID AND sa_page_id=$page 
				AND sa_action IN ('$action','$action.php','$action.h')";
		parse_str(ado_query2url($sql));
		if (!$c && $action!="KoszykDodaj" && $action!="KoszykOfertDoKoszyka" && $action != "OsobaRejestruj" && !$WEBTD->sid)
		{
			$error=sysmsg("No right to run","action").": $action";
		}
		if (!$c && $WEBTD->sid)
		{
			list($action) = explode(".",$action);
			$sql="INSERT INTO system_action (sa_server,sa_page_id,sa_action) VALUES ($SERVER_ID,$page,'$action')";
			ado_query2url($sql);
		}
	}

	if (strstr($AUTH[blokady],":$action.php:")) 
	{
		$error=sysmsg("Module blocked","action");
		$error.=": ".sysmsg("$action.php","action");
	}

	while (strlen($action) && !strlen($error))
	{

		
		if (file_exists("$SKLEP_INCLUDE_PATH/action/$action.php"))
		{
			$oldaction=$action;
			$query="";
			$actionmail=$action;
			$WM->action_state=1;
			$WM->action_name=$action;
			include("$SKLEP_INCLUDE_PATH/action/$action.php");

			if(!strlen($error)) include("$SKLEP_INCLUDE_PATH/actionmail.php");

			if (!strlen($error)) 
				$newaction=$WM->action($oldaction,$FORM,$LIST,$_REQUEST,$AUTH[id],$action_id);

			$WM->action_state=0;
			if ($oldaction==$action) $action=$newaction;
		}
		else
		{
			
			break;
		}
	}


	if (strlen($error)) 
	{
		$error=addslashes(stripslashes($error));
		$error=ereg_replace("[\r\n]+","\\n",$error);
	}

	if (strlen($_REQUEST[js_action]))
	{
		if (strlen($error)) echo "alert('$error');";
		else 
		{
			$js_action=$_REQUEST[js_action];
			eval("\$js_action=\"$js_action\";");
			echo "$js_action;";
		}
		exit();
	}


	if (strlen($error)) echo "<script>alert('$error'); history.back()</script>";
	if (strlen($error)) exit();


	

	$_REQUEST["action"]="";
	
?>
