<?
  //  $projdb->debug=1;

  global $bon_wysylka_maila;
  $user_action = '';

  if(isset($_POST['user_action'])) $user_action = $_POST['user_action'];
  elseif(isset($_GET['user_action'])) $user_action = $_GET['user_action'];

  //  echo '<pre>';
  //  print_r($_GET);
  //  print_r($_POST);
  //  echo '</pre>';

  if(!$user_action['id']) return;

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

  // sprawdzenie czy user ma jakies przypisane punkty
  $sql3 = "SELECT * FROM voucher_produkt_user WHERE su_id=".$user_action['id'];
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
  
  $sql = "SELECT * FROM system_user, voucher, voucher_type WHERE system_user.su_id=voucher.su_id AND voucher.voucher_type_id = voucher_type.voucher_type_id AND voucher.su_id=".$user_action['id'];
  
  parse_str(ado_query2url($sql));

  $osoby_fields_imiona = sysmsg("Forename","user");
  $osoby_fields_nazwisko = sysmsg("Surname","user");
  $osoby_fields_email = sysmsg("Email","user");

  $osoby_fields_ulica = sysmsg("Street","user");
  $osoby_fields_numer_domu = sysmsg("Numer domu","user");
  $osoby_fields_numer_mieszkania = sysmsg("Numer mieszkania","user");
  $osoby_fields_kod_pocztowy = sysmsg("Zip code","user");
  $osoby_fields_miasto = sysmsg("City","user");

  $osoby_fields_telefon = sysmsg("Telephone","user");

  $su_ulica_value = '';
  $su_ulica_data = '';
  $su_ulica_dom_value = '';
  $su_ulica_mieszkanie_value = '';

  list($su_ulica_value, $su_ulica_data) = split('&nbsp;', $su_ulica);

  if(isset($su_ulica_data)) {
    list($su_ulica_dom_value, $su_ulica_mieszkanie_value) = split('/', $su_ulica_data);

    if(!isset($su_ulica_mieszkanie_value)) {
      $su_ulica_mieszkanie_value = '&nbsp;';
    }
  }

  //  lista produktow w promocji
  $sql1 = "SELECT * FROM voucher_produkt WHERE voucher_type_id=$voucher_type_id ORDER BY voucher_produkt_wartosc, voucher_produkt_name";
  $res1 = $projdb->Execute($sql1);

  //  lista produktow przypisanych do uzytkownika
  $sql2 = "SELECT * FROM voucher_produkt_user WHERE su_id=".$user_action['id'];
  $res2 = $projdb->Execute($sql2);  

  $voucher_produkt_user = array();
  for($i=0; $i < $res2->RecordCount(); $i++) {
    parse_str(ado_explodename($res2,$i));
    $voucher_produkt_user[$voucher_produkt_id] = $voucher_produkt_ilosc;
  }

?>

<div class="dane">
  <TABLE class="list_table">
    <col align="right" width="150">
    <col class="cd">
    <TBODY>  
      <TR>
        <Th colspan=2><? echo sysmsg('Dane o promocji','bony'); ?></th>
      </tr>
      <TR>
        <TD><? echo sysmsg('promocja','bony'); ?>:</TD>
        <TD><? echo $voucher_type_name; ?></TD>
      </TR>
      <TR>
        <Th colspan=2><? echo sysmsg('Dane użytkownika','bony'); ?></th>
      </tr>
      <TR>
        <TD><? echo $osoby_fields_imiona; ?> *:</TD>
        <TD><? echo $su_imiona; ?></TD>
      </TR>
      <TR>
        <TD><? echo $osoby_fields_nazwisko; ?> *:</TD>
        <TD><? echo $su_nazwisko; ?></TD>
      </TR>
      <TR>
        <TD><? echo $osoby_fields_email; ?>:</TD>
        <TD><? echo $su_email; ?></TD>
      </TR>
      <TR>
        <TD><? echo $osoby_fields_ulica; ?> *:</TD>
        <TD><? echo $su_ulica_value; ?></TD>
      </TR>
      <TR>
        <TD>Numer domu *:</TD>
        <TD><? echo $su_ulica_dom_value; ?></TD>
      </TR>
      <TR>
        <TD>Numer mieszkania:</TD>
        <TD><? echo $su_ulica_mieszkanie_value; ?></TD>
      </TR>
      <TR>
        <TD><? echo $osoby_fields_kod_pocztowy; ?> *:</TD>
        <TD><? echo $su_kod_pocztowy; ?></TD>
      </TR>
      <TR>
        <TD><? echo $osoby_fields_miasto; ?> *:</TD>
        <TD><? echo $su_miasto; ?></TD>
      </TR>
      <TR>
        <TD><? echo $osoby_fields_telefon; ?>:</TD>
        <TD><? echo $su_telefon; ?></TD>
      </TR>
    </TBODY>  
  </TABLE>

  <?
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
      <FORM METHOD=POST ACTION="<?php echo $bon_wysylka_maila; ?>" name="user_action[bon_wysylka_maila]">
        <INPUT TYPE="hidden" name="user_action[id]" value="<? echo $user_action['id']; ?>">
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
</div>
