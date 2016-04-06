
<script type="text/javascript">
  jQuery(function(){
    $(".tabContent").hide();
    $("input.selectTab").click(function() {
      $(".tabContent").hide("slow");
      $('#tab_' + $(this).val()).show("slow");

    });

    $('form').submit(function() {
      var $form = $( this );
      nazwa_nr  = $form.find( 'input[name="nazwa_nr"]' ).val();
      nazwa_nr2 = $form.find( 'input[name="nazwa_nr2"]' ).val();

      if(nazwa_nr != null) {
        if(nazwa_nr.length == 0) {
          alert('Nalezy podac pierwszą czesc numeru tabliczki znamionowej!');
          return false;
        }
      }

      if(nazwa_nr2 != null) {
        if(nazwa_nr2.length != 4) {
          alert('Nalezy podac druga czesc numeru tabliczki znamionowej!');
          return false;
        }

        // test czy sa 4 znaki numeryczne
        var re = /\d{4}/g;
        // var re = /\d\d[1-9]\d/g;
        if(!nazwa_nr2.match(re)) {
          alert('Druga czesc numeru tabliczki znamionowej moze zawierać tylko cyfry!');
          return false;
        }

        var tydzien = nazwa_nr2.substring(2, 4);
        tydzien = tydzien.substring(1, 2) + "" + tydzien.substring(0, 1);
        tydzien = new Number(tydzien);

        if((parseInt(tydzien) >= 1) && (parseInt(tydzien) <= 52)) {
          //OK
        }else{
          alert('Bledna wartosc w drugiej czesci numeru tabliczki znamionowej!');
          return false;
        }
      }

      return true;
    })
  })
</script>

<div align="center">

  <table id="codes_pic_table">
    <tbody>
      <tr>
        <td><input type="radio" class="selectTab" name="tab" value="1"></td>
        <td><img src="<?=$_path?>image/t1.png" border="0" width="340" height="70" alt=""></td>
      </tr>
      <tr>
        <td></td>
        <td>
          <div id="tab_1" class="tabContent" style="display:none;">
            <form action="<?=$_action;?>" method="post" name="tab1">
              <input type="hidden" name="mode" value="results">
              numer identyfikacyjny produktu:<br>
              <input type="text" name="nazwa_nr" value="" size="5">
              <input type="text" name="nazwa_nr2" value="" size="4" maxlength="4">
              <input type="text" name="nazwa_nr3" value="" size="2" maxlength="2">
              <input type="text" name="nazwa_nr4" value="" size="4" maxlength="4">
              <br>
              <input class="button" type="submit" value="szukaj">
            </form>
          </div>
        </td>
      </tr>
      <tr>
        <td><input type="radio" class="selectTab" name="tab" value="2"></td>
        <td><img src="<?=$_path?>image/t2.png" border="0" width="340" height="70" alt=""></td>
      </tr>
      <tr>
        <td></td>
        <td>
          <div id="tab_2" class="tabContent" style="display:none;">
            <form action="<?=$_action;?>" method="post" name="tab2">
              <input type="hidden" name="mode" value="results">
              <input type="hidden" name="nazwa_nr3" value="">
              <input type="hidden" name="nazwa_nr4" value="">
              numer identyfikacyjny produktu:<br>
              <input type="text" name="nazwa_nr" value="" size="5">
              <input type="text" name="nazwa_nr2" value="" size="4" maxlength="4">
              <br>
              <input class="button" type="submit" value="szukaj">
            </form>
          </div>
        </td>
      </tr>
      <tr>
        <td><input type="radio" class="selectTab" name="tab" value="3"></td>
        <td><img src="<?=$_path?>image/t3.png" border="0" width="340" height="70" alt=""></td>
      </tr>
      <tr>
        <td></td>
        <td>
          <div id="tab_3" class="tabContent" style="display:none;">
            <form action="<?=$_action;?>" method="post" name="tab3">
              <input type="hidden" name="mode" value="results">
              numer identyfikacyjny produktu:<br>
              <input type="text" name="nazwa_nr" value="" size="5">
              <input type="text" name="nazwa_nr2" value="" size="4" maxlength="4">
              <?php /* <input type="text" name="nazwa_nr3" value="" size="2" maxlength="2"> */ ?>
              <?php /* <input type="text" name="nazwa_nr4" value="" size="4" maxlength="4"> */ ?>
              <br>
              <input class="button" type="submit" value="szukaj">
            </form>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2"><br></td>
      </tr>
      <? if(is_array($_SESSION['bp_form_data']) && (count($_SESSION['bp_form_data']) > 0)) { ?>
        <tr>
          <td align="right" colspan="2">
            <form action="<?=$_action;?>" method="post">
              <input type="hidden" name="mode" value="details">
              <input type="hidden" name="user_action" value="summary">
              <input class="button" type="submit" value="Przejdź do podsumowania">
            </form>
          </td>
        </tr>
        <? } ?>
    </tbody>
  </table>

</div>

<script type="text/javascript">
  document.getElementById('nazwa_nr').focus();
</script>