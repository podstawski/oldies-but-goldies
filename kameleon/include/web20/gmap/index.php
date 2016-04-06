<?
global $kameleon;
if (!$WEBTD->sid) return;

$width = (strlen($options["width"])>0 ? $options["width"]."px" : "100%");
$height = (strlen($options["height"])>0 ? $options["height"]."px" : "260px");
$type = (strlen($options["type"])>0 ? strtoupper($options["type"]) : "ROADMAP");

if (strlen($options["link"])==0)
{
$lat = strlen($options["lat"]) ? (float)$options["lat"] : 53.41935400090768;
$lng = strlen($options["lng"]) ? (float)$options["lng"] : 14.58160400390625;

echo "
<div id=\"km_w2_".$WEBTD->sid."\" style=\"width: ".$width."; height: ".$height."\"></div>
<script src=\"http://maps.google.com/maps/api/js?sensor=false\" type=\"text/javascript\"></script>
<script type='text/javascript'> 
var km_w2_".$WEBTD->sid."_marker;
var km_w2_".$WEBTD->sid."_mapa;
function km_w2_".$WEBTD->sid."_start()  
{  
    var km_w2_wspolrzedne_".$WEBTD->sid." = new google.maps.LatLng(".$lat.",".$lng.");
    var km_w2_opcjeMapy_".$WEBTD->sid." = {
      zoom: ".(strlen($options["zoom"]) ? (int)$options["zoom"] : 10).",
      center: km_w2_wspolrzedne_".$WEBTD->sid.",
      mapTypeId: google.maps.MapTypeId.".$type."
    };
    km_w2_".$WEBTD->sid."_mapa = new google.maps.Map(document.getElementById(\"km_w2_".$WEBTD->sid."\"), km_w2_opcjeMapy_".$WEBTD->sid.");";
    
    list($x,$y) = getimagesize($KAMELEON_UIMAGES."/".$WEBTD->img); 
    if (strlen($WEBTD->img) && file_exists($UIMAGES."/".$WEBTD->img)) echo "
    var km_w2_".$WEBTD->sid."_rozmiar = new google.maps.Size(".$x.",".$y.");
	var km_w2_".$WEBTD->sid."_punkt_startowy = new google.maps.Point(0,0);
	var km_w2_".$WEBTD->sid."_punkt_zaczepienia = new google.maps.Point(".(int)($x/2).",".(int)($y/2).");
    var km_w2_".$WEBTD->sid."_ikona = new google.maps.MarkerImage(\"".$UIMAGES."/".$WEBTD->img."\", km_w2_".$WEBTD->sid."_rozmiar, km_w2_".$WEBTD->sid."_punkt_startowy, km_w2_".$WEBTD->sid."_punkt_zaczepienia);
    ";
    
    echo "
    km_w2_".$WEBTD->sid."_marker = new google.maps.Marker({ position : new google.maps.LatLng(".$lat.",".$lng."), ".((strlen($WEBTD->img) && file_exists($UIMAGES."/".$WEBTD->img)) ? "icon: km_w2_".$WEBTD->sid."_ikona, ": "")." map : km_w2_".$WEBTD->sid."_mapa, visible : false });";
	
	if ($options["marker"] || $editmode) echo "km_w2_".$WEBTD->sid."_marker.setVisible(true);";
	
	if ($editmode)
	{
		echo "
		google.maps.event.addListener(km_w2_".$WEBTD->sid."_mapa, 'click', function(event) {
		    if (km_w2_".$WEBTD->sid."_marker.getVisible()) 
		    {
		    	km_w2_".$WEBTD->sid."_marker.setPosition(event.latLng);
		    	km_w2_".$WEBTD->sid."_savepos(event.latLng);
		    }
		});
		
		google.maps.event.addListener(km_w2_".$WEBTD->sid."_mapa, 'zoom_changed', function(event) {
			var lv = km_w2_".$WEBTD->sid."_mapa.getZoom();
	        jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'zoom', val : lv}, function(){	});

	    });
		
		google.maps.event.addListener(km_w2_".$WEBTD->sid."_mapa, 'maptypeid_changed', function(event) {
			var lv = km_w2_".$WEBTD->sid."_mapa.getMapTypeId();
	      	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'type', val : lv}, function(){	});
	    });
		
		";
	}
	
echo "	               
}
(function () {
	if (window.addEventListener)
 		window.addEventListener('load', km_w2_".$WEBTD->sid."_start, false);
 	else if (window.attachEvent)
		window.attachEvent('onload', km_w2_".$WEBTD->sid."_start);
}());
</script>
";
}
else
{
	echo "<iframe width='".str_replace("px","",$width)."' height='".$height."' frameborder='0' scrolling='no' marginheight='0' marginwidth='0' src='".$options["link"]."&amp;output=embed'></iframe>";
}