<?
global $ORYGINAL_WEBTD;

if(is_object($ORYGINAL_WEBTD)) $WEBTD=$ORYGINAL_WEBTD;
	$t=explode(':',substr($tree,1).$page);
	
	$back='';
	$html='';
	
	push($adodb);
	$adodb=$kameleon_adodb;
	
	for($i=count($t)-1;$i>=0;$i--) {
		$WP=kameleon_page($t[$i]);
		$WP=$WP[0];
		
		if(strlen($WP->background) && !strlen($back)) {
			$backsrc=$WP->background;
			$back="background-image: url($UIMAGES/$backsrc)";
			}
		if(strlen($WP->pagekey) && !strlen($html)) $html=$WP->pagekey;
		if(strlen($back) && strlen($html)) break;
		}
	
	$adodb=pop();
	
	$html=ereg_replace("uimages/[0-9]+/[0-9]+",$UIMAGES,$html);	
	$ext=strtolower(substr($backsrc,-3));
	
if($ext!='swf') {
	echo "<div id=\"$WEBTD->sid\" class=\"$WEBTD->class\" style=\"$back\"></div>";
	}else{
	echo "modul przeznaczony tylko dla plikow: jpg, gif";
	}
?>
