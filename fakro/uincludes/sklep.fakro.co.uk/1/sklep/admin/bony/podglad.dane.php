<?
  //$projdb->debug=1;

  global $bon_wysylka_maila;
  $user_action = '';

  if(isset($_POST['user_action'])) $user_action = $_POST['user_action'];
  elseif(isset($_GET['user_action'])) $user_action = $_GET['user_action'];

  /*
  echo '<pre>';
  print_r($_GET);
  print_r($_POST);
  print_r($SKLEP_ID);
  echo '</pre>';
  */

  if(!$user_action['voucher_id']) return;
  if(!$user_action['id']) return;
  
  $sql = "SELECT * FROM system_user, voucher, voucher_type WHERE system_user.su_id=voucher.su_id AND voucher.voucher_type_id = voucher_type.voucher_type_id AND voucher.voucher_id='".$user_action['voucher_id']."' AND voucher.su_id=".$user_action['id'];
  
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

if($voucher_type_id AND $voucher_type_id == 1) {
include("$SKLEP_INCLUDE_PATH/admin/bony/podglad.dane.v1.php");
}

if($voucher_type_id AND $voucher_type_id == 2) {
include("$SKLEP_INCLUDE_PATH/admin/bony/podglad.dane.v2.php");
}

?>
</div>
