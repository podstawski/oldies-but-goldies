<?php 
$width = (strlen($options["width"])>0 ? (int)$options["width"] : "400");

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

km_w2_".$WEBTD->sid."_refresh = function()
{
	var typ = jQueryKam('#km_w2_".$WEBTD->sid."_type').val();
	var color = jQueryKam('#km_w2_".$WEBTD->sid."_color').val();
	var layout = jQueryKam('#km_w2_".$WEBTD->sid."_styl').val();
	var verb = jQueryKam('#km_w2_".$WEBTD->sid."_verb').val();
	var faces = jQueryKam('#km_w2_".$WEBTD->sid."_faces').attr('checked') ? 'true' : 'false';
	var header = jQueryKam('#km_w2_".$WEBTD->sid."_header').attr('checked') ? 'true' : 'false';
	var stream = jQueryKam('#km_w2_".$WEBTD->sid."_stream').attr('checked') ? 'true' : 'false';
	if (typ=='button')
	{
		var height = 35;
		if (layout=='button_count') height = 21;
		if (layout=='box_count') height = 90;
		if (faces == 'true') height += 45;
		ht = '<iframe src=\'http://www.facebook.com/plugins/like.php?app_id=272503249434979&amp;href=".str_replace(array(':','/'),array('%3A','%2F'),$options["url"])."&amp;send=false&amp;layout='+layout+'&amp;width=".$width."&amp;show_faces='+faces+'&amp;action='+verb+'&amp;colorscheme='+color+'&amp;height='+height+'\' scrolling=\'no\' frameborder=\'0\' style=\'border:none; overflow:hidden; width:".$width."px; height:80px;\' allowTransparency=\'true\'></iframe>';
	}
	else
	{
		var height = 62;
		if (faces=='true') height += 196;
		if (stream=='true') height += 300;
		if (header=='true') height += 32;
		ht = '<iframe src=\'http://www.facebook.com/plugins/likebox.php?href=".str_replace(array(':','/'),array('%3A','%2F'),$options["url"])."&amp;width=".$width."&amp;colorscheme='+color+'&amp;show_faces='+faces+'&amp;border_color&amp;stream='+stream+'&amp;header='+header+'&amp;height='+height+'\' scrolling=\'no\' frameborder=\'0\' style=\'border:none; overflow:hidden; width:".$width."px; height:'+height+'px;\' allowTransparency=\'true\'></iframe>';
	}
	jQueryKam('#km_w2_".$WEBTD->sid."_fb').html('').html(ht);
}

km_w2_".$WEBTD->sid."_changetype = function(typ)
{
	if (typ=='button')
		jQueryKam('#km_w2_".$WEBTD->sid."_opt_styl').show();
	else
		jQueryKam('#km_w2_".$WEBTD->sid."_opt_styl').hide();
		
	if (typ=='box')
	{
		jQueryKam('#km_w2_".$WEBTD->sid."_opt_stream').show();
		jQueryKam('#km_w2_".$WEBTD->sid."_opt_header').show();
		jQueryKam('#km_w2_".$WEBTD->sid."_opt_verb').hide();
	}
	else
	{
		jQueryKam('#km_w2_".$WEBTD->sid."_opt_stream').hide();
		jQueryKam('#km_w2_".$WEBTD->sid."_opt_header').hide();
		jQueryKam('#km_w2_".$WEBTD->sid."_opt_verb').show();	
	}
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'type', val : typ}, function(){	});
	km_w2_".$WEBTD->sid."_refresh();
}

km_w2_".$WEBTD->sid."_changestyl = function(typ)
{
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'styl', val : typ}, function(){	});
	km_w2_".$WEBTD->sid."_refresh();		
}

km_w2_".$WEBTD->sid."_changecolor = function(typ)
{
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'color', val : typ}, function(){	});
	km_w2_".$WEBTD->sid."_refresh();		
}

km_w2_".$WEBTD->sid."_changeverb = function(typ)
{
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'verb', val : typ}, function(){	});
	km_w2_".$WEBTD->sid."_refresh();		
}

