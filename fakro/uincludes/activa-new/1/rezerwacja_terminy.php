<?php

  $date_today = explode('-',date('Y-m-d')) ;
  $time_today = mktime(0,0,0,$date_today[1],$date_today[2], $date_today[0]);
  $redirect_location = 'none';

  $time_from  = $time_today;
  $time_to    = $time_today + (60*60*24);

  if(isset($_REQUEST['date']) ){

    $date_from = fix_date_arr($_REQUEST['date']['from']);
    $time_from = mktime(0,0,0,$date_from['month'],$date_from['day'], $date_from['year']);

    $date_to   = fix_date_arr($_REQUEST['date']['to']);
    $time_to   = mktime(0,0,0,$date_to['month'],$date_to['day'], $date_to['year']);

    $days_count = ($time_to - $time_from) / (60*60*24);


    if($days_count >0 && $time_from >= $time_today){

      $date_url_string =
      'date[from][day]='.    $date_from['day'].
      '&date[from][month]='.  $date_from['month'].
      '&date[from][year]='.   $date_from['year'].
      '&date[to][day]='.      $date_to['day'].
      '&date[to][month]='.    $date_to['month'].
      '&date[to][year]='.     $date_to['year'];

      if($KAMELEON_MODE == 0 ){ $redirect_location = 'http://www.hotel-activa.pl/rezerwacja.php?'.$date_url_string;}
      if($KAMELEON_MODE == 1 ){ $redirect_location = 'http://kameleon01.fakro.pl/index.php?page=99&'.$date_url_string;}

    }else{
      $error_1 = '<span class="error error_info error_dates"  >&nbsp;w formularzu podano zle daty&nbsp;</span>';
    }

  }

  //daty do zaznaczania selektów w kalendarzyku
  $from_exploded = explode('-',date('Y-m-d',$time_from));
  $to_exploded = explode('-',date('Y-m-d',$time_to));

  function fix_date_arr($date_arr){
    $result = explode('-',date('d-m-Y',strtotime(implode('-',$date_arr))));

    $result['day'] = $result[0];
    $result['month'] = $result[1];
    $result['year'] = $result[2];

    return $result;
  }


 if($KAMELEON_MODE == 0 ){ $action = 'http://www.hotel-activa.pl/rezerwacja.php';}
 if($KAMELEON_MODE == 1 ){ $action = 'http://kameleon01.fakro.pl/index.php?page=99';}
?>
<form id="date_select_form" action="<?php echo $action ?>" lang="<?php echo $action ?>"   method="post" style="" >

  <h3>Okres pobytu</h3>
  <?php echo $error_1 ?>
  <p>Od dnia:</p>
  <p>
    <select id="day_from" name="date[from][day]" >
      <?php for($a=1; $a<=31; $a++){ ?>
        <?php if(strlen($a)<2){ $a = '0'.$a; }?>
        <option <?php if($from_exploded[2] == $a){echo ' selected="selected" ';} ?> value="<?php echo $a ?>" ><?php echo $a ?></option>
        <?php } ?>
    </select>

    <select id="month_from" name="date[from][month]" >
      <?php for($a=1; $a<=12; $a++){ ?>
        <?php if(strlen($a)<2){ $a = '0'.$a; }?>
        <option <?php if($from_exploded[1] == $a){echo ' selected="selected" ';} ?> value="<?php echo $a ?>" ><?php echo $a ?></option>
        <?php } ?>
    </select>

    <select id="year_from" name="date[from][year]">
      <?php for($a=2011; $a<=2014; $a++){ ?>
        <option <?php if($from_exploded[0] == $a){echo ' selected="selected" ';} ?> value="<?php echo $a ?>" ><?php echo $a ?></option>
        <?php } ?>
    </select>
  </p>

  <p>Od dnia:</p>
  <p>
    <select id="day_to" name="date[to][day]">
      <?php for($a=1; $a<=31; $a++){ ?>
        <?php if(strlen($a)<2){ $a = '0'.$a; }?>
        <option <?php if($to_exploded[2] == $a){echo ' selected="selected" ';} ?> value="<?php echo $a ?>" ><?php echo $a ?></option>
        <?php } ?>
    </select>

    <select id="month_to" name="date[to][month]">
      <?php for($a=1; $a<=12; $a++){ ?>
        <?php if(strlen($a)<2){ $a = '0'.$a; }?>
        <option <?php if($to_exploded[1] == $a){echo ' selected="selected" ';} ?>  value="<?php echo $a ?>" ><?php echo $a ?></option>
        <?php } ?>
    </select>

    <select id="year_to" name="date[to][year]" >
      <?php for($a=2011; $a<=2014; $a++){ ?>
        <option <?php if($to_exploded[0] == $a){echo ' selected="selected" ';} ?>  value="<?php echo $a ?>" ><?php echo $a ?></option>
        <?php } ?>
    </select>
  </p>

  <input type="submit" id="submit_dates" value="Wybierz" />
  <input type="button" id="close_calendar_form" value="Anuluj" />
</form>

<?php

  function _d($a){
    echo '<pre>';
    var_dump($a);
    echo '</pre>';
  }

?>