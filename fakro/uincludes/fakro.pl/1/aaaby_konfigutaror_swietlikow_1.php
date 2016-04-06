<style>
  #head{display:none}
  #swietliki .error{color:red;padding:2px;display:none;}
  #swietliki h3{padding-bottom:5px !important;}
  #swietliki .row_box{padding-bottom:20px;}

  #zone_table{border-collapse: collapse;}
  #zone_table tbody tr td{width:125px;border:1px solid #ccc;text-align: center;cursor:pointer;vertical-align: middle !important;}
  #zone_table tbody tr td.active{background-color:#98CD03 }

  #site_table{border-collapse: collapse;}
  #site_table tbody td {border:1px solid #ccc;}
  #site_table tbody td * {border:1px solid white;cursor:pointer;width:68px;height:99px }
  #site_table tbody td *.active{border-color:#98CD03 }

  #diameter_table {border-collapse: collapse;}
  #diameter_table td{border:1px solid #ccc }
  #diameter_table td *{border:1px solid transparent;cursor:pointer}
  #diameter_table td .active{border:1px solid #98CD03}

  #roof_angle_table {border-collapse: collapse;}
  #roof_angle_table td{border:1px solid #ccc }
  #roof_angle_table td *{border:1px solid transparent;cursor:pointer}
  #roof_angle_table td .active{border:1px solid #98CD03 !important}

  .result{width:100%;border-collapse: collapse;}
  .result th{border:1px solid #cccccc;background-color:#f4f4f4}
  .result td{border:1px solid #cccccc}
  .result td.stream{width:80px;text-align:center;}
  .result td,#result th{padding:2px;}
  .result td.lux{text-align:center}
  .result .lux, #result  .lux_label {background-color:#EAE3DB}
  .result .table_subtitle{text-align:center;padding:10px;font-weight:bold}
  .red_info{color:red; display:none}

  #table_ile{border-collapse: collapse;width:100%}
  #table_ile tbody tr th {border:1px solid #ccc;background-color:#F4F4F4}
  #table_ile tbody tr th.header {padding:10px;background-color:white}
  #table_ile tbody tr td {border:1px solid #ccc;text-align:center;vertical-align: middle;}

  .bw_ico{display:none !important}

  #s129760  #meteo_1,
  #s129760  #meteo_2,
  #s129760  #meteo_3{display:none}

  .pieces_table{border-collapse: collapse;width:100%}
  .pieces_table th,
  .pieces_table td{border: 1px solid #ccc;text-align:center;padding:2px;}
  .pieces_table .w_80{width:80px;}

  .error_info{border:1px solid red;padding:5px;text-align:center;color:red;}
</style>
<?php

  if($_POST){
    $cons     = constans();
    $sunlight = sunlight(); // dane o nasłonecznieniu
    $Mk       = Mk();       // costam kolanka
    $MR       = MR();       // costam rury
    $WNS      = WNS();      // wymagane natężenia światła

    // dane z formularza
    $zone         = substr($_POST['zone'],0,1);
    $site         = substr($_POST['site'],0,2);
    $roof_angle   = intval($_POST['roof_angle']);
    $diameter     = intval($_POST['diameter']);

    $pipe_length  = floatval($_POST['pipe_length']);
    $room_length  = floatval($_POST['room_length']);
    $room_width   = floatval($_POST['room_width']);
    $room_height  = floatval($_POST['room_height']);

    //powierzchnia dachu [m]/ ile świetlików sie zmiesci
    $room_area_factor    = $room_length * $room_width   / $cons['collar_area'][$diameter] * 0.6;

    //_d($room_area_factor    );

    // Eh  - nasłonecznienie
    $Eh_1 = $sunlight[$zone][1][$site];
    $Eh_2 = $sunlight[$zone][2][$site];
    $Eh_3 = $sunlight[$zone][3][$site];

    // Fi_e   - PRZEPŁYW ŚWIATŁA
    $Fi_e_1 = $Eh_1 * $cons['area'][$diameter];
    $Fi_e_2 = $Eh_2 * $cons['area'][$diameter];
    $Fi_e_3 = $Eh_3 * $cons['area'][$diameter];

    // ?M (Mk + MR)
    $EM = $Mk[$diameter][$roof_angle] + $MR[$diameter][''.get_L($pipe_length)];

    // liczenie TTE
    $tmp_1 = exp( $EM * $cons['tg_alpha'] * $cons['ln_R'] );
    $tmp_2 = pow(1-(  $EM * $cons['tg_alpha'] * $cons['ln_R']), 0.5)  ;

    $TTE = $tmp_1 / $tmp_2;

    // efektywność całego świetlika
    $EG   = $TTE * $cons['maintenance_faktor'][$diameter] * $cons['dome_permeability'] * $cons['diffuser_permeability'] * $cons['pipe_reflectivity'];
    $EGe  = $TTE * $cons['maintenance_faktor'][$diameter] * $cons['dome_permeability'] * $cons['diffuser_permeability'] * $cons['pipe_reflectivity_elastic'];

    // przepływ światła przez rozpraszacz
    $FD_1 = $Fi_e_1 * $EG;
    $FD_2 = $Fi_e_2 * $EG;
    $FD_3 = $Fi_e_3 * $EG;

    if($diameter > 250){ // dla elastycznych
      $FD_1e = $Fi_e_1 * $EGe;
      $FD_2e = $Fi_e_2 * $EGe;
      $FD_3e = $Fi_e_3 * $EGe;
    }

    //room index
    $K = ($room_length * $room_width) / ( ($room_length + $room_width) * ($room_height - 0.8) );

    // jak sie to nazywa???
    $UF = UF($K);

    // przepływ w pomieszczeniu
    $Fc_1 = $FD_1 / (1 - $UF);
    $Fc_2 = $FD_2 / (1 - $UF);
    $Fc_3 = $FD_3 / (1 - $UF);

    if($diameter > 250){ // dla elastycznych
      $Fc_1e = $FD_1e / (1 - $UF);
      $Fc_2e = $FD_2e / (1 - $UF);
      $Fc_3e = $FD_3e / (1 - $UF);
    }

    // natężenie światła na poziomie biurka
    $I_1 = $Fc_1 / ( $room_width * $room_length);
    $I_2 = $Fc_2 / ( $room_width * $room_length)  ;
    $I_3 = $Fc_3 / ( $room_width * $room_length) ;

    if($diameter > 250){ // dla elastycznych
      $I_1e = $Fc_1e / ( $room_width * $room_length);
      $I_2e = $Fc_2e / ( $room_width * $room_length)  ;
      $I_3e = $Fc_3e / ( $room_width * $room_length) ;
    }
  }
?>

<script type="text/javascript">
  var config = {
    'is_post':'<?php echo intval($_POST) ?>'
  }
  $(function(){
    (function(){
      if(config.is_post == 1){
        setZone('<?php echo $zone ?>');
        setSite('<?php echo $site ?>');
        setRoofAngle('<?php echo $roof_angle ?>');
        setDiameter('<?php echo $diameter ?>');
        setPipeLength('<?php echo $pipe_length ?>');
        setRoomLength('<?php echo $room_length ?>');
        setRoomWidth('<?php echo $room_width ?>');
        setRoomHeight('<?php echo $room_height ?>');
      }

      $('.meteo_ico_1').append( $('#meteo_1').clone() );
      $('.meteo_ico_2').append( $('#meteo_2').clone() );
      $('.meteo_ico_3').append( $('#meteo_3').clone() );

      var icons = {
        0:$('img[alt=schody]').attr('src'),
        1:$('img[alt=pokoj_odpoczynku]').attr('src'),
        2:$('img[alt=magazyny]').attr('src'),
        3:$('img[alt=lazienka]').attr('src'),
        4:$('img[alt=pokoj_cwiczen]').attr('src'),
        5:$('img[alt=rampy_zaladunkowe]').attr('src'),
        6:$('img[alt=recepcja]').attr('src'),
        7:$('img[alt=biura]').attr('src'),
        8:$('img[alt=kuchnia]').attr('src'),
        9:$('img[alt=pokoj_konferencyjny]').attr('src'),
        10:$('img[alt=praca_precyzyjna]').attr('src')
      }

      for(i in icons){
        $('.name_'+i)
        .css({
          'background-image':'url("' + icons[i] + '")',
          'background-position':'left center',
          'background-repeat':'no-repeat',
          'text-indent':'40px',
          'height':'36px'
        });
      }

      var site_table = $('#site_table')
      $('#site_table_holder').prepend(site_table)

      $('#zone_table td').click(function(){
        $('#zone_table td').removeClass('active');
        $(this).addClass('active');
        $('#zone').val( $(this).attr('id') );
        $('#zone_error').hide();
      });

      $('#site_table td img').click(function(){
        $('#site_table td img').removeClass('active');
        $(this).addClass('active');
        $('#site').val( $(this).attr('alt') );
        $('#site_error').hide();
      });

      $('#roof_angle_table td > *').click(function(){
        $('#roof_angle_table td > *').removeClass('active');
        $(this).addClass('active');
        $('#roof_angle').val( $(this).attr('alt') );
        $('#roof_angle_error').hide();
      });

      $('#diameter_table td > *').click(function(){
        $('#diameter_table td > *').removeClass('active');
        $(this).addClass('active');
        $('#diameter').val( $(this).attr('alt') );
        $('#diameter_error').hide();
      });

      $('#pipe_length').keyup(function(){
        $('#pipe_length_error').hide();
      })

      $('#room_length').keyup(function(){
        $('#room_length_error').hide();
      })

      $('#room_width').keyup(function(){
        $('#room_width_error').hide();
      })

      $('#room_height').keyup(function(){
        $('#room_height_error').hide();
      })

      $('#ognia').click(function(){
        $('.error').hide();

        validateZone();
        validateSite();
        validateRoofAngle();
        validateDiameter();
        validatePipeLength();
        validateRoomWidth();
        validateRoomLength();
        validateRoomHeight();

        if( $('#swietliki .error:visible').length == 0 ){
          $('#swietliki').submit();
        }else{
          alert('W formularzu wystąpiły błędy.')
        }
      });

      // VALIDATORY ////////////////////////////////////////////////////
      function validateZone(){
        if( getZone() ){
          jQuery('#zone_error').hide();
        }else{
          jQuery('#zone_error').show();
        }
      }
      function validateSite(){
        if( getSite() ){
          jQuery('#site_error').hide();
        }else{
          jQuery('#site_error').show();
        }
      }
      function validateRoofAngle(){
        if( getRoofAngle() < 0 ){
          jQuery('#roof_angle_error').show();
        }else{
          jQuery('#roof_angle_error').hide();
        }
      }
      function validateDiameter(){
        if( getDiameter() != 250 && getDiameter() != 350 && getDiameter() != 550   ){
          jQuery('#diameter_error').show();
        }else{
          jQuery('#diameter_error').hide();
        }
      }
      function validatePipeLength(){
        var max_length = 12;

        if( getDiameter() == 250 ){
          max_length = 6;
          $('#label_max_length').text(6);
        }
        if( getDiameter() == 350 ){
          max_length = 12;
          $('#label_max_length').text(12);
        }
        if( getDiameter() == 550 ){
          max_length = 12;
          $('#label_max_length').text(12);
        }

        if( getPipeLength() >= 0.5 && getPipeLength() <= max_length   ){
          $('#pipe_length').val( getPipeLength() );
          jQuery('#pipe_length_error').hide();
        }else{
          jQuery('#pipe_length_error').show();
        }
      }
      function validateRoomLength(){
        if( getRoomLength() >= 1  && getRoomLength() <= 20 ){
          jQuery('#room_length_error').hide();
          $('#room_length').val( getRoomLength() );
        }else{
          jQuery('#room_length_error').show();
        }
      }
      function validateRoomWidth(){
        if( getRoomWidth() >= 1 && getRoomWidth() <= 20 ){
          $('#room_width').val( getRoomWidth() );
          jQuery('#room_width_error').hide();
        }else{
          jQuery('#room_width_error').show();
        }
      }
      function validateRoomHeight(){
        if( getRoomHeight() >= 2 && getRoomHeight() <= 20 ){
          jQuery('#room_height_error').hide();
          $('#room_height').val( getRoomHeight() );
        }else{
          jQuery('#room_height_error').show();
        }
      }
      // GETTERY ////////////////////////////////////////////////////
      function getZone(){
        return $('#zone').val();
      }
      function getSite(){
        return $('#site').val();
      }
      function getRoofAngle(){
        return $('#roof_angle').val();
      }
      function getDiameter(){
        return $('#diameter').val();
      }

      function getPipeLength(){
        return parseFloat($('#pipe_length').val());
      }
      function getRoomWidth(){
        return parseFloat($('#room_width').val());
      }
      function getRoomLength(){
        return parseFloat($('#room_length').val());
      }
      function getRoomHeight(){
        return parseFloat($('#room_height').val());
      }
      // SETTERY ////////////////////////////////////////////////////
      function setZone(zone){
        $('#zone').val(zone);
        $('#zone_table td.active').removeClass('active');
        $('#zone_table td#'+zone).addClass('active');
      }
      function setSite(site){
        $('#site').val(site);
        $('#site_table td img.active').removeClass('active');
        $('#site_table td img[alt='+site+']').addClass('active');
      }
      function setRoofAngle(roof_angle){
        $('#roof_angle').val(roof_angle);
        $('#roof_angle_table td *.active').removeClass('active');
        $('#roof_angle_table td *[alt='+roof_angle+']').addClass('active');
      }
      function setDiameter(diameter){
        $('#diameter').val(diameter);
        $('#diameter_table td *.active').removeClass('active');
        $('#diameter_table td *[alt='+diameter+']').addClass('active');
      }
      function setPipeLength(pipe_length){
        $('#pipe_length').val(pipe_length);
      }
      function setRoomLength(room_length){
        $('#room_length').val(room_length);
      }
      function setRoomWidth(room_width){
        $('#room_width').val(room_width);
      }
      function setRoomHeight(room_height){
        $('#room_height').val(room_height);
      }
      ////////////////////////////////////////////////////////////////////////////////////////////////////
    })()
  });
</script>


<form id="swietliki" method="post" action="" >
  <h3>Wybór strefy klimatycznej</h3>
  <div class="row_box" >
    <table id="zone_table" >
      <tbody>
        <tr>
          <td id="b">B</td>
          <td id="s">S</td>
          <td id="i">I</td>
          <td id="r">R</td>
        </tr>
        <tr>
          <td id="f">F</td>
          <td id="u">U</td>
          <td id="g">G</td>
          <td id="y">Y</td>
        </tr>
        <tr>
          <td id="p">P</td>
          <td id="h">H</td>
          <td id="t">T</td>
          <td id="n">N</td>
        </tr>
      </tbody>
    </table>
    <div id="zone_error" class="error" >Proszę wybrać strefę klimatyczną</div>
    <input type="hidden" id="zone" name="zone" value="" />
  </div>

  <h3>Ustawienie względem stron świata</h3>
  <div id="site_table_holder" class="row_box site" >
    <?php // tutaj JS-em wpada tabelka z następnego bloczka ?>
    <div id="site_error" class="error" >Proszę wybrać stronę świata</div>
    <input type="hidden" id="site" name="site" value=""/>
  </div>

  <h3>Kąt nachylenia dachu</h3>
  <div class="row_box" >

    <table cellpadding="0" cellspacing="0" id="roof_angle_table">
      <tbody>
        <tr>
          <td><img class="roof_angle" alt="0" ></td>
          <td><img class="roof_angle" alt="15" ></td>
          <td><img class="roof_angle" alt="30" ></td>
          <td><img class="roof_angle" alt="45" ></td>
          <td><img class="roof_angle" alt="60" ></td>
        </tr>
      </tbody>
    </table>

    <input type="hidden" id="roof_angle" name="roof_angle" />
    <div id="roof_angle_error" class="error" >Proszę wybrać kąt nachylenia dachu</div>
  </div>

  <h3>Średnica świetlika</h3>
  <div class="row_box" >
    <table cellpadding="0" cellspacing="0" id="diameter_table">
      <tbody>
        <tr>
          <td>
            <img class="diameter_size" alt="250"  />
          </td>
          <td>
            <img class="diameter_size"  alt="350" />
          </td>
          <td>
            <img class="diameter_size"  alt="550" />
          </td>
        </tr>
      </tbody>
    </table>
    <input type="hidden" id="diameter" name="diameter">
    <div id="diameter_error" class="error" >Proszę wybrać średnicę świetlika</div>
  </div>

  <h3>Długość rury swietlika</h3>
  <div class="row_box" >
    <input type="text" id="pipe_length" name="pipe_length" /> [m]
    <div id="pipe_length_error" class="error" >Proszę podać wartość od 0.5-<span id="label_max_length">12</span> m</div>
  </div>

  <h3>Wymiary pomieszczenia</h3>
  <div class="row_box" >
    <table>
      <tbody>
        <tr>
          <td>długość: </td>
          <td><input type="text" name="room_length" id="room_length"  /> [m]</td>
          <td><span class="error" id="room_length_error" >Proszę podać wartość od 1-20</span></td>
        </tr>
        <tr>
          <td>szerokość: </td>
          <td><input type="text" name="room_width" id="room_width"  /> [m]</td>
          <td><span class="error" id="room_width_error" >Proszę podać wartość od 1-20</span></td>
        </tr>
        <tr>
          <td>wysokość: </td>
          <td><input type="text" name="room_height" id="room_height"  /> [m]</td>
          <td><span class="error" id="room_height_error" >Proszę podać wartość od 2-6</span></td>
        </tr>
      </tbody>
    </table>
  </div>
  <input type="button" name="ognia" id="ognia" value="Ognia!" />
</form>

<br/><br/>

<div style="<?php if( !$_POST ){ echo "display:none"; }?>" >
  <table class="result result_1" cellpadding="0" cellspacing="0">
    <tr>
      <td colspan="4" class="table_subtitle" >Świetliki sztywne</td>
    </tr>
    <tr>
      <th rowspan="2" >Typ pomieszenia</th>
      <th colspan="3" >Typ pogody</th>
    </tr>
    <tr>
      <th class="meteo_ico_1" ></th>
      <th class="meteo_ico_2" ></th>
      <th class="meteo_ico_3" ></th>
    </tr>
    <tr>
      <td class="lux_label" >Ilość światła</td>
      <td class="lux" ><?php echo intval($I_1)  ?> lux</td>
      <td class="lux" ><?php echo intval($I_2)  ?> lux</td>
      <td class="lux" ><?php echo intval($I_3)  ?> lux</td>
    </tr>
    <?php foreach($WNS as $key => $row){ ?>
      <tr>
        <td class="name name_<?php echo $key ?>" ><span><?php echo $row[0] ?></span></td>
        <td class="stream" ><?php if($room_area_factor < ceil($row[1]/$I_1)){echo '-';}else{ echo ceil($row[1]/$I_1);} ?></td>
        <td class="stream" ><?php if($room_area_factor < ceil($row[1]/$I_2)){echo '-';}else{ echo ceil($row[1]/$I_2);} ?></td>
        <td class="stream" ><?php if($room_area_factor < ceil($row[1]/$I_3)){echo '-';}else{ echo ceil($row[1]/$I_3);} ?></td>
      </tr>
    <?php } ?>
  </table>
</div>

<?php if($_POST){ ?>

  <?php $zestaw = '-';$rura = '-';$kolanko = '-';$podwieszka = '-'; ?>

  <?php if($diameter == 250 || $diameter == 350 || ($diameter == 550 && $roof_angle <= 15)){ ?>
    <?php $zestaw = 1 ?>
    <?php $rura = ceil( ($pipe_length - 2.1)/0.61 ) ?>
  <?php } ?>

  <?php if($diameter == 550 && $roof_angle > 15){ ?>
    <?php $zestaw = 1; ?>
    <?php $rura = ceil( ($pipe_length - 1.8 - 0.60)/0.61 ); ?>
    <?php $kolanko = 1; ?>
  <?php } ?>

  <?php if($pipe_length > 4){ ?>
    <?php $podwieszka = 1; ?>
  <?php } ?>

  <br/>

  <table class="result pieces_table" cellpadding="0" cellspacing="0">
    <tbody>
      <tr>
        <td></td>
        <th class="w_80" >zestaw</th>
        <th class="w_80" >rura</th>
        <th class="w_80" >kolanko</th>
        <th class="w_80" >podwieszka</th>
      </tr>
      <tr>
        <th>Ilość sztuk</th>
        <td><?php echo $zestaw ?></td>
        <td><?php echo $rura ?></td>
        <td><?php echo $kolanko ?></td>
        <td><?php echo $podwieszka ?></td>
      </tr>
    </tbody>
  </table>

  <br/><br/>

  <?php if( ($diameter==350 && $pipe_length <=4) || ($diameter == 550 && $pipe_length <=6) ){ ?>
    <table class="result result_2" cellpadding="0" cellspacing="0">
      <tr>
        <td colspan="4"class="table_subtitle" >Świetliki elastyczne</td>
      </tr>
      <tr>
        <th rowspan="2" >Typ pomieszenia</th>
        <th colspan="3" >Typ pogody</th>
      </tr>
      <tr>
        <th class="meteo_ico_1" ></th>
        <th class="meteo_ico_2" ></th>
        <th class="meteo_ico_3" ></th>
      </tr>
      <tr>
        <td class="lux_label" >Ilość światła</td>
        <td class="lux" ><?php echo intval($I_1e)  ?> lux</td>
        <td class="lux" ><?php echo intval($I_2e)  ?> lux</td>
        <td class="lux" ><?php echo intval($I_3e)  ?> lux</td>
      </tr>
      <?php foreach($WNS as $key => $row){ ?>
        <tr>
          <td class="name  name_<?php echo $key ?>" ><?php echo $row[0] ?></td>
          <td class="stream elastic" ><?php if($room_area_factor < ceil($row[1]/$I_1e)){echo '-';} else {echo ceil($row[1]/$I_1e);} ?></td>
          <td class="stream elastic" ><?php if($room_area_factor < ceil($row[1]/$I_2e)){echo '-';} else {echo ceil($row[1]/$I_2e);} ?></td>
          <td class="stream elastic" ><?php if($room_area_factor < ceil($row[1]/$I_3e)){echo '-';} else {echo ceil($row[1]/$I_3e);} ?></td>
        </tr>
      <?php } ?>
    </table>

    <?php $rura = '-';$podwieszka = '-'; ?>

    <?php $zestaw = 1; ?>
    <?php $rura = ceil( ($pipe_length - 2.1)/1.2 ); ?>

    <?php if($pipe_length > 4){ ?>
      <?php $podwieszka = 1  ?>
    <?php } ?>

    <br/>

    <table class="result pieces_table" cellpadding="0" cellspacing="0">
      <tbody>
        <tr>
          <td></td>
          <th class="w_80" >zestaw</th>
          <th class="w_80" >rura</th>
          <th class="w_80" >podwieszka</th>
        </tr>
        <tr>
          <th>Ilośc sztuk</th>
          <td><?php echo $zestaw ?></td>
          <td><?php echo $rura ?></td>
          <td><?php echo $podwieszka ?></td>
        </tr>
      </tbody>
    </table>

  <?php }else if($diameter==350 && $pipe_length > 4){ ?>
    <div class="error_info" >
      <b>Nie da się skonfigurować świetlika elastycznego według podanych parametrów.</b><br/>
      Maksymalna długość rury elastycznej ∅350mm to 4 metry
    </div>
  <?php }else if($diameter==550 && $pipe_length > 6){ ?>
    <div class="error_info" >
      <b>Nie da się skonfigurować świetlika elastycznego według podanych parametrów.</b><br/>
      Maksymalna długość rury elastycznej ∅550mm to 6 metrów
    </div>
  <?php } ?>

<?php } ?>
<div id="fb-root"></div>
<div class="fb-like" data-href="http://foto.ostol.pl/" data-send="false" data-width="450" data-show-faces="false"></div>