<?
	
	global $action,$action_progress,$after_action_reload;


	$action_progress=1;
	while ( strlen($action) && !strlen($error))
	{
		if (file_exists("$INCLUDE_PATH/action/$action.h")) 
		{
			$oldaction=$action;
			include ("$INCLUDE_PATH/action/$action.h");
			if (strlen($error)) break;

			$query="SELECT * FROM webtd
					WHERE server = $SERVER_ID 
					AND lang = '$lang' AND ver = $ver 
					AND mod_action='$oldaction'";

			$_webtd = ado_ObjectArray($adodb,$query);

			push($WEBTD);
			ob_start();
			for ($_webtd_i=0; strlen($action) && is_array($_webtd) && $_webtd_i < count($_webtd); $_webtd_i++)
			{
				$WEBTD = $_webtd[$_webtd_i];
				$action_progress=1;
				if (!file_exists("modules/".$WEBTD->html)) continue;
				include("modules/".$WEBTD->html);
			}
			ob_end_clean();
			$WEBTD=pop();

			if ($oldaction==$action) $action="";
		}
		else break;
	}
	$action_progress=0;
	$adodb->debug=false;


	if (strlen($after_action_reload))
	{


		eval ("\$location_reload=\"$after_action_reload\";");
		//eval ("\$reload=\"$after_action_reload\";"); echo $reload;
		$after_action_reload="";
		
	}


	if (strlen($error)) echo "<script>alert('$error'); history.back()</script>";
	if (strlen($location_reload)) echo "<script>location.reload('$location_reload')</script>";
	if (strlen($error) || strlen($location_reload) ) exit();
?>