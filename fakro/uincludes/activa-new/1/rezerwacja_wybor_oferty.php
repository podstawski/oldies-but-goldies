<?php
  //ini_set('display_errors', 1);
  //ini_set('error_reporting', E_ALL);
  $date_from = $_REQUEST['date']['from'];
  $time_from = mktime(0,0,0,$date_from['month'],$date_from['day'], $date_from['year']);

  $date_to   = $_REQUEST['date']['to'];
  $time_to   = mktime(0,0,0,$date_to['month'],$date_to['day'], $date_to['year']);

  $days_count = ($time_to - $time_from) / (60*60*24);

  //wysyłanie mejla z rezerwacją
  if(strlen($_POST['email_text']) > 0){

    include("Mail.php");

    $m  = &Mail::Factory("smtp",$params);

    $header['From'] = "Strona www Activa <activa-recepcja@fakro.pl >";

    if($KAMELEON_MODE == 0){
      $header['To'] = 'recepcja@hotel-activa.pl,';
    }else{
      $header['To'] = 'p.ostolski@fakro.pl, activa-recepcja@fakro.pl, recepcja@hotel-activa.pl, marketing@hotel-activa.pl';
    }

    $header['Subject'] = 'Rezerwacja ze strony ACTIVA w hotelu';
    $header['Content-Type'] = "text/plain; charset=UTF-8";
    //treść mejla schodzi z ukrytego formularza koło buttona
    $body = $_POST['email_text'].'
    --------------------
    Czas: '.date('Y-m-d H:i:s').'

    Dziekuje.

    Twoj automatyczny asystent od przyjmowania rezerwacji zatroszczy sie o wszystko.';

    $error = $m->send($header['To'], $header, $body);

    if(PEAR::isError($re)) {
      $reservation_result =  '<div id="reservation_sending_result"><span style="background-color:red;color:white">Błąd wysylania rezerwacji</span></div>';
    }else{
      $reservation_result =  '<div id="reservation_sending_result"><span style="background-color:green;color:white;" >Zgłoszenie rezerwacji zostało wysłane</span></div>';
    }
  }
