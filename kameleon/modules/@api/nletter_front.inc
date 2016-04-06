<?
	global $SERVER_ID, $NLFRONT, $HTTP_HOST;

	$API_VARS=null;
	if (is_array($NLFRONT)) $API_VARS[]="NLFRONT";

	if (!$_API_MODULE_MODE)
	{
		include("$INCLUDE_PATH/api.inc");
	}
	else
	{
		include("$INCLUDE_PATH/.api/nletter_front.h");
	}
?>
