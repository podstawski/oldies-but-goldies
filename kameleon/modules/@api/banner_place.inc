<?
	global $CMS_API_HOST;
	
	$API_VARS=array("sid","CMS_API_HOST","ORIGIN_HOST");


	if (!$_API_MODULE_MODE)
	{

		$ORIGIN_HOST=$_SERVER['HTTP_HOST'];

		$nocache=true;
		$DontShowHourglass=1;
		include("$INCLUDE_PATH/api.inc");
		$DontShowHourglass=0;
	}
	else
	{
		include("$INCLUDE_PATH/.api/banner_place.h");
	}
?>
