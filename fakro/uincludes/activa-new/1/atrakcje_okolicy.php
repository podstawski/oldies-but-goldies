<style>
  .roll_links li{cursor:pointer;text-decoration:underline}

  #s78450.roll{display:none}
  #s110454.roll{display:none}
  #s110455.roll{display:none}
  #s110456.roll{display:none}
  #s110457.roll{display:none}
  #s110458.roll{display:none}
  #s110459.roll{display:none}

  #s119675.roll{display:none}
  #s119676.roll{display:none}
  #s119677.roll{display:none}
  #s119678.roll{display:none}
  #s119679.roll{display:none}
  #s119681.roll{display:none}
  #s119682.roll{display:none}

  #s127858.roll{display:none}
  #s127859.roll{display:none}
  #s127860.roll{display:none}
  #s127861.roll{display:none}
  #s127862.roll{display:none}
  #s127863.roll{display:none}
  #s127864.roll{display:none}
  #s128871.roll{display:none}


</style>
<script>
  jQuery(function(){

    if( !jQuery('#c').children('a').length == 0  ) return;

    var bloczki = '#s78450,#s110454,#s110455,#s110456,#s110457,#s110458,#s110459'
    + ',#s119675,#s119676,#s119677,#s119678,#s119679,#s119681,#s119682'
    + ',#s127858,#s127859,#s127860,#s127861,#s127862,#s127863,#s127864,#s128871' ;



    var elements = jQuery(bloczki)

    elements.addClass('roll');

    jQuery('.roll_links li[lang]').click(function(){

      var id = jQuery(this).attr('lang')
      operate(id)
    })

    elements.prev().children('h2')
    .css('cursor','pointer')
    .click(function(){
      var id = jQuery(this).parent().next().attr('id');
      operate(id)
    });

    function operate(id){
      if(jQuery('#'+id+':hidden').length > 0){
        jQuery('.roll:visible').slideUp();
        jQuery('#'+id).slideDown();
      }else{
        jQuery('#'+id).slideUp();
      }
    }
  });
</script>