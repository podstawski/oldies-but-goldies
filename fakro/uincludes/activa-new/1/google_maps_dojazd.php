<?
global $API_KEY_GOOGLE_MAPS;
?>

<style>
#map {
    width:  520px;
    height:  300px;
}
</style>

<script src='http://maps.google.com/maps?file=api&v=2&key=<? echo $API_KEY_GOOGLE_MAPS; ?>' type='text/javascript'></script>
<script type='text/javascript'>
/* wyswietlenie opisu */
function zawartoscOkna(tytul,opis) {
	return '<b>'+tytul+'</b>-<p>'+opis+'</p>';
	}
GMarker.prototype.pokazInfo=function() {
	this.openInfoWindowHtml(zawartoscOkna(this.tytul,this.opis));
	};
/********/
/* ikonky bazowe 2 */
function dodajMarker01(nazwa,punkt,ikona) {
	var marker = new GMarker(punkt,{icon: ikony[ikona]});
	marker.txt = nazwa;
	GEvent.addListener(marker,'click',function() {
		marker.pokazInfo();
		});
	mapa.addOverlay(marker);
	}

/* ikonky bazowe 3 */
var minimapa = document.createElement('img');
var mapa;

function dodajMarker02(nazwa,punkt,ikona) {
	var marker = new GMarker(punkt,{icon: ikony[ikona]});
	
	mapa.addOverlay(marker);
	GEvent.addListener(marker,'click',function() {
		//var div1 = document.createElement('div');
		//div1.className = 'dymek';
		var div2 = document.createElement('div');
		div2.className = 'dymek';
		//div1.appendChild(document.createTextNode(nazwa));
		div2.appendChild(minimapa);
		//minimapa.src = 'http://maps.google.com/staticmap?center='+marker.getPoint().lat()+','+marker.getPoint().lng()+'&size=200x180&zoom=14&key=<? echo $API_KEY_GOOGLE_MAPS; ?>';
		minimapa.src = './google_ico/mapka_muszyna.gif';
		//marker.openInfoWindowTabs([new GInfoWindowTab('marker',div1),new GInfoWindowTab('minimapa',div2)]);
		marker.openInfoWindowTabs([new GInfoWindowTab('minimapa',div2)]);
		});
	return marker;
	}
/********/
var ikony;

function mapaStart() {
	if(GBrowserIsCompatible()) {
		/* zestaw ikon na mapie */
		var ikona1 = new GIcon();
		ikona1.image = "<? echo $UIMAGES; ?>/google_ico/marker-landmark.png";
		ikona1.shadow = "<? echo $UIMAGES; ?>/google_ico/marker-shadow-landmark.png";
		ikona1.iconSize = new GSize(17,20); 
		ikona1.infoWindowAnchor = new GPoint(16,16);
		ikona1.iconAnchor = new GPoint(16,16);
		ikona1.shadowSize = new GSize(28, 20);

		var ikona2 = new GIcon();
		ikona2.image = "<? echo $UIMAGES; ?>/google_ico/marker-hotel-orange-large.png";
		ikona2.shadow = "<? echo $UIMAGES; ?>/google_ico/marker-shadow-hotel-large.png";
		ikona2.iconSize = new GSize(26,27);  
		ikona2.infoWindowAnchor = new GPoint(16,16);
		ikona2.iconAnchor = new GPoint(16,16);
		ikona2.shadowSize = new GSize(40, 27);

		ikony = [ikona1,ikona2];
		
		mapa = new GMap2(document.getElementById("mapka"));
		//mapa.addControl(new GMapTypeControl());		/* typ mapy */
		mapa.addControl(new GSmallMapControl());	/* nawigacja */
		mapa.setCenter(new GLatLng(49.36415368929453, 20.887928009033203), 13);	/* start mapy */
		
		/* punkty do wyswietlenia */
		var directions = new GLatLng(49.37544361924293, 20.878400802612305);
		var marker = new GMarker(directions,{title: "Activa", icon: ikona2});
		mapa.addOverlay(marker);
		marker.txt = "<div style='float: left; width: 84px;'></div><div style='float: left; text-align: left;'><div style='padding: 5px 0;'><strong>Hotel Aktiv sp. z o.o.</strong></div><div>ul. Złockie 78</div><div>33-370 Muszyna</div></div>";
			GEvent.addListener(marker,"click",function() {  
			marker.openInfoWindowHtml(marker.txt);  
			}); 
		mapa.openInfoWindowHtml(directions,marker.txt);
		/**/
		/* punkty do wyswietlenia */
		/*
		dodajMarker01('Hotel ACTIVA',new GLatLng(49.37544361924293, 20.878400802612305),1);
		dodajMarker02('Droga do HOTELU ACTIVA',new GLatLng(49.36388817706712, 20.88799238204956),0);
		dodajMarker02('Muszyna',new GLatLng(49.35311276084234, 20.89099645614624),0);
		*/
		/**/
		/* marker */
		/*
		var marker = new GMarker(mapa.getCenter(),{draggable: true});
		mapa.addOverlay(marker);
		GEvent.addListener(marker,'dragend',function()
		{
			zmienStatus(marker.getPoint());
		});
		GEvent.trigger(marker,'dragend');
		*/
		/**/
		}
	}
	
function zmienStatus(punkt) {
	document.getElementById('pasekStatusu').innerHTML='Marker znajduje się w punkcie: '+punkt;
	}
</script>

<table cellspacing="2" cellpadding="2" border="0">
<tr>
    <td>
	<div id="mapka" style="width: 523px; height: 350px; border: 1px solid black; background: gray;"></div>
	<div id="pasekStatusu"></div>
	</td>
</tr>
</table>

<SCRIPT>
	mapaStart();
</SCRIPT>


