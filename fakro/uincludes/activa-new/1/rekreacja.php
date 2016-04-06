<style>
  .roll_links li{cursor:pointer;text-decoration:underline}

  #s93658 table{margin:auto}

  #s119540.roll{display:none}
  #s110450.roll{display:none}
  #s119540.roll{display:none}


</style>
<script>
  jQuery(function(){


    if( !jQuery('#c').children('a').length == 0  ) return;


    var elements = jQuery('#s110447, #s110450, #s119540');

    elements.addClass('roll');

    jQuery('.roll_links li').click(function(){
      var id = jQuery(this).attr('lang')
      operate(id)
    })

    /*elements.prev().children('h2')
    .css('cursor','pointer')
    .click(function(){
      var id = jQuery(this).parent().next().attr('id');
      operate(id)
    });          */

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