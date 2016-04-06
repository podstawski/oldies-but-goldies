<?
		if (is_array($SEARCH)) $API_VARS=array("SEARCH");

		$API_CLASS="api2_curr_head_tr";

		if (!$_API_MODULE_MODE)
		{
	                include("$INCLUDE_PATH/api.inc");
		}
		else
		{
			include("$INCLUDE_PATH/.api/waluty.h");
		}

?>
