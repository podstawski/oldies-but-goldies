<?php

global $kameleon;
if (!$WEBTD->sid) return;

$width = (strlen($options["width"])>0 ? $options["width"] : "400");

echo "<div id='km_w2_".$WEBTD->sid."_fb' style='".$width."'>";
switch ($options["type"])
{
	case 'button':
		$height = 35;
		if ($options["layout"]=='button_count') $height = 21;
		if ($options["layout"]=='box_count') $height = 90;
		if ($options["faces"]) $height += 45;
		
		echo "<iframe src='http://www.facebook.com/plugins/like.php?app_id=272503249434979&amp;href=".str_replace(array(':','/'),array('%3A','%2F'),$options["url"])."&amp;send=false&amp;layout=".$options["layout"]."&amp;width=".$width."&amp;show_faces=".$options["faces"]."&amp;action=".$options["verb"]."&amp;colorscheme=".$options["color"]."&amp;height=".$height."' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:".$width."px; height:".(int)$height."px;' allowTransparency='true'></iframe>";
		break;
		
	case 'box':
		$height = 62;
		if ($options["faces"]) $height += 196;
		if ($options["stream"]) $height += 300;
		if ($options["header"]) $height += 32;
		echo "<iframe src='http://www.facebook.com/plugins/likebox.php?href=".str_replace(array(':','/'),array('%3A','%2F'),$options["url"])."&amp;width=".$width."&amp;colorscheme=".$options["color"]."&amp;show_faces=".($options["faces"] ? "true" : "false")."&amp;border_color&amp;stream=".($options["stream"] ? "true" : "false")."&amp;header=".($options["header"] ? "true" : "false")."&amp;height=".$height."' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:".$width."px; height:".$height."px;' allowTransparency='true'></iframe>";
		break;
	
	default:
		if ($editmode) echo $kameleon->label("Choose widget type");
}
echo "</div>";
?> 
