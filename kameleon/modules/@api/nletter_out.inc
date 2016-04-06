<?
	global $cemail, $cid, $SERVER_ID, $act, $cos;
	
	$API_VARS=null;
	if (isset($cemail)) $API_VARS[]="cemail";
	if (isset($cid)) $API_VARS[]="cid";
	if (isset($act)) $API_VARS[]="act";
	if (isset($cos)) $API_VARS[]="cos";

	if (!$_API_MODULE_MODE)
	{
		include("$INCLUDE_PATH/api.inc");
	}
	else
	{
		include("$INCLUDE_PATH/.api/nletter_out.h");
	}
?>