?>
<script>

  var offers = {

    /////////////////////////////////////////////////////////////////////////////////
    'pokoje_sezon_letni':{
      'status':'1',
      'title':'Pokój w sezonie letnim',
      'description':'Wszystkie pokoje standardowo wyposażone są w łazienkę, TV SAT, telefon.',

      'period_between':'01-04-2011/31-10-2011',
      'minimum_nights':1,

      'rooms':{
        'pokój jednoosobowy':                                 140,
        'pokój jednoosobowy**':                               160,
        'pokój dwuosobowy':                                   240,
        'pokój dwuosobowy Premium':                           280,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka />':280,
        'apartament bez balkonu':                             350,
        'apartament z balkonem':                              380,
        'apartament z jacuzzi':                               450
      },
      'radio':{ //inputy radio, tablica tablic
        'Dostawka':{
          'dostawka dla dziecka od 3 do 12 lat***':            70,
          'dostawka dla osoby dorosłej':                      100
        },
        'Druga dostawka':{
          'dostawka dla dziecka od 3 do 12 lat***':            70,
          'dostawka dla osoby dorosłej':                      100
        }
      }
    }


     /////////////////////////////////////////////////////////////////////////////////
    ,'pokoje_sezon_zimowy':{
      'status':'1',
      'title':'Pokój w sezonie zimowym',
      'description':'Wszystkie pokoje standardowo wyposażone są w łazienkę, TV SAT, telefon.',

      'period_between':'01-11-2011/31-03-2012',
      'minimum_nights':1,

      'rooms':{
        'pokój jednoosobowy':                                 180,
        'pokój jednoosobowy**':                               220,
        'pokój dwuosobowy':                                   360,
        'pokój dwuosobowy Premium':                           390,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka />':430,
        'apartament bez balkonu':                             450,
        'apartament z balkonem':                              500,
        'apartament z jacuzzi':                               600
      },
      'radio':{ //inputy radio, tablica tablic
        'Dostawka':{
          'dostawka dla dziecka od 3 do 12 lat***':            80,
          'dostawka dla osoby dorosłej':                      110
        },
        'Druga dostawka':{
          'dostawka dla dziecka od 3 do 12 lat***':            80,
          'dostawka dla osoby dorosłej':                      110
        }
      }
    }


    /////////////////////////////////////////////////////////////////////////////////
    ,'majowka':{
      'status':'1',
      'title':'Majówka w górach',
      'description':'Majówka w górach jest fajna.<br><b>4 noclegi</b>.',

      'period_between':'28-04-2012/06-05-2012',
      'required_nights':4,

      'rooms':{
        'pokój jednoosobowy':                                       610,
        'pokój jednoosobowy**':                                     680,
        'pokój dwuosobowy':                                         1090,
        'pokój dwuosobowy Premium':                                 1220,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka/>':       1220,
        'apartament bez balkonu':                                   1220,
        'apartament z balkonem':                                    1220,
        'apartament z jacuzii':                                     1760
      },
      'radio':{
        'Dostawka':{
          'dostawka dla osoby dorosłej':                            500,
          'dostawka dla dziecka od 3 do 12 lat***':                 340
        },
        'Druga dostawka':{
          'dostawka dla osoby dorosłej':                            500,
          'dostawka dla dziecka od 3 do 12 lat***':                 340
        }
      }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ,'weekendy_w_gorach_lato':{
      'status':'1',
      'title':'Weekendy w górach',
      'description':'Weekend w malowniczych górach Beskidu Sądeckiego.  <b>2 noclegi</b>.',

      'period_between':'01-04-2012/31-10-2012',
      'required_nights':2,

      'rooms':{
        'pokój jednoosobowy':                                       310,
        'pokój jednoosobowy**':                                     340,
        'pokój dwuosobowy':                                         550,
        'pokój dwuosobowy Premium':                                 610,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka/>':       610,
        'apartament bez balkonu':                                   610,
        'apartament z balkonem':                                    610,
        'apartament z jacuzii':                                     880

      },
      'radio':{
        'Dostawka':{
          'dostawka dla osoby dorosłej':                            250,
          'dostawka dla dziecka od 3 do 12 lat***':                 170
        },
        'Druga dostawka':{
          'dostawka dla osoby dorosłej':                            250,
          'dostawka dla dziecka od 3 do 12 lat***':                 170
        }
      }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ,'wakacje_w_gorach':{
      'status':'1',
      'title':'Wakacje w górach',
      'description':'Wakacje w górach to najlepszy wypoczynek.<br><b>6 noclegów</b>.',

      'period_between':'01-04-2012/31-10-2012',
      'required_nights':6,

      'rooms':{
        'pokój jednoosobowy':                                       960,
        'pokój jednoosobowy**':                                     1060,
        'pokój dwuosobowy':                                         1710,
        'pokój dwuosobowy Premium':                                 1910,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka/>':       1910,
        'apartament bez balkonu':                                   1910,
        'apartament z balkonem':                                    1910,
        'apartament z jacuzii':                                     2780
      },
      'radio':{
        'Dostawka':{
          'dostawka dla osoby dorosłej':                            750,
          'dostawka dla dziecka od 3 do 12 lat***':                 510
        },
        'Druga dostawka':{
          'dostawka dla osoby dorosłej':                            750,
          'dostawka dla dziecka od 3 do 12 lat***':                 510
        }
      }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ,'pakiety_rodzinne':{
      'status':'1',
      'title':'Wakacyjne pakiety rodzinne',
      'description':'Pakiet dla osoby dorosłej i dziecka w wieku 3-12 lat. <b>6 noclegówi</b>.',

      'period_between':'01-04-2012/31-10-2012',
      'required_nights':6,

      'rooms':{
        'pokój jednoosobowy (dorosły + dziecko)':                                         1470,
        'pokój dwuosobowy (2 dorosłych + dziecko)':                                       2220,
        'studio rodzinne (2 dorosłych + 2 dzieci)':                                      2930
      }
    }









    /////////////////////////////////////////////////////////////////////////////////
    //                                                                             //
    //                           REZERWACJE STARE                                  //
    //                                                                             //
    /////////////////////////////////////////////////////////////////////////////////






    /////////////////////////////////////////////////////////////////////////////////
    ,'weekendy_w_gorach_zima':{
      'status':'1',
      'title':'Weekendy w górach - zima',
      'description':'Weekend w malowniczych górach Beskidu Sądeckiego.  <b>2 noclegi</b>.',

      'period_between':'01-11-2011/30-03-2012',
      'required_nights':2,

      'rooms':{
        'pokój jednoosobowy':                                       380,
        'pokój jednoosobowy**':                                     450,
        'pokój dwuosobowy':                                         760,
        'pokój dwuosobowy Premium':                                 870,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka/>':       870,
        'apartament bez balkonu':                                   870,
        'apartament z balkonem':                                    870,
        'apartament z jacuzii':                                      1140

      },
      'radio':{
        'Dostawka':{
          'dostawka dla osoby dorosłej':                            290,
          'dostawka dla dziecka od 3 do 12 lat***':                 200
        },
        'Druga dostawka':{
          'dostawka dla osoby dorosłej':                            290,
          'dostawka dla dziecka od 3 do 12 lat***':                 200
        }
      }
    }


    ,'rezerwacja_pokoju':{
      'status':'0',
      'title':'Pokój w wybranym terminie',
      'description':'Wszystkie pokoje standardowo wyposażone są w łazienkę, TV SAT, telefon.',
      //'periods':{},
      //'required_nights':0,
      //'period_between'
      'rooms':{
        'pokój jednoosobowy':                                 180,
        'pokój jednoosobowy**':                               220,
        'pokój dwuosobowy':                                   360,
        'pokój dwuosobowy Premium':                           390,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka />':430,
        'apartament bez balkonu':                             450,
        'apartament z balkonem':                              500,
        'apartament z jacuzzi':                               600
      },
      'radio':{ //inputy radio, tablica tablic
        'Dostawka':{
          'dostawka dla dziecka od 3 do 12 lat***':            80,
          'dostawka dla osoby dorosłej':                      110
        },
        'Druga dostawka':{
          'dostawka dla dziecka od 3 do 12 lat***':            80,
          'dostawka dla osoby dorosłej':                      110
        }
      },
      'checkbox':{ //inputy checkbox
        //'Kawiaty w pokoju':                                 40,
        //  'wanna':                                          80,
        //  'mydło':                                          160
      }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ,'sylwester_w_gorach':{
      'status':'0',
      'title':'TEST Sylwester w górach',
      'description':'Sylwester na nartach - aktywne spędzenie czasu. <b>5 noclegów</b>.',

      'periods':{
        1:'27-12-2011/01-01-2012',
        2:'30-12-2011/04-01-2012'
      },
      'rooms':{
        'pokój jednoosobowy':                                   970,
        'pokój jednoosobowy**':                                 1160,
        'pokój dwuosobowy':                                     1970,
        'pokój dwuosobowy Premium':                             2270,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka/>':   2270,
        'apartament bez balkonu':                               2270,
        'apartament z balkonem':                                2270,
        'apartament z jacuzii':                                 2990
      },
      'radio':{
        'Dostawka':{
          'dostawka dla osoby dorosłej':                        840,
          'dostawka dla dziecka od 3 do 12 lat***':              590
        },
        'Druga dostawka':{
          'dostawka dla osoby dorosłej':                        840,
          'dostawka dla dziecka od 3 do 12 lat***':              590
        },

        'Bal sylwestrowy':{
          '1 osoba dorosła':                                    300,
          '2 osoby dorosłe':                                    600
        },
        'Kinder Bal':{
          '1 dziecko < 12 lat':                                 80,
          '2 dzieci < 12 lat':                                  160
        }
      }
      //'checkbox':{
      //  'wanna':                                 80,
      //  'mydło':                                  160
      //}
    }

    /////////////////////////////////////////////////////////////////////////////////
    ,'trzech_kroli_w_gorach':{
      'status':'0',
      'title':'Trzech Króli w górach',
      'description':'Sylwester na nartach - aktywne spędzenie czasu. <b>3 noclegi</b>.',

      'periods':{
        1:'05-01-2012/08-01-2012'
      },
      'rooms':{
        'pokój jednoosobowy':                                           560,
        'pokój jednoosobowy**':                                         650,
        'pokój dwuosobowy':                                             1110,
        'pokój dwuosobowy Premium':                                     1280,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka/>':           1280,
        'apartament bez balkonu':                                       1280,
        'apartament z balkonem':                                        1280,
        'apartament z jacuzii':                                         1680
      },
      'radio':{
        'Dostawka':{
          'dostawka dla dziecka od 3 do 12 lat***':                      290,
          'dostawka dla osoby dorosłej':                                410
        },
        'Druga dostawka':{
          'dostawka dla dziecka od 3 do 12 lat***':                      290,
          'dostawka dla osoby dorosłej':                                410
        }
      }
    }

    /////////////////////////////////////////////////////////////////////////////////
    ,'ferie_w_gorach':{
      'status':'0',
      'title':'Ferie w górach',
      'description':'Ferie zimowe to czas na kilka dni odpoczynku w górach. <b>7 noclegów</b>.',

      'period_between':'14-01-2012/25-02-2012',
      'required_nights':7,

      'rooms':{
        'pokój jednoosobowy':                                       1360,
        'pokój jednoosobowy**':                                     1590,
        'pokój dwuosobowy':                                         2710,
        'pokój dwuosobowy Premium':                                 3120,
        'studio rodzinne (cena dla 2 osób)<druga_dostawka/>':       3120,
        'apartament bez balkonu':                                   3120,
        'apartament z balkonem':                                    3120,
        'apartament z jacuzii':                                     4130
      },
      'radio':{
        'Dostawka':{
          'dostawka dla osoby dorosłej':                            1000,
          'dostawka dla dziecka od 3 do 12 lat***':                 690
        },
        'Druga dostawka':{
          'dostawka dla osoby dorosłej':                            1000,
          'dostawka dla dziecka od 3 do 12 lat***':                 690
        }
      }
    }

  }; // END OFFERS


  jQuery(function(){

    // rezerwacja by ostol

    ///////////////////////////////////
    ///////////////////////////////////
    ////                           ////
    ////         ON-LOAD           ////
    ////                           ////
    ///////////////////////////////////
    ///////////////////////////////////

    //pobieramy daty wybrane w kalendarzyku w headerze
    room_date_from = function(){ return jQuery('#day_from').val()+'-'+jQuery('#month_from').val()+'-'+jQuery('#year_from').val(); }
    room_date_to = function(){ return jQuery('#day_to').val()+'-'+jQuery('#month_to').val()+'-'+jQuery('#year_to').val(); }

    jQuery('#reservation').prepend(jQuery('#date_select_form'));

    // var offers_list_holder = jQuery('#lista_ofert');

    generateOffers(offers);

    //ukrywanie komunikata "rezerwacja została wysłana" po 5 sekundach od przeładowania strony
    //if( jQuery('#reservation_sending_result').length >0 ){
    //  window.setTimeout(function(){
    //    jQuery('#reservation_sending_result').fadeOut(function(){
    //     jQuery('#reservation_sending_result').remove()
    //    })
    //  }, 10000)
    //}

    if(location.hash.replace('#','').length){
      //przy wejściu na rezerwacje z oferty pobieramy nazwe oferty z hasza w urlu
      jQuery('#'+location.hash.replace('#','')+' .period').eq(0).click();
      jQuery('#'+location.hash.replace('#','')+' ').addClass('active');
      location.hash = '';
    }else{
      jQuery('#lista_ofert .period:not(.error)').eq(0).click(); //klika w pierwsza oferte przy onLoad
    }

    operateAction(); //obliczenie ceny przy onLoad

    // przeliczanie cen po kliku w okresy ofert oraz opcje ofert
    jQuery('.period, .radios_options input, .checkbox_options input').live('click', function(){
      operateAction();
    })

    //przeliczanei cen po zmianie pokoju
    jQuery('#pokoj').change(function(){
      jQuery('.error.error_dostawka').remove();
      jQuery('.options input[type=radio]').removeAttr('checked');
      operateAction();
    })



    //jeśli po przeładowaniu w kalendarzyku wyskoczy błąd to pokazac kalendarzyk
    if(jQuery('.error_dates').length>0){
      jQuery('#date_select_form').show();
    }

    //po kliknięciu w button zmiany daty pokazac kalendarzyk
    jQuery('.change_dates')
    .click(function(){
      jQuery('#date_select_form').fadeIn();
    })
    .opTooltip({
      'css':{      //required
        'color':'white',
        'background-color':'#FD7B22',
        'font-weight':'bold',
        'border':'1px solid white'
      },
      'text':function(element){   //required
        return jQuery(element).attr('title');
      }
    });

    jQuery('#close_calendar_form').click(function(){
      jQuery('#date_select_form').fadeOut();
    });



    jQuery('#send_reservation').click(function(){
      jQuery('#reservation .error:not(.error_dates, #date_from.error, #date_to.error, )').remove();//usuwamy dla pewności pozostałe błędy po poprzedniej walidacji

      validateRoom();
      validateName();
      validatePhone();
      validateDrugaDostawka(); //<druga_dostawka> // <bez_dostawki/>

      //tutaj dodajemy inne walidatory, validator po validacji ma we formularzu dodawać klase error
      //po obecnosci tej klasy skrypt sprawdza czy mozna iśc dalej czy nie
      if(jQuery('#reservation .error').length > 0){ // jeśli sa jakies błędy
        alert('W formularzu rezerwacji wystąpiły błędy.');
        return false;
      }else{

        if(confirm('Czy napewno wysłać rezerwację?') ){
          prepareMail();
          printReservation();
          return true;
          // i potem leci submit z formularza i przeładowanie strony
        }else{
          return false;
        }

      }
    })

    ///////////////////////////////////
    ///////////////////////////////////
    ////                           ////
    ////         FUNKCJE           ////
    ////                           ////
    ///////////////////////////////////
    ///////////////////////////////////

    function operateAction(){ //funkcja działa po klikniuęciu w rodzaj pokoju, dostawkę, ofertę
      operateDostawki(); //<druga_dostawka>
      countPayment();
    }


    function generateOffers(offers_data){
      jQuery('#lista_ofert').html('');
      //generujemy liste ofert na podstawie konfiga
      for(i in offers_data){
        if(offers_data[i]['status'] == 1){
          generateOfferHolder(offers_data[i], i);
        }// end IF
      }// end FOR

      //dodajemy dwa śródtytuliki na liście ofert
      //jQuery('#pokoje_sezon_letni').before(  jQuery('<div class="offer_separator" >').html('Rezerwacja pokoi') );
      jQuery('#pokoje_sezon_letni').before(  jQuery('<div class="offer_separator" >').html('Rezerwacja pokoi') );
      jQuery('#pokoje_sezon_zimowy').after(  jQuery('<div class="offer_separator" >').html('Rezerwacja ofert specjalnych') );
    }


    function countPayment(){
      var TOTAL_PAYMENT = 0;

      var days_factor = parseInt( jQuery('#days_factor').val() );
      var room_price = parseInt( jQuery('#pokoj').val() );

      TOTAL_PAYMENT += room_price * days_factor;
      jQuery('input:visible[type=checkbox]:checked, input:visible[type=radio]:checked').each(function(key,row){
        TOTAL_PAYMENT += jQuery(row).val() * days_factor;
      })

      if(TOTAL_PAYMENT > 0){
        jQuery('#total_payment').html(TOTAL_PAYMENT + ' zł');
      }else{
        jQuery('#total_payment').html('0 zł');
      }
    }


    /////////////////////////////////////////////////////////////////////////////////////
    /// FUNKCJA PRZYGOTOWUJE TREŚĆ MEJLA REJESTRACYJNEGO
    /// I UMIESZCZA GO W UKRYTYM FORMIE OBOK KLAWISZA SEND
    /////////////////////////////////////////////////////////////////////////////////////
    function prepareMail(){

      var br =  '\n';

      var EMAIL_TEXT = 'Witaj! ' + br + br;
      var EMAIL_TEXT = 'Oto nowa rezerwacja:' + br + br;

      EMAIL_TEXT += '----- OFERTA -----'+br;
      EMAIL_TEXT += 'Nazwa oferty: '    + jQuery('#offer_title').text() + br;
      EMAIL_TEXT += br;
      EMAIL_TEXT += 'Od dnia: '     + jQuery('#date_from').text() + br;
      EMAIL_TEXT += 'Do dnia: '     + jQuery('#date_to').text() + br;

      EMAIL_TEXT += 'Ilosc dni: '   + countDaysBetween(jQuery('#date_from').text(),jQuery('#date_to').text()) + br;

      var room_price = parseInt( jQuery('#pokoj').val() );
      var room_name  = jQuery('#pokoj option:selected').text();
      var days_factor = parseInt( jQuery('#days_factor').val() );
      EMAIL_TEXT += 'Pokoj: '+room_name + ' * ' + days_factor;

      EMAIL_TEXT += br + br;
      EMAIL_TEXT += '----- OPCJE -----'+br ;

      jQuery('input:visible[type=checkbox]:checked, input:visible[type=radio]:checked').each(function(key,row){
        EMAIL_TEXT += jQuery(row).attr('title')+' ('+jQuery(row).attr('name')+')'+': '+jQuery(row).val()+' zł'+ ' * ' + days_factor + br;
      })

      EMAIL_TEXT += br ;
      EMAIL_TEXT += 'Do zaplacenia: '+jQuery('#total_payment').text();

      EMAIL_TEXT += br + br ;
      EMAIL_TEXT += '----- DANE KONTAKTOWE -----'+br;
      EMAIL_TEXT += 'Imie i nazwisko: '+jQuery('#name').val() + br;
      EMAIL_TEXT += 'Firma: '+jQuery('#company').val() + br;
      EMAIL_TEXT += 'E-mail: '+jQuery('#email').val() + br;
      EMAIL_TEXT += 'Telefon: '+jQuery('#phone').val() + br;
      EMAIL_TEXT += 'Uwagi: '+jQuery('#remarks').val() + br;

      jQuery('#email_text').val(EMAIL_TEXT);
    }




    function validateRoom(){
      if( jQuery('#pokoj').val() == 0){

        var error = jQuery('<tr class="error" ><td colspan="2" class="error error_info error_pokoj" style="padding:0px"  >&nbsp;Pokój jest wymagany</td></tr>');

        jQuery('#pokoj').change(function(){
          error.remove();
        }).click(function(){
          jQuery(this).keyup();
        });

        jQuery('#pokoj').parent().parent().after(error);
      }
    }


    function validatePhone(){
      if( jQuery('#phone').val().length < 9){
        var error = jQuery('<tr class="error"><td colspan="2" class="error error_info error_phone" style="padding:0px"  >&nbsp;Telefon jest wymagany</td></tr>');
        jQuery('#phone').parent().parent().after(error);
      }
      jQuery('#phone').keyup(function(){
        error.remove();
      }).click(function(){
        jQuery(this).keyup();
      });
    };



    function validateName(){
      if( jQuery('#name').val().length < 2){
        var error = jQuery('<tr class="error"><td colspan="2" class="error error_info error_name" style="padding:0px"  >&nbsp;Imie i nazwisko jest wymagane</td></tr>');
        jQuery('#name').parent().parent().after(error);
      }
      jQuery('#name').keyup(function(){
        error.remove();
      }).click(function(){
        jQuery(this).keyup();
      });
    }






    /////////////////////////////////////////////////////////////////////////////////////
    // kombinacje alpejskie co by obsłużyc drugą dostawkę dla pokoi typu studio rodzinne
    /////////////////////////////////////////////////////////////////////////////////////

    function operateDostawki(){ //<druga_dostawka> <bez_dostawki>

        //ten kodzik ukrywa dodatkowe opcje i pokazuje je tylko dla wybranego typu pokoju, sprawdza po nazwie dodatkowego znacznika <druga_dostawka/> w danych JSON na górze
        if(jQuery('#pokoj option:selected druga_dostawka').length  > 0){
          jQuery('.druga_dostawka').show();
        }else{
          jQuery('.druga_dostawka').hide();
          jQuery('.druga_dostawka input[type=radio]').removeAttr('checked');
        }

        if(jQuery('#pokoj option:selected bez_dostawki').length  > 0){
          jQuery('.dostawka').hide();
          jQuery('.dostawka input[type=radio]').removeAttr('checked');
        }else{
          jQuery('.dostawka').show();
        }
    }


    function validateDrugaDostawka(){ //<druga_dostawka>

      if(jQuery('#pokoj option:selected druga_dostawka').length > 0){

        if(jQuery('input[name=dostawka]:checked, input[name=druga_dostawka]:checked ').length < 2){ //jeśli nie zaznaqczono dwóch dostawek
          //wyswietlamy błąd
          var error = jQuery('<div class="error error_info error_dostawka" >').html('&nbsp;Dla pokoju rodzinnego wymagane są obie dostawki.');
          jQuery('.dostawka h3').after( error );
        }

        jQuery('input[name=dostawka], input[name=druga_dostawka]').click(function(){
          error.remove();
        })

      }// end IF
    }//end validateDrugaDostawka()





    ////////////////////////////////////////////////////////////////////////
    // Generuje listę ofert
    ////////////////////////////////////////////////////////////////////////
    function generateOfferHolder(data, id){

      var offers_list_holder    = jQuery('#lista_ofert');
      var title                 = jQuery('<div class="h3">').html(data.title);
      var description           = jQuery('<div class="p">').html(data.description);
      var offer_holder          = jQuery('<div>')
      .attr('id',id)
      .addClass('offer_holder')
      .append(title)
      .append(description);

      //po kliknięciu w ofertę, która ma czerwone terminy pokazujemy błąd
      offer_holder.children('.p,.h3').click(function(){
        jQuery(this).parent().find('.period').eq(0).click();
      })

      var periods_holder = jQuery('<div>').addClass('periods_holder');

      offer_holder.prepend(periods_holder);

      offers_list_holder.append(offer_holder);

      /////////////////////////////////////////
      // oferta typu pokój
      /////////////////////////////////////////
      if(  !data.periods && !data.period_between && !data.required_nights){
        periods_holder.html('Twój termin:');
        var period = jQuery('<div>')
        .addClass('period')
        .html(room_date_from()+'/'+room_date_to());

        period.click(function(){
          operatePeriodClick(this,data);
        })
        addTooltip(period, 'Oferta dostepna')
        periods_holder.append(period);
      }

      //////////////////////////////////////////////////////////////
      // oferty na wybrane okresy kilkudniowe np konkretny tydzień
      /////////////////////////////////////////////////////////////
      if( data.periods ){
        periods_holder.html('Dostepne terminy:');
        for(t in data.periods){
          var period = jQuery('<div>')
          .addClass('period')
          .html(data.periods[t])
          .click(function(){
            operatePeriodClick(this,data);
          });

          addTooltip(period, 'Oferta dostepna')
          periods_holder.append(period);
        }// end FOR
      }

      ///////////////////////////////////////////////////////////////////////////////////////
      //oferta dostępna przez dłuższy przedział czasu, a wymagany okres pobytu to konkretna ilośc dni
      ///////////////////////////////////////////////////////////////////////////////////////
      if( data.period_between && data.required_nights){
        periods_holder.html('Dostępne w okresie:');

        var between = data.period_between.split('/');

        var period = jQuery('<div>')
        .addClass('period')
        .html(between[0]+'/'+between[1]);

        var between = data.period_between.split('/');
        var a = countDaysBetween(between[0],room_date_from());
        var b = countDaysBetween(room_date_to(),between[1]);

        var period_between = a < 0 || b < 0; //false if proper

        if(period_between || countDaysBetween(room_date_from(),room_date_to()) != data.required_nights ){
          period.addClass('error');
          addTooltip(period, 'Wybrany termin musi obejmować dokładnie '+data.required_nights+' noclegi(-ów)<br/>w czasie obowiązywania oferty.');
        }else{
          addTooltip(period, 'Oferta dostepna');
        }

        period.click(function(){
          operatePeriodClick(this,data,true);
        });

        periods_holder.append(period);
      }

      ///////////////////////////////////////////////////////////////////////////////////////
      //oferta dostępna przez dłuższy przedział czasu i wymagany minimalny okres pobytu (również jeden dzień)
      ///////////////////////////////////////////////////////////////////////////////////////
      if( data.period_between && data.minimum_nights){
        periods_holder.html('Dostęne w okresie:');

        var between = data.period_between.split('/');

        var period = jQuery('<div>')
        .addClass('period')
        .html(between[0]+'/'+between[1]);

        var a = countDaysBetween(between[0],room_date_from());
        var b = countDaysBetween(room_date_to(),between[1]);

        var period_between = a < 0 || b < 0; //false if proper

        if(period_between || countDaysBetween(room_date_from(),room_date_to()) < data.minimum_nights ){
          period.addClass('error');
          addTooltip(period, 'Wybrany termin musi obejmować minimalnie '+data.minimum_nights+' noclegi(-ów)<br/>w czasie obowiązywania oferty.');
        }else{
          addTooltip(period, 'Oferta dostepna');
        }

        period.click(function(){
          operatePeriodClick(this,data,true);
        });

        periods_holder.append(period);
      }// end IF
    }// end FUNCTION generate_offer


    function prepareOfferReloadUrl(offer_id){
      var def_action_url = jQuery('#date_select_form').attr('lang') ;
      var location = def_action_url+'#'+offer_id;
      jQuery('#date_select_form').attr('action', location);
    }

    /////////////////////////////////////////////////////////////
    // obsluga klikniecia w termin
    ////////////////////////////////////////////////////////////
    function operatePeriodClick(period,data,date_source){


      var offer_id = jQuery(period).parent().parent().attr('id');
      //window.location.hash = offer_id;
      prepareOfferReloadUrl(offer_id);



      jQuery('.offer_holder').removeClass('active');
      jQuery(period).parent().parent().addClass('active');

      jQuery('#offer_title').html(data.title);

      //skąd pobierac dane o datach
      if(date_source == true){
        //tutaj pobieramy z formularza wyboru daty
        //tutaj wpada przy długich ofertach liczonych za dzień
        var c = room_date_from()+'/'+room_date_to();
        var dates = c.split('/');
      }else{
        //tutaj pobieramy z oferty
        //tutaj wpada przy samych pokojach albo ofertach w wybranym okresie
        var dates = jQuery(period).html().split('/');
      }

      jQuery('#date_from').html(dates[0]);
      jQuery('#date_to').html(dates[1]);

      if(jQuery(period).hasClass('error') ){
        jQuery('#date_from, #date_to').addClass('error');
        alert('Wybrany okres pobytu nie pokrywa się z okresem dostępności tej oferty.\nProszę dopasować okres pobytu (ilość noclegów) do tej oferty.');
      }else{
        jQuery('#date_from, #date_to').removeClass('error');
      }

      jQuery('.period').removeClass('active_period')
      jQuery(period).addClass('active_period');

      fillRoomsSelect(data.rooms);

      jQuery('tr.to_remove').remove();

      fillRadio(data.radio);
      fillCheckbox(data.checkbox);

      var days_between = countDaysBetween( jQuery('#date_from').text(), jQuery('#date_to').text() );
      jQuery('#nights').text(days_between);

      setPriceForDaysFactor(data,dates);

    } // end operatePeriodClick




    ////////////////////////////////////////////////////////////////////////
    // generuje inputy radio z dostawkami itp
    ////////////////////////////////////////////////////////////////////////
    function fillRadio(data){

      var table = jQuery('#form_table tbody');

      if(!isEmpty(data) ){
        for(name in data){
          var data_row = data[name];

          var title_cell = jQuery('<td>')
          .attr('colspan','2')
          .html('<h3>'+name+'</h3>');

          var title_row = jQuery('<tr>')
          .addClass('to_remove')
          .addClass(name.toLowerCase().replace(' ','_'))
          .append(title_cell);

          if( title_row.hasClass('druga_dostawka')){
           title_row.addClass('d_none');
          }

          table.append(title_row);

          for(r in data_row){

            var left_cell = jQuery('<td>')
            .addClass('l')
            .html(r);

            var right_cell = jQuery('<td>')
            .addClass('r');

            var table_row = jQuery('<tr>')
            .addClass('radios_options')
            .addClass('options')
            .addClass('to_remove')
            .addClass(name.toLowerCase().replace(' ','_'))
            .append(left_cell)
            .append(right_cell);

            if( table_row.hasClass('druga_dostawka')){
              table_row.addClass('d_none');
            }

            var label = jQuery('<label>').html(data_row[r]+' zł');

            var input = jQuery('<input type="radio" title="'+r+'" name="'+name.toLowerCase().replace(' ','_')+'" value="'+data_row[r]+'" />');
            //można to było zrobić na .attr({}) ale oczywiście IE7......

            right_cell
            .append(input)
            .append(label);

            table.append(table_row);

            if( left_cell.find('hidden').length == true ){
              table_row
              .addClass('druga_dostawka')
              .hide();
            }
          }// end for
        }// edn for

      }// end IF
    }// end  fillRadio


    ////////////////////////////////////////////////////////////////////////
    // generuje checkboxy z opcjami
    ////////////////////////////////////////////////////////////////////////
    function fillCheckbox(data){
      var table = jQuery('#form_table tbody');

      if(!isEmpty(data)){
        var name = 'Opcje';
        var title_cell = jQuery('<td>')
        .attr('colspan','2')
        .html('<h3>'+name+'</h3>');

        var title_row = jQuery('<tr>')
        .attr('colspan','2')
        .addClass('to_remove')
        .append(title_cell);

        table.append(title_row)

        for(r in data){
          var left_cell   = jQuery('<td>')
          .addClass('l')
          .html(r);

          var right_cell  = jQuery('<td>')
          .addClass('l');

          var table_row   = jQuery('<tr>')
          .addClass('checkbox_options')
          .addClass('to_remove')
          .addClass('options')
          .addClass(name.toLowerCase())
          .append(left_cell)
          .append(right_cell);

          var label = jQuery('<label>').html(data[r]+' zł');
          //var input = jQuery('<input type="checkbox" title="'+r+'" name="'+name.toLowerCase().replace(' ','_')+'" value="'+data_row[r]+'" />');
          var input = jQuery('<input type="checkbox" title="'+r+'" name="'+name.toLowerCase().replace(' ','_')+'" value="'+data[r]+'" />');

          right_cell
          .append(input)
          .append(label);

          table.append(table_row)
        }// end for
      }// end if
    }// end fillCheckbox


    ////////////////////////////////////////////////////////////////////////
    //ustawia odpowiedni współczynnikliczenia cen za nocleg
    // w zależności od tego, czy w ofercie podana jest cena za jedną dobę czy za pakiet
    ////////////////////////////////////////////////////////////////////////
    function setPriceForDaysFactor(data,dates){
      if( !data.periods && !data.period_between && !data.required_nights && !data.minimum_nights){
        var factor = countDaysBetween(dates[0],dates[1])
      };
      if(data.periods){
        var factor = 1;
      }
      if(data.period_between && data.required_nights){
        var factor = 1;
      }
      if(data.period_between && data.minimum_nights){
        var factor = countDaysBetween(dates[0],dates[1]);
      }
      jQuery('#days_factor').val(factor);
    }//end setPriceForDaysFactor


    ////////////////////////////////////////////////////////////
    // Po kliknieciu w oferte wypelnia danymi selecta z pokojami
    ////////////////////////////////////////////////////////////
    function fillRoomsSelect(rooms){
      jQuery('#pokoj option').remove();

      jQuery('#pokoj').append(jQuery('<option value="0" >---- wybierz ----</option>') );

      for(i in rooms){
        var option = jQuery('<option>')
        .attr({
          'value': rooms[i],
          'title': i+' '+rooms[i]+' zł'
        })
        .html(i+' '+rooms[i]+' zł');
        jQuery('#pokoj').append(option);
      }
    }// end fillRoomsSelect


    ////////////////////////////////////////////////////////////
    // oblicza ilosc dni pomiedzy dwoma datami w formacie dd-mm-yyyy
    ////////////////////////////////////////////////////////////
    function countDaysBetween(date_from,date_to){
      var d1 = date_from.split('-');
      var d2 = date_to.split('-');

      var date1 = new Date(d1[2],d1[1]-1,d1[0]);
      var date2 = new Date(d2[2],d2[1]-1,d2[0]);
      a = Math.round(date1.getTime()/1000/60/60/24) ;
      b = Math.round(date2.getTime()/1000/60/60/24) ;
      return b-a;
    }// end countDaysBetween


    ////////////////////////////////////////////////////////////
    // sprawdza czy obiekt jest pusty czy ma jakies wlasciwosci
    ////////////////////////////////////////////////////////////
    function isEmpty(ob){
      for(var i in ob){ return false;}
      return true;
    }// end isEmpty

    function addTooltip(element, text){
      element.opTooltip({
        'css':{      //required
          'color':'white',
          'background-color':'#FD7B22',
          'font-weight':'bold',
          'border':'1px solid white'
        },
        'text':function(element){   //required
          return text;
        }
      });
    }// end addTooltip

    function printReservation(){

      jQuery('#pokoj option:selected').attr('checked', 'checked');

      var content = jQuery('#email_text').val();
      var w = window.open('','','width=500,height=900');

      content = content.replace('Oto nowa rezerwacja:','<h1>Szczegóły rezerwacji</h1>');
      content = content.replace('----- OFERTA -----','<h2>Wybrana oferta i termin pobytu</h2>');
      content = content.replace('----- OPCJE -----','<h2>Opcje</h2>');
      content = content.replace('----- DANE KONTAKTOWE -----','<h2>Dane kontaktowe</h2>');

      w.document.writeln("<html> <head></head>  <body> <style>"
      + "*{font-family:Trebuchet MS, Verdana, Arial}"
      + "#main{border:1px solid black;padding:10px;float:left;overflow:hidden;font-size:12px;width:450px;clear:both}"

      + "#ble{padding:10px;float:left;overflow:hidden;font-size:12px;width:450px;;clear:both}"
      + "h1{font-size:19px}"
      + "#main h1{margin:0px;border-bottom:1px solid black;font-size:17px;}"
      + "#main h2{margin:0px;border-bottom:1px solid black;font-size:14px;}"
      + "</style>"
      + '<h1>Rezerwacja pobytu w hotelu Activa</h1>'
      + '<div id="main" >'
      +  nl2br(content, false)
      + '<br/><br/><small>Wyslanie formularza powoduje przeslanie informacji do recepcji hotelu. Niezwlocznie po otrzymaniu zgloszenia skontaktujemy sie z Panstwem w celu potwierdzenia rezerwacji.</small>'
      + "</div>"
      + "<br/>"
      + "<br/>"

      + '<div id="ble" >'
      + "<b>Recepcja:</b><br/>"
      + "email: recepcja@hotel-activa.pl<br/>"
      + "tel.: +48 18 471 80 20 (19)<br/>"
      + "fax: +48 18 471 80 18<br/>"
      + "tel. kom.: +48 609 992 588<br/>"
      + '<br/> <br/><button onclick="window.print()" >Drukuj</button>'
      + '<br/><small>Uwaga!<br/>'
      + 'W starszych przeglądarkach drukowanie uruchamia się wciskając jednocześnie [Control]+[P]</small>'

      + "</div>"


      + "<script>"
      + ""
      + "</"+"script></body></html>");
    }

    function nl2br(str, is_xhtml) {
      var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
      return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    }

  });
</script>

<style>
  #date_select_form{padding:0px 10px 10px 10px;display:none;position:absolute;right:35px;top:35px;-moz-border-radius: 4px 4px 4px 4px;-moz-box-shadow: 2px 2px 3px #666666;background-color: #FD7B22;border: 1px solid white;}

  .error_info{color:white !important;background-color:red !important;line-height:17px;}

  #home #home_main_1 #c {padding:15px}

  #lista_ofert{width:432px;float:left;border:1px solid silver;position:relative;padding:1px 1px 0 1px}

  #reservation {width:330px;position:relative;}
  #reservation h2{line-height:25px;}
  #reservation h3{margin-top:5px;margin-bottom:1px;}
  #reservation td{line-height:15px;padding:3px 0 3px 0}

  #form_table {background-color:red;border-collapse:collapse;width:325px;margin-right:5px;}
  #form_table tr {padding-top:15px}
  #form_table tr.options td{}
  #form_table td {padding:3px 0 3px 0;background-color: white;}

  #form_table .h3 {font-size:14px;font-weight:bold}
  #form_table #date_from {font-weight:bold}
  #form_table #date_to {font-weight:bold}

  #form_table #date_from.error,
  #form_table #date_to.error {background-color:red !important;color:white !important;padding-left:3px;padding-right:3px}

  #form_table .change_dates{cursor:pointer;font-size:10px;border:1px solid #3F4852;display:inline-block;padding:1px 5px 1px 4px;-moz-border-radius:5px;line-height:11px;margin-left:74px}
  #form_table .change_dates:hover{background-color:#FF7B29;color:white;text-decoration:underline}

  #payment_table{width:305px;margin:15px 5px 20px 0px;border:1px solid #FF7B21;overflow:hidden}
  #payment_table h3{margin-top:1px;}
  #payment_table td{padding-left:5px;}
  #payment_table #total_payment{float:right;width:40%;font-size:16px;color:#FF7B21;font-weight:bold;text-align:right;padding-right:5px}

  #personal_table{width:325px;margin-right:5px}


  div.offer_separator{background-color: #000;color:white;margin:0;text-align:center;padding-bottom:1px;margin-bottom:1px;clear:both;overflow:hidden;font-weight:bold}

  div.offer_holder{margin:0px;background-color:#f0f0f0;overflow:hidden !important;margin-bottom:1px;padding:1px;padding-bottom:4px;font-weight:bold}
  div.offer_holder.active{background-color:white}
  div.offer_holder div.h3 {cursor:pointer;width:280px;margin:0;width:267px;font-size:14px;font-weight:bold;background:transparent;color:black;padding-bottom:5px}
  div.offer_holder.active .h3{color:#FF7B29 !important;}
  div.offer_holder div.p{margin:0px;width:280px;background:transparent;cursor:pointer;}

  div.offer_holder div.periods_holder{border:1px solid silver;padding:1px;background-color:white;width:139px;float:right;}

  div.period{font-size:11px;cursor:pointer;padding-left:2px;color:black;margin-top:1px;font-weight:normal}
  div.period:hover{background-color: yellow;}
  div.period.active_period{background-color:#FF7B29;color:white;}
  div.period.error{color:red;background-color:white}
</style>

<div id="reservation" style="float:left" style="" >
  <input id="days_factor" name="days_factor" type="hidden" />
  <table id="form_table" style="" cellpadding="0" cellspacing="0">
    <tbody>
      <tr>
        <td colspan="2">
          <?php if(isset($reservation_result)){ echo $reservation_result; } ?>
          <h2>Szczegóły rezerwacji</h2>
        </td>
      </tr>
      <tr>
        <td style="width:40%" >Wybrana oferta:</td>
        <td style="width:60%"  ><b id="offer_title"  ></b></td>
      </tr>
      <tr>
        <td style="width:40%" >Od dnia:</td>
        <td style="width:60%"   ><span id="date_from" ></span>&nbsp;<span class="change_dates" title="zmień termin" >zmień</span></td>
      </tr>
      <tr>
        <td>Do dnia:</td>
        <td><span id="date_to" ></span>&nbsp;<span class="change_dates" title="zmień termin">zmień</span></td>
      </tr>
      <tr>
        <td>Noclegów:</td>
        <td id="nights" ></td>
      </tr>
      <tr>
        <td>Pokój: <span style="color:red" >*</span></td>
        <td><select id="pokoj" name="pokoj" style="width:180px" ></select></td>
      </tr>
    </tbody>
  </table>

  <table id="payment_table" style=""  cellpadding="0" cellspacing="0">
    <tbody>
      <tr>
        <td><h3>Do zapłaty</h3></td>
        <td style="" id="total_payment" ></td>
      </tr>
    </tbody>
  </table>

  <table id="personal_table" style="" cellpadding="0" cellspacing="0">
    <tbody>
      <tr>
        <td colspan="2"><h2>Dane kontaktowe</h2></td>
      </tr>
      <tr>
        <td style="width:40%" >Imie i nazwisko: <span style="color:red" >*</span></td>
        <td style="width:60%" ><input type="text" id="name" name="name" style="width:180px" /></td>
      </tr>
      <tr>
        <td>Nazwa firmy:</td>
        <td><input type="text" id="company" name="company" style="width:180px" /></td>
      </tr>
      <tr>
        <td>E-mail:</td>
        <td><input type="text" id="email" name="email" style="width:180px" /></td>
      </tr>
      <tr>
        <td>Telefon: <span style="color:red" >*</span></td>
        <td><input type="text" id="phone" name="phone" style="width:180px" /></td>
      </tr>
      <tr>
        <td>Uwagi:</td>
        <td><textarea type="text" id="remarks" name="remarks" style="width:180px;height:100px;min-height:100px;max-width:180px;min-width:180px;" ></textarea></td>
      </tr>
      <tr>
        <td colspan="2" style="color:silver;font-size:9px;line-height:10px;padding-top:5px;" >
          Wyslanie formularza powoduje przeslanie informacji do recepcji hotelu.
          Niezwlocznie po otrzymaniu zgloszenia skontaktujemy sie z Panstwem w celu potwierdzenia rezerwacji.
        </td>
      </tr>
      <tr>
        <td colspan="2" style="" ><span style="color:red" >*</span> oznacza pola wymagane</td>
      </tr>
      <tr>
        <td>
          <?php if($KAMELEON_MODE == 0 ){ $url = 'http://www.hotel-activa.pl/rezerwacja.php';} ?>
          <?php if($KAMELEON_MODE == 1 ){ $url = 'http://kameleon01.fakro.pl/index.php?page=99'; } ?>
          <form id="reservation_form" action="<?php echo $url ?>" method="post" lang="<?php echo $url ?>" >
            <input type="submit" id="send_reservation" value="Rezerwuję" />
            <input type="hidden" id="email_text" name="email_text"  />
          </form>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<div id="lista_ofert"></div>
