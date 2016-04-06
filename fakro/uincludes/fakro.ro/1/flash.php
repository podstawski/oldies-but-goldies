<?
include ($INCLUDE_PATH."/fun.php");

parse_str($WEBTD->costxt);

if (strlen($flash_width)) $flash_width = "width=\"".$flash_width."\"";
	else $flash_width = "width=\"775\"";
if (strlen($flash_height)) $flash_height = "height=\"".$flash_height."\"";
	else $flash_height = "height=\"333\"";

if (strlen($WEBTD->bgcolor)) {
	$flash_bgcolor = "<PARAM NAME=\"BGColor\" VALUE=\"#".$WEBTD->bgcolor."\">";
	$embed_bgcolor = "bgcolor=\"#".$WEBTD->bgcolor."\"";
}

$flash_src = $UIMAGES."/".$WEBTD->bgimg;

$flash_vars = "my_xml=".$INCLUDE_PATH."/xml.php";
$flash_variables = "<PARAM NAME=\"flashvars\" VALUE=\"".$flash_vars."\">";

$flash_plain = "<OBJECT codeBase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0 
					$flash_width $flash_height classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000>
				<PARAM NAME=\"Movie\" VALUE=\"".$flash_src."\">
				<PARAM NAME=\"Src\" VALUE=\"".$flash_src."\">
				<PARAM NAME=\"wmode\" VALUE=\"transparent\">
				$flash_variables
				$flash_clicktag
				$flash_bgcolor
				$flash_loop
				<EMBED src=\"$flash_src\" quality=high wmode=\"transparent\" $flash_width $flash_height $embed_bgcolor flashvars=\"".$flash_vars."\" 
					TYPE=\"application/x-shockwave-flash\" 
					PLUGINSPAGE=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\">
				</EMBED></OBJECT>";
echo $flash_plain;
?>