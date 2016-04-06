<script type="text/javascript" >
  jQuery(function(){

/*    jQuery('input,select,textarea').each(function(key,row){
      if(jQuery(row).attr('lang')=='required' ){ //dla każdego inputa, selexta, textarea z lang=required w labelu dodaje gwiazdkę
        var gwiazdka =  jQuery('<span>').html('*').css('color','red') ;
        var text = jQuery(row).parent().prev().append(gwiazdka) ;
      }
    })
    */

    jQuery('option').each(function(key,row){ //zamiana value na optionach z numerów na słowa co by sie dało to w mejlu rozczytać
      var option = jQuery(row);
      if( option.val() >= 1)option.val(option.val()+'. '+option.text())
    });

    jQuery('#submit').bind('click',function(){
      jQuery('form .js_error').remove(); //usuwamy stare errory

      var error = function(){
        return jQuery('<span>')
        .addClass('js_error')
        .html(' To pole jest wymagane')
        .css({
          'color':'red',
          'font-weight':'bold'
        })
      }; //konstruktor errora

      //dla każdego inputa, selexta, textarea sprawdzamy value i ewentualnie wywalamy błąd
      jQuery('input[lang="required"], select[lang="required"], textarea[lang="required"], ').each(function(key,row){
        var field = jQuery(row);
        var field_type = field.attr('type');

        if( (field_type == 'text' || field_type == 'textarea') && field.val() == "" ){
          field.parent().append( error) ;
        }
        if(field_type == 'select-one' && (field.val() == 0 || field.val() == '' ) ){
          field.parent().append( error) ;
        }
        if(field_type == 'checkbox' && field.attr('checked') == false){
          field.parent().append( error) ;
        }
      })

      //jeśli są jakies błędy
      if(jQuery('form .js_error').length > 0 ){
        alert('W formularzu wystąpiły błędy');
        return false;
      }else{
        if(!confirm('Czy wszystkie informacje są poprawne??')) return false;

        jQuery('#doswiadczenie').val( jQuery('#doswiadczenie').val()+ ' poszukiwane stanowisko: ' +jQuery('#poszukiwane_stanowisko').val()  )

      }
    })
  })
</script>