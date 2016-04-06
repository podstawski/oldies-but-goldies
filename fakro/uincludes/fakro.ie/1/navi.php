<?
$ret = "<div class=\"navi\">
		<a href=\"javascript:history.back();\"><img src=\"".$IMAGES."/n_back_".$lang.".gif\" border=\"0\"></a>
		<a href=\"#top\"><img src=\"".$IMAGES."/n_top_".$lang.".gif\" border=\"0\" hspace=\"10\"></a>
		<a href=\"javascript:drukuj();\"><img src=\"".$IMAGES."/n_print_".$lang.".gif\" border=\"0\"></a></div>";
echo $ret;

global $C_URL, $REQUEST_URI,$NAVI,$HTTP_HOST, $WEBPAGE;
if (!$KAMELEON_MODE) 
{
	$file_name = $WEBPAGE->file_name;
	echo "<? \$file_name='$file_name'; include(\"\$INCLUDE_PATH/navipub.php\");?>";
}
?>
