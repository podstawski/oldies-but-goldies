<?
function newsletter_tokens($t)
{
	global $WEBPAGE, $KAMELEON_MODE, $UIMAGES, $WEBTD, $WEBLINK, $IMAGES, $KAMELEON_UIMAGES;
	global $page, $ver, $lang, $editmode;
	global $LINK_TYPY,$tokens;
	global $SZABLON_PATH;
	global $INCLUDE_PATH;
	
	$pages_list = explode(":",$WEBPAGE->tree);
	$pages_list[] = $page;
	
	switch ($t)	{
		case "NEWSLETTER_MANAGEMENT":
			kameleon_include("INCLUDE_NEWSLETTER_PATH/management.php","");
			return " ";
			break;
		

		default:
		return "";

	}
}
?>
