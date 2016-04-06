<?
  //$projdb->debug=1;

  global $bon_wysylka_maila_1;
  
  //  dodanie produktu do usera
  if($user_action['action'] == 'select_product') {
    $query = "DELETE FROM voucher_produkt_user WHERE voucher_id=".$user_action['voucher_id']." AND su_id=".$user_action['id'];
    $projdb->Execute($query);    

    foreach($user_action['product'] as $key => $value) {

      if($value > 0) {
        $query = "INSERT INTO voucher_produkt_user (voucher_id,su_id,voucher_produkt_id,voucher_produkt_ilosc) VALUES (".$user_action['voucher_id'].",".$user_action['id'].",$key,$value);";
        $projdb->Execute($query);
      }
    }
  }

  // sprawdzenie czy user ma jakies przypisane produkty
  $sql3 = "SELECT * FROM voucher_produkt_user WHERE voucher_id='".$user_action['voucher_id']."' AND su_id='".$user_action['id']."';";
  $res3 = $projdb->Execute($sql3);
  $voucher_punkty = $res3->RecordCount();

  //  dodanie wartosci do usera
  if($user_action['action'] == 'wartosc') {
    if($user_action['wartosc']['ile'] >= 50 AND $user_action['wartosc']['ile'] <= 600) {

      if($voucher_punkty>0) {
        $query = "UPDATE voucher SET voucher_wartosc='".$user_action['wartosc']['ile']."', voucher_date_modyfikacji='".time()."', voucher_status='2' WHERE voucher_id='".$user_action['voucher_id']."' AND su_id='".$user_action['id']."';";
        $projdb->Execute($query);
      }
    }
  }
  
  //  lista produktow w promocji
  $sql1 = "SELECT * FROM voucher_produkt WHERE voucher_type_id=$voucher_type_id ORDER BY voucher_produkt_wartosc, voucher_produkt_name";
  $res1 = $projdb->Execute($sql1);

  //  lista produktow przypisanych do uzytkownika
  $sql2 = "SELECT * FROM voucher_produkt_user WHERE voucher_id='".$user_action['voucher_id']."' AND su_id='".$user_action['id']."';";
  $res2 = $projdb->Execute($sql2);  

  $voucher_produkt_user = array();
  for($i=0; $i < $res2->RecordCount(); $i++) {
    parse_str(ado_explodename($res2,$i));
    $voucher_produkt_user[$voucher_produkt_id] = $voucher_produkt_ilosc;
  }




  if($voucher_wartosc AND ($voucher_status>1)) {
    ?>
    <div style="text-align: center; margin-top: 5px; margin-bottom: 5px;">
      <h2>
      Wartosc vouchera: <? echo $voucher_wartosc; ?> PLN<br>
      Kod vouchera: <? echo $voucher_name; ?><br>
      Waznosc vouchera: <? echo date("Y-m-d", ($voucher_date_modyfikacji+(86400*365))); ?>
      </h2>
    </div>

    <div style="text-align: center; margin-top: 5px; margin-bottom: 5px; <?php echo $bon_input_hidden ?>">
      <FORM METHOD=POST ACTION="<? echo $bon_wysylka_maila_1; ?>" name="user_action[bon_wysylka_maila]">
        <INPUT TYPE="hidden" name="user_action[id]" value="<? echo $user_action['id']; ?>">
		<INPUT TYPE="hidden" name="user_action[voucher_id]" value="<? echo $user_action['voucher_id']; ?>">
        <INPUT TYPE="submit" NAME="" VALUE="<? echo sysmsg('Wysylka maila z informacja o przyznaniu vouchera','bony'); ?>" class="button" style="width:300px;">
      </FORM>
    </div>
    <?
    }elseif($voucher_punkty>0) {
    ?>
    <br><br>
    <FORM METHOD='POST' ACTION="<? echo $self; ?>" name="user_action[wartosc]">
      <INPUT TYPE="hidden" name="user_action[action]" value="wartosc">
      <INPUT TYPE="hidden" name="user_action[id]" value="<? echo $user_action['id']; ?>">
      <INPUT TYPE="hidden" name="user_action[voucher_id]" value="<? echo $voucher_id; ?>">
      <div style="text-align: center; margin-top: 5px; margin-bottom: 5px;">
        Wartosc <input type="text" size="5" maxlength="5" value="" name="user_action[wartosc][ile]"><br>
        <INPUT TYPE="submit" NAME="" VALUE="<? echo sysmsg('Dodaj','bony'); ?>" class="button" style="width:250px;" onclick="return confirm('Jesteś pewien wykonaia tej operacji? Po zatwierdzeniu zostanie wyslany email od klienta!');">
      </div>
    </FORM>
    <?
    }
  ?>

  <br><br>
  <FORM METHOD='POST' ACTION="<? echo $self; ?>" name="user_action[product]">
    <INPUT TYPE="hidden" name="user_action[action]" value="select_product">
    <INPUT TYPE="hidden" name="user_action[id]" value="<? echo $user_action['id']; ?>">
    <INPUT TYPE="hidden" name="user_action[voucher_id]" value="<? echo $voucher_id; ?>">
    <table id="wydruk" class="list_table">
      <col>
      <col width="100">
      <col width="100">
      <TR>
        <th class="name" valign="top">Produkt</th>
        <th class="name" valign="top" align="center">Wartosc</th>
        <th class="name" valign="top"></th>
      </TR>
      <?
        if(!is_object($res1)) {
          echo sysmsg('Invalid characters where used when searching','admin')." !!!<br>";
          return;
        }

        if (!$res1->RecordCount()) return;

        for($i=0; $i < $res1->RecordCount(); $i++) {
          parse_str(ado_explodename($res1,$i));

          if($voucher_wartosc AND ($voucher_status>1)) {
            if($voucher_produkt_user[$voucher_produkt_id]) {
              echo '<TR class="'.(($i && ($i%2))?'even':'odd').'">';
              echo '<TD>'.$voucher_produkt_name.'</TD>';
              echo '<TD align="center">'.$voucher_produkt_wartosc.'</TD>';
              echo '<TD>'.$voucher_produkt_user[$voucher_produkt_id].'</TD>';
              echo '</TR>';
            }
          }else{
            echo '<TR class="'.(($i && ($i%2))?'even':'odd').'">';
            echo '<TD>'.$voucher_produkt_name.'</TD>';
            echo '<TD align="center">'.$voucher_produkt_wartosc.'</TD>';
            echo '<TD><input type="text" size="5" maxlength="2" value="'.$voucher_produkt_user[$voucher_produkt_id].'" name="user_action[product]['.$voucher_produkt_id.']"></TD>';
            echo '</TR>';
          }
        }
      ?>
    </table>
    <? if(!$voucher_wartosc AND ($voucher_status==1)) {
      ?>
      <div style="text-align: center; margin-top: 5px; margin-bottom: 5px;">
        <INPUT TYPE="submit" NAME="" VALUE="<? echo sysmsg('Dodaj produkty','bony'); ?>" class="button" style="width:250px;">
      </div>
      <?
      }
    ?>
  </FORM>  
