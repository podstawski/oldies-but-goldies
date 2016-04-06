<?
	global $SERVER_PORT, $MODULES, $WEATHER;

	if (is_array($WEATHER)) $API_VARS[]= $WEATHER;

	if (!$_API_MODULE_MODE)
	{
		include("$INCLUDE_PATH/api.inc");
		return;
	}


	$string = explode(";",$costxt);

	$WEATHER[code]=$string[0];

	if (!strlen($WEATHER[code])) return;

	$WEATHER[type] = $string[1];
	if (!strlen($WEATHER[type])) $WEATHER[type] = "bigwx_metric_cond/language/polish";
	
	echo "
	<a href=\"http://www.wunderground.com/global/stations/$WEATHER[code].html\">
		<img border=\"0\" alt=\"Prognoza pogody\" src=\"http://banners.wunderground.com/banner/$WEATHER[type]/global/stations/$WEATHER[code].gif\">
	</a><br><br>";

//	if ($SERVER_PORT!=80) 
//	_display_view($MODULES->api->files->pogoda);

?>