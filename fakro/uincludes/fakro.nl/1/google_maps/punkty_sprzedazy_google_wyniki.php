<?
include($INCLUDE_PATH.'/google_maps/config_maps.php');

global $DB_GOOGLE_MAPS;
global $SZUKAJ_PUNKTU, $list;

$LIST = $list;
$szukaj = $SZUKAJ_PUNKTU;

if(!is_array($szukaj) && !strlen($tylko_dla)) return;
if(strlen($szukaj['kod'])) $cond .= " AND lpad(kod,2) = '".addslashes(stripslashes($szukaj['kod']))."'";

$cond = " AND SUBSTRING(kod,1,2) = '".addslashes(stripslashes($szukaj['kod']))."'";

$lista = $DB_GOOGLE_MAPS->fetch_assoc($DB_GOOGLE_MAPS->query("SELECT * FROM punkty_sprzedazy_nl WHERE status='1' $cond ORDER BY nazwa,kod"));
$lista_count = $DB_GOOGLE_MAPS->getvalues($DB_GOOGLE_MAPS->query("SELECT count(id) AS total FROM punkty_sprzedazy_nl WHERE status='1' $cond"));
$LIST['ile'] = $lista_count['total'];

if(!$lista_count['total']) {
	echo "<br><br><div align=\"center\"><strong>Geen dealers gevonden die voldoen aan ingeveoerde criteria.</strong></div>";
	return;
	}
?>

<style>
.map { width: 510px; height: 350px; border: 1px solid black; background: gray; }
h4 { border-bottom: 1px solid green; font-size: 13px; }
p { font-size: 11px; }
#relatedList { border: 1px solid #ffffff; background-color: #ffffff; height:200px; overflow:auto; padding-bottom:7px; padding-left:10px; padding-top:7px; font-size: 8pt;
}
</style>

<script src='http://maps.google.com/maps?file=api&v=2&key=<? echo $API_KEY_GOOGLE_MAPS; ?>' type='text/javascript'></script>
<script type='text/javascript'>
var mapa;
var markery=[];

/* ikonky bazowe */
var baseIcon = new GIcon();
baseIcon.iconSize=new GSize(32,32);
baseIcon.shadowSize=new GSize(56,32);
baseIcon.iconAnchor=new GPoint(16,32);
baseIcon.infoWindowAnchor=new GPoint(16,0);
	  
var ikony=[];
ikony["aparat"] = new GIcon(baseIcon, "http://maps.google.com/mapfiles/kml/pal5/icon14.png", null, "http://maps.google.com/mapfiles/kml/pal5/icon14s.png");

/* nawigacja */
function myzoom(a) {
	mapa.setZoom(mapa.getZoom() + a);
	}

function zawartoscOkna(tytul,opis) {
	return '<h4>'+tytul+'</h4><p>'+opis+'</p><div align="right"><font size=2>zoom</font><a style=\'text-decoration:none; font-family: Tahoma; font-size:19px\' href=javascript:myzoom(+1)>+</a> <a style=\'text-decoration:none;font-size:19px\' href=javascript:myzoom(-1)>-</a></div>';
	}

GMarker.prototype.pokazInfo=function() {
	this.openInfoWindowHtml(zawartoscOkna(this.tytul,this.opis));
	};

function infoMarker(dlug_g,szer_g,kod,tytul,opis,icon) {
	var geo = new GClientGeocoder();
	var adres = kod;
	geo.getLatLng(adres,function(punkt) {  
		if(!punkt) {
			// brak kodu
			}else{
			mapa.panTo(punkt);
			mapa.openInfoWindowHtml(punkt,'<h4>'+tytul+'</h4><p>'+opis+'</p><div align="right"><font size=2>zoom</font><a style=\'text-decoration:none; font-family: Tahoma; font-size:19px\' href=javascript:myzoom(+1)>+</a> <a style=\'text-decoration:none;font-size:19px\' href=javascript:myzoom(-1)>-</a></div>');
			}
		});
	}
	
function dodajMarker(dlug_g,szer_g,kod,tytul,opis,icon) {
	var geo = new GClientGeocoder();
	var adres = kod;
	geo.getLatLng(adres,function(punkt) {  
		if(!punkt) {
			// brak kodu
			}else{
			var marker = new GMarker(punkt,ikony[icon]);
			
			marker.tytul = tytul;
			marker.opis = opis;
			
			GEvent.addListener(marker,'click',function() {
				marker.pokazInfo(punkt);
				});
			mapa.addOverlay(marker);
			}

		});
	}

function mapaStart() {
	if(GBrowserIsCompatible()) {
		mapa = new GMap2(document.getElementById("mapka"));
		mapa.addControl(new GSmallMapControl());	/* nawigacja */
		
		var geo = new GClientGeocoder();
		var adres = '<? echo $lista[0]['kod']." ".$lista[0]['miasto']." ".$lista[0]['adres']; ?>';
		geo.getLatLng(adres,function(punkt) {
			if(punkt) {
				//mapa.setCenter(new GLatLng(52.732965503769044, 6.097412109375), 8);	/* start mapy */
				mapa.setCenter(punkt, 12);	/* start mapy */
				}
			});
		
		/* punkty do wyswietlenia */
<?
foreach($lista as $key => $value) {
		echo "dodajMarker('','','".$lista[$key]['kod']." ".$lista[$key]['miasto']." ".$lista[$key]['adres']."','".$lista[$key]['nazwa']."','".$lista[$key]['adres']."<br>".$lista[$key]['kod']." ".$lista[$key]['miasto']."<br><br><strong>".$lista[$key]['tel']."<br>".$lista[$key]['fax']."</strong>','aparat');\n";
	}
?>
		/**/
		}
	}
</script>

<br>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
    <td><div id="mapka" class="map"></div></td>
</tr>
<tr>
    <td>
<br>
<div id="relatedList"><div style="width: 100%;">
<table class="tl" cellspacing="0" cellpadding="3" width="100%">
<tbody>
<?
$i = 0;
foreach($lista as $key => $value) {
	echo "<tr style=\"cursor: pointer;\" class=\"".(($i && ($i%2))?'even':'odd')."\" onClick=\"infoMarker('','','".$lista[$key]['kod']." ".$lista[$key]['miasto']." ".$lista[$key]['adres']."','".$lista[$key]['nazwa']."','".$lista[$key]['adres']."<br>".$lista[$key]['kod']." ".$lista[$key]['miasto']."<br><br><strong>".$lista[$key]['tel']."<br>".$lista[$key]['fax']."</strong>','aparat');return true;\">";
	echo '<td class="name" valign="top">'.(($lista[$key]['www'])?"<a href=\"http://".$lista[$key]['www']."\" target=\"_blank\"><img src=\"".$INCLUDE_PATH."/punkty_sprzedazy/i_www.gif\" align=\"right\" width=\"19\" height=\"28\" border=\"0\"></a>":"").' '.stripslashes($lista[$key]['nazwa']).'</td>';
	echo '<td valign="top">'.$lista[$key]['adres'].'<br>'.$lista[$key]['kod'].' '.$lista[$key]['miasto'].'</td>';
	echo '<td valign="top" style="font-weight:bold">'.$lista[$key]['tel'].'<br>'.$lista[$key]['fax'].'</td>';
	echo '</tr>';
	$i++;
	}
?>
</tbody>
</table>
</div></div>
	</td>
</tr>
</table>

<SCRIPT>
	mapaStart();
</SCRIPT>


