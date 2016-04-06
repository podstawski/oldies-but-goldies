<?
	global $AUTH;

	$API_CLASS="api2_auth_table";

	$API_VARS=array();
	if (is_array($AUTH)) $API_VARS=array("AUTH"); 
	if ($KAMELEON_MODE) 
	{
		global $ile,$start,$offset;

		$API_VARS[]="ile"; 
		$API_VARS[]="offset"; 
		$API_VARS[]="start"; 
	}


	if (!$_API_MODULE_MODE)
	{
		include("$INCLUDE_PATH/api.inc");
	}
	else
	{
		include("$INCLUDE_PATH/.api/login.h");
	}
	
?>