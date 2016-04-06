<?
$ret = "<div class=\"navi\">";
$ret .= "<a href=\"#top\"><img src=\"".$IMAGES."/navi/top.gif\" border=\"0\" hspace=\"0\" alt=\"\"></a>";
//$ret .= "<img src=\"".$IMAGES."/navi/sep.gif\" border=\"0\" hspace=\"0\" alt=\"\">";
//$ret .= "<a href=\"javascript:drukuj();\"><img src=\"".$IMAGES."/navi/print.gif\" border=\"0\" alt=\"\"></a>";
$ret .= "</div>";
echo $ret;


global $C_URL, $REQUEST_URI,$NAVI,$HTTP_HOST, $WEBPAGE;
if (!$KAMELEON_MODE) 
{
	$file_name = $WEBPAGE->file_name;
	echo "<? \$file_name='$file_name'; include(\"\$MAIN_INCLUDE_PATH/navipub.php\");?>";
}
?>
