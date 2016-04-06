<? 
	global $MODULES;

	include_once("$INCLUDE_PATH/fun.inc");

	if (file_exists("$INCLUDE_PATH/.pre.h")) include("$INCLUDE_PATH/.pre.h");

	if ($KAMELEON_MODE) $next_char = '&';
	else $next_char = '?';
?>
