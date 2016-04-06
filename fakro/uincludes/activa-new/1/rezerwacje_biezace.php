<?php
//ini_set('display_errors', '1');
//error_reporting(E_ALL);


?><script type="text/javascript" >

  //inArray()
  Array.prototype.inArray = function(v){
    for(var i in this){
    if(this[i] == v){
      return true;}
    }
    return false;
  }

  jQuery(function(){


    jQuery('#name').keyup(function(){
      jQuery('#name_error').hide();
    })
    jQuery('#company').keyup(function(){
      jQuery('#company_error').hide();
    })
    jQuery('#ulica').keyup(function(){
      jQuery('#ulica_error').hide();
    })
    jQuery('#dom').keyup(function(){
      jQuery('#dom_error').hide();
    })
    jQuery('#kod_pocztowy').keyup(function(){
      jQuery('#kod_pocztowy_error').hide();
    })
    jQuery('#poczta').keyup(function(){
      jQuery('#poczta_error').hide();
    })
    jQuery('#nip').keyup(function(){
      jQuery('#nip_error').hide();
    })
    jQuery('#email').keyup(function(){
      jQuery('#email_error').hide();
    })
    jQuery('#phone').keyup(function(){
      jQuery('#phone_error').hide();
    })


  jQuery('#submit_offer').click(function(){


    if( validate_fields() ){
      var name          = jQuery('#name').val();
      var company       = jQuery('#company').val();
      var ulica         = jQuery('#ulica').val();
      var dom           = jQuery('#dom').val();
      var kod_pocztowy  = jQuery('#kod_pocztowy').val();
      var poczta        = jQuery('#poczta').val();
      var nip           = jQuery('#nip').val();
      var email         = jQuery('#email').val();
      var phone         = jQuery('#phone').val();
      var remarks       = jQuery('#remarks').val();

      var string    = 'Imie i Nazwisko: '             +name+'%br%';
        string   += 'Firma: '                         +company+'%br%';
        string   += 'Ulica / miejscowość, nr domu: '  +ulica+ ', ' +dom+ '%br%';
        string   += 'Kod pocztowy, poczta: '          +kod_pocztowy+ ' ' +poczta+ '%br%';
        string   += 'NIP: '                           +nip+'%br%';
        string   += 'Email: '      +email+'%br%';
        string   += 'Telefon:'      +phone+'%br%';
        string   += 'Uwaga! '      +remarks+'%br%';


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

    if(jQuery('#name').val().length < 3){
      jQuery('#name_error').show();
    }
    if(jQuery('#company').val().length < 3){
      jQuery('#company_error').show();
    }
    if(jQuery('#ulica').val().length < 3){
      jQuery('#ulica_error').show();
    }
    if(jQuery('#dom').val().length < 1){
      jQuery('#dom_error').show();
    }
    if(jQuery('#kod_pocztowy').val().length < 3){
      jQuery('#kod_pocztowy_error').show();
    }
    if(jQuery('#poczta').val().length < 3){
      jQuery('#poczta_error').show();
    }
    if(jQuery('#nip').val().length < 10){
      jQuery('#nip_error').show();
    }
//    if( jQuery('#company').val().length > 0 && jQuery('#email').val().length < 6){
//      jQuery('#email_error').show();
//    }
    if(jQuery('#email').val().length < 6){
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

    $header['Subject'] = 'Zapytanie ze strony www';
    $header['Content-Type'] = "text/plain;\n\tharset=UTF-8;";

$body ='
Witaj! Oto zapytanie ze strony Activa:

'.str_replace('%br%','
' ,$offer).'
Dziekuje.

Twoj automatyczny asystent od przyjmowania rezerwacji zatroszczy sie o wszystko.
';

    $error = $m->send($header['To'], $header, $body);
  }
?>


 <form id="rezerwacja" method="post" action="#rezerwacja">
  <input type="hidden" name="offer_string" id="offer_string" value="puste" />

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Imie i nazwisko <span style="color:red" >*</span>
    </label>
    <input type="text" name="name" id="name" style="width:250px"/>
  </div>
  <div>
    <span id="name_error" class="form_error d_none" >nie podano imienia i nazwiska</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Nazwa firmy <span style="color:red" >*</span>
    </label>
    <input type="text" name="company" id="company" style="width:250px"/>
  </div>
  <div>
    <span id="company_error" class="form_error d_none" >nie podano nazwy firmy</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Ulica / miejscowość, nr domu <span style="color:red" >*</span>
    </label>
    <input type="text" name="ulica" id="ulica" style="width:197px"/>
    <input type="text" name="dom" id="dom" style="width:50px"/>
  </div>
  <div style="margin-left:183px">
    <span id="ulica_error" class="form_error d_none" style="margin-left:0" >nie podano ulicy / miejscowości</span>
    <span id="dom_error" class="form_error d_none" style="margin-left:0 ">nie podano nr domu</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Kod pocztowy, poczta <span style="color:red" >*</span>
    </label>
    <input type="text" name="kod_pocztowy" id="kod_pocztowy" style="width:70px"/>
    <input type="text" name="poczta" id="poczta" style="width:177px"/>
  </div>
  <div style="margin-left:183px">
    <span id="kod_pocztowy_error" class="form_error d_none" style="margin-left:0" >nie podano kodu pocztowego</span>
    <span id="poczta_error" class="form_error d_none" style="margin-left:0" >nie podano poczty</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      NIP <span style="color:red" >*</span>
    </label>
    <input type="text" name="nip" id="nip" style="width:250px"/>
  </div>
  <div>
    <span id="nip_error" class="form_error d_none" >nie podano nipu</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      E-mail <span style="color:red" >*</span>
    </label>
    <input type="text" name="email" id="email" style="width:250px"/>
  </div>
  <div>
    <span id="email_error" class="form_error d_none" >nie podano adresu e-mail</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Telefon <span style="color:red" >*</span>
    </label>
    <input type="text" name="phone" id="phone" style="width:250px"/>
  </div>
  <div>
    <span id="phone_error" class="form_error d_none" >nie podano numeru telefonu</span>
  </div>

  <div style="margin-top:2px;" >
    <label style="width:180px;display:inline-block" >
      Uwagi
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

  <table border="0" cellspacing="1" cellpadding="1" width="100%">
    <tbody>
      <tr>
        <td valign="top" align="right">
        <p><input type="checkbox" name="ZGODA[zgoda]" value="tak" /></p>
        </td>
        <td>Wyrażam zgodę na wykorzystywanie i przetwarzanie przez Aktiv sp. z o.o. z siedzibą w Muszynie przy ul. Złockie 78, moich danych osobowych zawartych w tym formularzu w celach marketingowych, zgodnie z ustawą z dn. 29.08.1997r. o ochronie danych osobowych (Dz. U. Nr 133, poz.883).</td>
      </tr>
      <tr>
        <td valign="top" align="right"><input type="checkbox" name="ZGODA[zgodaMarketing]" value="tak" /></td>
        <td>Wyrażam zgodę na otrzymywanie informacji promocyjnych, informacyjnych, reklamowych i marketingowych o produktach Aktiv sp. z o.o.&nbsp;33-370 Muszyna, ul. Złockie 78, na m&oacute;j adres e-mail i telefon zgodnie z ustawą z dnia 18.07.2002r. o Świadczeniu usług drogą elektroniczną (Dz. U. Nr 144, poz. 1204).</td>
      </tr>
    </tbody>
  </table>

  <p style="color:#aaaaaa;font-size:10px;" >Wysłanie formularza powoduje przesłanie informacji do recepcji hotelu.<br/>Niezwłocznie po otrzymaniu zgłoszenia skontaktujemy się z Państwem w celu potwierdzenia rezerwacji.   </p>

  <p><span style="color:red" >*</span> oznacza pola wymagane </p>

 </form>

