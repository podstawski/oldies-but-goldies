<?
//return;
global $WEBPAGE;
//print_r($WEBPAGE);

$pages_list = explode(":",$WEBPAGE->tree);
$pages_list[] = $WEBPAGE->id;

$_width = "width=749";
$_height = "height=199";
$_ret = "";

if (is_array($pages_list)) {
	for ($i=count($pages_list); $i >= 0 ; $i--) {	
		if (strlen($pages_list[$i])){							
			$adodb=$kameleon_adodb;
			$PREV_WEBPAGE=kameleon_page($pages_list[$i]);
			$adodb=$projdb;
			
			$_background = $PREV_WEBPAGE[0]->background;
			
			$_head_arr = explode(".",$_background);
			$_head_img_rest = strtolower($_head_arr[count($_head_arr)-1]);
			$_src = $UIMAGES."/".$_background;
			
			if (($_head_img_rest=="jpg") || ($_head_img_rest=="gif")) {
				//echo "IMAGE";
				$_ret = "<img src=\"$_src\" border=0 $_width $_height style=\"border-bottom: 3px solid #1B1D2A;border-top: 2px solid #090B18;\">";
			}	
			elseif ($_head_img_rest=="swf") {
				//echo "FLASH";
				$_ret = "
					<OBJECT codeBase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0 
						$_width $_height classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000>
					<PARAM NAME=\"Movie\" VALUE=\"$_src\">
					<PARAM NAME=\"Src\" VALUE=\"$_src\">
					<EMBED src=\"$_src\" quality=high $_width $_height 
						TYPE=\"application/x-shockwave-flash\" 
						PLUGINSPAGE=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\">
					</EMBED>
					</OBJECT>";				
			}	
			
			if (strlen($_ret)) break;
		}	
	}
}

echo $_ret;
?>
