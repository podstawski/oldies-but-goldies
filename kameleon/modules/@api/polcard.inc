<?
	global $POLCARD;

	$API_CLASS="api2_polcard_table";

	if (is_array($POLCARD)) 
	{
		global $message,$order_id,$err_code,$ret_code;
		$API_VARS=array("POLCARD","order_id","message","err_code","ret_code");
	}

	if (!$_API_MODULE_MODE)
	{
		include("$INCLUDE_PATH/api.inc");
	}
	else
	{
		include("$INCLUDE_PATH/.api/polcard.h");
		//$JScript.=" alert(document.cookie);\n";
	}
	
?>