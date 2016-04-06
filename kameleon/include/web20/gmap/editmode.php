<?php 


if (strlen($options["link"])==0)
{
echo "

<script type='text/javascript'>
km_w2_".$WEBTD->sid."_szukaj = function ()
{
	var adres = document.getElementById('km_w2_".$WEBTD->sid."_adres').value;
	var geo = new google.maps.Geocoder();
	if(!geo) return;
	var tab = new Array();
	tab['address']=adres;
	geo.geocode(tab, function (wyniki,status){
	    if(status == google.maps.GeocoderStatus.OK)
	    {
	    	km_w2_".$WEBTD->sid."_marker.setPosition(wyniki[0].geometry.location);
	        km_w2_".$WEBTD->sid."_mapa.setCenter(wyniki[0].geometry.location);
	        km_w2_".$WEBTD->sid."_savepos(wyniki[0].geometry.location);
	        console.log('lat = '+wyniki[0].geometry.location.lat());
	        console.log('lng = '+wyniki[0].geometry.location.lng());
	    }
    }); 
}

km_w2_".$WEBTD->sid."_savepos = function (ll)
{
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'lat', val : ll.lat()}, function(){	});
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'lng', val : ll.lng()}, function(){	});
}

km_w2_".$WEBTD->sid."_show = function (el)
{
	if (el.checked)
	{
		//km_w2_".$WEBTD->sid."_marker.setVisible(true);
		vals=1;
	}
	else
	{
		//km_w2_".$WEBTD->sid."_marker.setVisible(false);
		vals=0;
	}
		
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'marker', val : vals}, function(){	});
}

km_w2_".$WEBTD->sid."_showmode = function()
{
	if (jQueryKam('#km_w2_".$WEBTD->sid."_editmode').css('display')=='block')
		jQueryKam('#km_w2_".$WEBTD->sid."_editmode').hide();
	else
		jQueryKam('#km_w2_".$WEBTD->sid."_editmode').show();
}

</script>
<a onclick='km_w2_".$WEBTD->sid."_showmode()' style='cursor: pointer'>".$kameleon->label("Edit")."</a>
<div id='km_w2_".$WEBTD->sid."_editmode' style='display: none'>
".$kameleon->label("Search address").": <input type='text' id='km_w2_".$WEBTD->sid."_adres' /><input type='button' value='".$kameleon->label("Szukaj")."' onclick='km_w2_".$WEBTD->sid."_szukaj()' /><br />
".$kameleon->label("Show marker on the map").": <input type='checkbox' value='1' ".($options["marker"] ? "checked='checked' " : "")." onclick='km_w2_".$WEBTD->sid."_show(this)' />
</div>
";
}