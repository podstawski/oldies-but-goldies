<?
	global $action,$ACTION_VAR;

	if (!strlen($action)) return;



	if (!$_API_MODULE_MODE)
	{

		global $CMS_API_HOST;
		$action_exists="$CMS_API_HOST/modules/@api/action_exists.php?action=$action";
		$action_exists=file($action_exists);
		$action_exists[0]=0+trim($action_exists[0]);

		if (!$action_exists[0]) return;

		if (strlen($ACTION_VAR))
		   foreach (explode(",",$ACTION_VAR) AS $v) 
				eval("global \$$v;");


		$API_VARS=array_merge(array("action"),explode(",",$ACTION_VAR));

		api2_push($html,$costxt,$sid);
		$html="action.inc";
		$costxt="";
		$sid=0;
		include("$INCLUDE_PATH/api.inc");
		@api2_pop(&$html,&$costxt,&$sid);
		$action="";
		$ACTION_VAR="";
		
	}
	else
	{
		include ("$INCLUDE_PATH/.api/action.h");
	}

?>
