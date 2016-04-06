<?
	global $SEARCH;

	if (is_array($SEARCH)) $API_VARS=array("SEARCH");

	if (!$_API_MODULE_MODE)
	{
		include("$INCLUDE_PATH/api.inc");
	}
	else
	{
		include("$INCLUDE_PATH/.api/search.h");
	}

?>	
