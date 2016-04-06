<style>
  .roll_links li{cursor:pointer;text-decoration:underline}

  #s93658 table{margin:auto}

  #s110433.roll{display:none}
  #s78433.roll{display:none}
  #s110438.roll{display:none}
  #s110439.roll{display:none}

</style>
<script>
  jQuery(function(){
  
      if( !jQuery('#c').children('a').length == 0  ) return;


    var elements = jQuery('#s110433, #s78433, #s110438, #s110439');

    elements.addClass('roll');

    jQuery('.roll_links li').click(function(){
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