km_w2_".$WEBTD->sid."_changefaces = function(typ)
{
	vals = 0;
	if (typ.checked==true) vals=1;
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'faces', val : vals}, function(){	});
	km_w2_".$WEBTD->sid."_refresh();		
}

km_w2_".$WEBTD->sid."_changestream = function(typ)
{
	vals = 0;
	if (typ.checked==true) vals=1;
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'stream', val : vals}, function(){	});
	km_w2_".$WEBTD->sid."_refresh();		
}

km_w2_".$WEBTD->sid."_changeheader = function(typ)
{
	vals = 0;
	if (typ.checked==true) vals=1;
	jQueryKam.getJSON('include/web20/?action=changeOption', { sid : ".$WEBTD->sid.", key : 'header', val : vals}, function(){	});
	km_w2_".$WEBTD->sid."_refresh();		
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
".$kameleon->label("Widget type").": <select id='km_w2_".$WEBTD->sid."_type' onchange='km_w2_".$WEBTD->sid."_changetype(this.value)'>
	<option value='button' ".($options["type"]=='button' ? " selected " : "")."'>".$kameleon->label("Button")."</option>
	<option value='box' ".($options["type"]=='box' ? " selected " : "")."'>".$kameleon->label("Box")."</option>
</select><br />
".$kameleon->label("Color scheme").": <select id='km_w2_".$WEBTD->sid."_color' onchange='km_w2_".$WEBTD->sid."_changecolor(this.value)'>
	<option value='light' ".($options["color"]=='light' ? " selected " : "")."'>".$kameleon->label("Light")."</option>
	<option value='dark' ".($options["color"]=='dark' ? " selected " : "")."'>".$kameleon->label("Dark")."</option>
</select>
<div id='km_w2_".$WEBTD->sid."_opt_styl' ".($options["type"]!="button" ? "style='display: none'" : "").">
".$kameleon->label("Styl").": <select id='km_w2_".$WEBTD->sid."_styl' onchange='km_w2_".$WEBTD->sid."_changestyl(this.value)'>
	<option value='standard' ".($options["styl"]=='standard' ? " selected " : "")."'>".$kameleon->label("Standard")."</option>
	<option value='button_count' ".($options["styl"]=='button_count' ? " selected " : "")."'>".$kameleon->label("Button count")."</option>
	<option value='box_count' ".($options["styl"]=='box_count' ? " selected " : "")."'>".$kameleon->label("Box count")."</option> 
</select>
</div>
<div id='km_w2_".$WEBTD->sid."_opt_verb' ".($options["type"]!="button" ? "style='display: none'" : "").">
".$kameleon->label("Verb to display").": <select id='km_w2_".$WEBTD->sid."_verb' onchange='km_w2_".$WEBTD->sid."_changeverb(this.value)'>
	<option value='like' ".($options["styl"]=='like' ? " selected " : "")."'>".$kameleon->label("Like")."</option>
	<option value='recommend' ".($options["styl"]=='recommend' ? " selected " : "")."'>".$kameleon->label("Recommend")."</option> 
</select>
</div>
<div id='km_w2_".$WEBTD->sid."_opt_faces'>
	".$kameleon->label("Show faces").": <input type='checkbox' id='km_w2_".$WEBTD->sid."_faces' value='1' ".($options["faces"] ? " checked='checked' " : "")." onchange='km_w2_".$WEBTD->sid."_changefaces(this)' />
</div>
<div id='km_w2_".$WEBTD->sid."_opt_stream' ".($options["type"]!="box" ? "style='display: none'" : "").">
	".$kameleon->label("Stream").": <input type='checkbox' id='km_w2_".$WEBTD->sid."_stream' value='1' ".($options["faces"] ? " checked='checked' " : "")." onchange='km_w2_".$WEBTD->sid."_changestream(this)' />
</div>
<div id='km_w2_".$WEBTD->sid."_opt_header' ".($options["type"]!="box" ? "style='display: none'" : "").">
	".$kameleon->label("Header").": <input type='checkbox' id='km_w2_".$WEBTD->sid."_header' value='1' ".($options["faces"] ? " checked='checked' " : "")." onchange='km_w2_".$WEBTD->sid."_changeheader(this)' />
</div>
";

echo "</div>";
}