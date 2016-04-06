<?

	if (!strlen($action) || !file_exists("$INCLUDE_PATH/.api/action/$action.h")) return;

	$action_progress=1;
	while ( strlen($action) && !strlen($error))
	{
		if (file_exists("$INCLUDE_PATH/.api/action/$action.h")) 
		{
			$oldaction=$action;
			include ("$INCLUDE_PATH/.api/action/$action.h");

			if ($oldaction==$action && !$auth_required && !strlen($error)) 
			{
				$sendmail_action=$action;
				include ("$INCLUDE_PATH/.api/action/SendmailOnAction.h");
				$action="";
			}
			if ($auth_required) 
			{
				include("$INCLUDE_PATH/.api/auth.h");
			}
			$auth_required=0;

		}
		else break;
	}


	$action_progress=0;
	$action="";

	if (strlen($error)) $JScript.="alert('$error');\n history.back();\n";
?>
