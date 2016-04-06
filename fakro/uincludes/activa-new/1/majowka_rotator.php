
<ul id="majowka_rotator" class="d_none"></ul>

<script type="text/javascript"><!--
  jQuery(document).ready(function() {




      jQuery('.majowka_image').parent().hide();
      jQuery('.majowka_image').each(function(key,row){

        var title = jQuery(row).attr('title');

        var img   = jQuery('<img>').attr('src',jQuery(row).attr('src'));
        var link  = jQuery('<a>')
        .attr({
          'href':jQuery('#majowka_link').attr('href'),
          'title': title,
          'target': '_blank'
        })
        .append(img);
        jQuery('#majowka_link').hide();
        var li =  jQuery('<li>').append(link);

        jQuery('#majowka_rotator').append(li);
      })

      jQuery("#majowka_rotator")
      .css({
        'background-image': 'url('+jQuery('#majowka_background').attr('src')+')',
        'border':'1px solid silver'
      })
      .jBanner({
        'height':185,
        'width':738,
        'borderSize':'0',
        'borderStyle':'solid',
        'borderColor':'CED4CA',
        'padding':0,
        'margin':0,
        'margin-bottom':'20px',
        'caption':false,
        'delay':5000,
        'speed':1000
      });

    jQuery('#majowka_rotator').show();

  });// -->

</script>

<?php
  /*
  PRZYKŁADOWE ZDJĘCIA DO BANERKA
  ZDJĘCIA OSADZAMY W BLOCZKU I UKRYWAMY
  JAVASCRIPT KOPIUJE ATRYBUT SRC Z TYCH ZDJĘĆ I WRZUCA DO BANERKA
  DZIEKI TEMU ZAWSZE MAMY ODPOWIEDNI URL ZDJĘCIA PRZED I PO PUBLIKACJI
  <img class="d_none" id="slider_background" alt="" border="0" src="/uimages/75/1/jslider/swimming_pools.jpg" />
  <img class="d_none slide_image" alt="" border="0" src="/uimages/75/1/jslider/slide_sierpniowa_promocja_napis.png" />
  <img class="d_none slide_image" alt="" border="0" src="/uimages/75/1/jslider/slide_do_konca_sierpnia_karnety_napis.png" />
  <img class="d_none slide_image" alt="" border="0" src="/uimages/75/1/jslider/slide_pierwsza_rezerwacja_apartamentu.png" />


  trzelnica łucznicza
  wycieczka na baseny termalne
  degustacja miodów
  kolejka gondolowa na jaworzynę
  turniej na kręgielni
  */
?>

