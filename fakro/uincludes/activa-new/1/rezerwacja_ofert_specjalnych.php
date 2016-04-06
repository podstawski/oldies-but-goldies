<?php /* ?><script type="text/javascript" >


  //inArray()
  Array.prototype.inArray = function(v){
    for(var i in this){
    if(this[i] == v){
      return true;}
    }
    return false;
  }

  jQuery(function(){

  ///  podajemy id diva z oferta
  ///  kazdy div musi zawiera´c:
  ///  h2 - pierwszy element tego typu jest brany jako tytul opcji
  ///  .offer_period - text z tego elementu jest brany jako okres obowiazywania oferty
  ///  table.offer_prices - parami nazwa pokoju lub inna opcja : cena - tymi danymi wypelniamydrugiego selecta

  var offer_divs_arr = new Array('s119093', 's119089', 's119087',  's116942', 's116440', 's117275'); //divy w których sa oferty

  var empty_option = '<option value="0" style="" >-- wybierz --</option>'
  fillOffersSelect(); //zapelniamy selecta ofertami

  jQuery('#offer_select').change(function(){
    jQuery('#prices_select option').remove('')

    if(this.value != '0'){
      fillPricesOfOffer(this.value);
    }else{
      jQuery('#prices_select').append(empty_option)
    }
  });

  jQuery('#offer_select').change(function(){
    jQuery('#offer_error').hide();
  })
  jQuery('#prices_select').change(function(){
    jQuery('#prices_error').hide();
  })
  jQuery('#data_od').change(function(){
    jQuery('#data_od_error').hide();
  })
  jQuery('#data_do').change(function(){
    jQuery('#data_do_error').hide();
  })
  jQuery('#name').keyup(function(){
    jQuery('#name_error').hide();
  })
  jQuery('#email').keyup(function(){
    jQuery('#email_error').hide();
  })
  jQuery('#phone').keyup(function(){
    jQuery('#phone_error').hide();
  })

  $("#data_od").datepicker({
    dateFormat: 'yy-mm-dd'
  });

  $("#data_do").datepicker({
    dateFormat: 'yy-mm-dd'
  });

  jQuery('#submit_offer').click(function(){


    if( validate_fields() ){
      var offer_name     = jQuery('#offer_select option[value='+ jQuery('#offer_select').val() +']').html()
      var prices_select   = jQuery('#prices_select option[value='+ jQuery('#prices_select').val() +']').html()
      var data_od     = jQuery('#data_od').val();
      var data_do     = jQuery('#data_do').val();
      var name         = jQuery('#name').val();
      var company     = jQuery('#company').val();
      var email       = jQuery('#email').val();
      var phone       = jQuery('#phone').val();
      var remarks     = jQuery('#remarks').val();

      var string    = 'Nazwa oferty: '    +offer_name+'%br%';
        string   += 'Pokój: '            +prices_select+'%br%';
        string  += 'Data od: '          +data_od+'%br%';
        string  += 'Data do: '          +data_do+'%br%';
        string   += 'Imie i Nazwisko: '  +name+'%br%';
        string   += 'Firma: '            +company+'%br%';
        string   += 'Email: '            +email+'%br%';
        string   += 'Telefon:'            +phone+'%br%';
        string   += 'Uwaga! '            +remarks+'%br%';


      var additional_input = jQuery('<input>')
      .attr({
        'name':'string'
      })
      .val(string);

      jQuery('#offer_string').val(string);
      jQuery('#rezerwacja').submit()
    }else{
      return false;
    }
  })

  function validate_fields(){
    if(jQuery('#offer_select').val()  == '' || jQuery('#offer_select').val() == '0'){
      jQuery('#offer_error').show();
    }
    if(jQuery('#prices_select').val()  == '' || jQuery('#prices_select').val() == '0'){
      jQuery('#prices_error').show();
    }
    if(jQuery('#data_od').val().length < 3){
      jQuery('#data_od_error').show();
    }
    if(jQuery('#data_do').val().length < 3){
      jQuery('#data_do_error').show();
    }
    if(jQuery('#name').val().length < 3){
      jQuery('#name_error').show();
    }
    if( jQuery('#company').val().length > 0 && jQuery('#email').val().length < 6){
      jQuery('#email_error').show();
    }
    if(jQuery('#phone').val().length < 9){
      jQuery('#phone_error').show();
    }
    if(jQuery('.form_error:visible').length >0){
      return false;
    }else{
      return true;
    }
  }

  function fillOffersSelect(){

    jQuery('#c > div').each(function(key,row){
      var div_id = jQuery(row).attr('id');

      if(offer_divs_arr.inArray(div_id)){
        var title = jQuery(row).children('h2').eq(0).text();
        var period = jQuery(row).children('h3.offer_period').text();
        var value = div_id;

        var option = jQuery('<option>')
        .attr({
          value:div_id
        })
        .html(title);

        if(period){
          option.html(option.html()+' - '+period);
        }

        jQuery('#offer_select').append(option);
      }// end IF

    });
  }//end fillOffersSelect()



  function fillPricesOfOffer(box_id){
    var table = jQuery('#'+box_id).children('.offer_prices');

    jQuery(table).children('tbody').children('tr').each(function(key,row){
      var cells = jQuery(row).children('td');

      var option_description =  jQuery(cells[0]).text();
      var option_price =  parseInt(jQuery(cells[1]).text());

      if(option_price > 0){
        var option = jQuery('<option>')
        .attr({
          value: option_price
        })
        .html(option_description +' - '+ option_price+'zl');
        jQuery('#prices_select').append(option);
      }
    });
  }


  })


 </script>

 <?php

  if(isset($_POST['offer_string']) && strlen($_POST['offer_string']) > 10){

    $offer = htmlspecialchars(html_entity_decode($_POST['offer_string']));


    include("Mail.php");

    $m  = &Mail::Factory("smtp",$params);

    $header['From'] = "Strona www Activa <recepcja@hotel-activa.pl>";

    if($KAMELEON_MODE == 0 ){
      $header['To'] = 'recepcja@hotel-activa.pl';
    }else{
      $header['To'] = 'p.ostolski@fakro.pl';
    }

    $header['Subject'] = 'Rezerwacja oferty specjalnej';
    $header['Content-Type'] = "text/plain; charset=UTF-8";

$body ='
Witaj! Ktos zlozyl nowa rezerwacje.

'.str_replace('%br%','
' ,$offer).'
Dziekuje.

Twoj automatyczny asystent od przyjmowania rezerwacji zatroszczy sie o wszystko.
';

    $error = $m->send($header['To'], $header, $body);

  }
?>


 <form id="rezerwacja" method="post" action="#rezerwacja">
 <a name="rezerwacja_ofert_specjalnych" ></a>
  <input type="hidden" name="offer_string" id="offer_string" value="puste" />

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Rodzaj oferty: <span style="color:red" >*</span>
    </label>
    <select id="offer_select"  name="offer">
      <option value="0" >-- wybierz --</option>
    </select>
  </div>
  <div>
    <span id="offer_error" class="form_error d_none" >nie wybrano rodzaju oferty</span>
  </div>



  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Pokój: <span style="color:red" >*</span>
    </label>
    <select id="prices_select"  name="price" >
      <option value="0" >-- wybierz --</option>
    </select>
  </div>
  <div>
    <span id="prices_error" class="form_error d_none" >nie wybrano pokoju</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Data od: <span style="color:red" >*</span>
    </label>
    <input type="text" name="data_od" id="data_od" style="width:100px"/>
  </div>
  <div>
    <span id="data_od_error" class="form_error d_none" >nie wybrano daty</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Data do: <span style="color:red" >*</span>
    </label>
    <input type="text" name="data_do" id="data_do" style="width:100px"/>
  </div>
  <div>
    <span id="data_do_error" class="form_error d_none" >nie wybrano daty</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Imie i nazwisko: <span style="color:red" >*</span>
    </label>
    <input type="text" name="name" id="name" style="width:250px"/>
  </div>
  <div>
    <span id="name_error" class="form_error d_none" >nie podano imienia i nazwiska</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Nazwa firmy:
    </label>
    <input type="text" name="company" id="company" style="width:250px"/>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      E-mail:
    </label>
    <input type="text" name="email" id="email" style="width:250px"/>
  </div>
  <div>
    <span id="email_error" class="form_error d_none" >nie podano adresu e-mail</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Telefon: <span style="color:red" >*</span>
    </label>
    <input type="text" name="phone" id="phone" style="width:250px"/>

  </div>
  <div>
    <span id="phone_error" class="form_error d_none" >nie podano numeru telefonu</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Uwagi:
    </label>
    <textarea id="remarks" style="width:250px"></textarea>
  </div>

  <div style="margin-top:2px;" >
    <button id="submit_offer" style="margin-left:183px" >Złóż rezerwację</button>
    <?php

    if(isset($_POST['offer_string']) && strlen($_POST['offer_string']) > 10){
      if(PEAR::isError($re)) {
        //echo $re->toString().'<br>';
          //echo "S:".$re->getMessage();
          echo '<div style="margin:4px" id="reservation_sending_result"><span style="background-color:red;color:white">Blad wysylania rezerwacji</span></div>';
        }else{
          echo '<div style="margin:4px" id="reservation_sending_result"><span style="background-color:green;color:white;" >Rezerwacja zostala nadana</span></div>';
        }
      }

    ?>
  </div>

  <br/>
  <p>Warunkiem dokonania rezerwacji jest wplata 100% wartosci zamówionych uslug na konto:<br />
    <em>ING Bank Slaski 42 1050 1445 1000 00023 0159 4434</em></p>

  <p style="color:#aaaaaa;font-size:10px;" >Wysłanie formularza powoduje przesłanie informacji do recepcji hotelu.<br/>
  Niezwłocznie po otrzymaniu zgłoszenia skontaktujemy się z Państwem w celu potwierdzenia rezerwacji.</p>

  <p><span style="color:red" >*</span> oznacza pola wymagane </p>

 </form>
*/