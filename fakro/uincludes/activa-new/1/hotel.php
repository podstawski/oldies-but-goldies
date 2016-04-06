<style>
  .roll_links li{cursor:pointer;text-decoration:underline}

  #fake1111.roll{display:none}
  #fake2222.roll{display:none}
  #fake3333.roll{display:none}
  #fake4444.roll{display:none}
  #fake5555.roll{display:none}


</style>
<script>
  jQuery(function(){


      if( !jQuery('#c').children('a').length == 0  ) return;

      //grupujemy DIV-y
      jQuery('#s106651,#s106652,#s106653,#s106654,#s106655,#s106656').wrapAll('<div id="fake1111" >');
      jQuery('#s110462,#s110461').wrapAll('<div id="fake2222" >');
      jQuery('#s110464,#s110463').wrapAll('<div id="fake3333" >');
      jQuery('#s110465,#s110466').wrapAll('<div id="fake4444" >');
      jQuery('#s110467,#s110468').wrapAll('<div id="fake5555" >');

      var elements = jQuery('#fake1111,#fake2222,#fake3333,#fake4444,#fake5555');

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
          //jQuery('#'+id).slideUp();
        }
      }







  });
</script>