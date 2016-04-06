<script src="http://maps.google.com/maps?file=api&v=2.x&key=ABQIAAAANYZ4I-4hgzXzZMpHQHCMDBRFZ4a55UITye2Am3SStOIWMwE42BQj-fNB4hRgXPdrtR7qPWYI1iA-Jw" type="text/javascript"></script>
<script type="text/javascript">

  jQuery(function(){

    // trafic by ostol

    var map;
    var geocoder;
    var address;
    var directionsPanel;
    var directions;
    var address_to = 'ul. Złockie 78, 33-370 Muszyna, Polska';
    var target_location = '49.37544361924293, 20.878400802612305';       //lokalizacja hotelu activa

    map = new GMap2(document.getElementById("map_canvas"));

    map.setCenter(new GLatLng(52.047,19.27), 6);
    map.setUIToDefault();
    map.enableScrollWheelZoom();
    GEvent.addListener(map, "click", getAddress);
    geocoder = new GClientGeocoder();

    marker = new GMarker(new GLatLng(49.37544361924293, 20.878400802612305));
    map.addOverlay(marker);
    marker.openInfoWindowHtml(
    '<span style="font-size:14px;font-weight:bold">'
    +'Hotel Activa***'
    +'</span><br/>'
    +'<span>'+address_to+'</span><br/>Współrzędne geograficzne: N 49.376 E 20.878'
    );

    GEvent.addListener(marker, "click", showLocationDetails);

    //alert('Kliknij na mape w miejscu, gdzie chcesz rozpocząć podróż');


    function getAddress(overlay, latlng) {
      if (latlng != null) {
        address = latlng;
        geocoder.getLocations(latlng, showAddress);
      }
    }

    function showLocationDetails(){
      map.clearOverlays();
      map.setCenter(new GLatLng(49.37544361924293, 20.878400802612305), 13);
      marker2 = new GMarker(new GLatLng(49.37544361924293, 20.878400802612305));
      map.addOverlay(marker2);
      marker2.openInfoWindowHtml(
      '<span style="font-size:14px;font-weight:bold">'
      +'Hotel Activa***'
      +'</span><br/>'
      +'<span>'+address_to+'</span><br>Współrzędne geograficzne: N 49.376 E 20.878'
      );

    }

    function showAddress(response) {
      map.clearOverlays();

      jQuery('#route').html('');

      if (!response || response.Status.code != 200) {
        alert("Status Code:" + response.Status.code);
      } else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1],
        place.Point.coordinates[0]);

        directionsPanel = document.getElementById("route");
        directions = new GDirections(map, directionsPanel);
        directions.load("from: " + place.address + " to: "+target_location+" ");

       }
    }



    jQuery('#show_trafic_details').click(function(){
      showRoute();
      map.checkResize();
    });

    jQuery('#hide_trafic_details').click(function(){
      hideRoute();
      map.checkResize();
    });

    function showRoute(){
      jQuery('#map_canvas').animate({
        width:'530px'
      }, {
        duration: 500,
        complete: function() {
          jQuery('#route').fadeIn(500);
          jQuery('#show_trafic_details:visible').hide();
          jQuery('#hide_trafic_details:hidden').show();
          //map.checkResize();
        }
      });


    }


    function hideRoute(){
      jQuery('#route').fadeOut(500,function(){
        jQuery('#map_canvas').animate({
          width:'100%'},{
          duration:500,
          complete: function(){
            jQuery('#hide_trafic_details:visible').hide();
            jQuery('#show_trafic_details:hidden').show();
            jQuery('#map_canvas div').eq(0).css({'width':'100%'});   //fuckin IE FIX

          }
        })
      });

    }

  });

</script>


<div id="google_trafic_route">
  <div id="trafic_details_holder" style="clear:both;margin-bottom:5px;overflow:hidden;height:60px;">
    <p style="float:left;">
      Kliknij na mape w miejscu, gdzie planujesz rozpocząć podróż.<br/>
      <b>Współrzędne geograficzne:</b> N 49.376  E 20.878<br/>
       Współrzędne lądowiska Activa: N 49°22'34.7, E 020°52'52.1
    </p>


    <p style="float:right;padding:1px 3px;border:1px solid silver;">
      Szczegóły dojazdu: <span id="show_trafic_details" style="cursor:pointer">pokaż</span><span class="d_none" id="hide_trafic_details" style="cursor:pointer">ukryj</span>
    </p>
  </div>
  <div id="map_canvas" style="float: left;width: 100%;height: 500px;overflow:hidden;border:1px solid silver;"></div>
  <div class="d_none" id="route" style="float: right; overflow: auto;  width: 190px; height: 500px;border:1px solid silver;padding:0px !important;padding-left:5px !important;padding-right:5px !important"><h3>Plan podróży:</h3></div>
</div>
