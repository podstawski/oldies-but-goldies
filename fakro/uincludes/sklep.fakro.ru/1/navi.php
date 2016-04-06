<?
$ret = "<div class=\"navi\">";
$ret.= "<a href=\"javascript:history.back();\"><img src=\"".$IMAGES."/navi/".$lang."/back.gif\" border=\"0\"></a>";
$ret.= "<a href=\"#top\"><img src=\"".$IMAGES."/navi/".$lang."/up.gif\" border=\"0\"></a>";
$ret.= "<a href=\"javascript:drukuj();\"><img src=\"".$IMAGES."/navi/".$lang."/print.gif\" border=\"0\"></a>";
/*
$ret.= "<a href=\"javascript:drukuj();\"><img src=\"".$IMAGES."/navi/".$lang."/polec.gif\" border=\"0\"></a>";
$ret.= "<a href=\"javascript:drukuj();\"><img src=\"".$IMAGES."/navi/".$lang."/komentarz.gif\" border=\"0\"></a>";
*/
$ret.= "<a href=\"javascript:favorite();\" title=\"Dodaj do ulubionych\"><img src=\"".$IMAGES."/navi/".$lang."/favorite.gif\" border=\"0\"></a>";
$ret.= "</div>";
echo $ret;

global $C_URL, $REQUEST_URI,$NAVI,$HTTP_HOST, $WEBPAGE;

if (!$KAMELEON_MODE) 
{
	$file_name = $WEBPAGE->file_name;
	echo "<? \$file_name='$file_name'; include(\"\$INCLUDE_PATH/navipub.php\");?>";
}
?>
