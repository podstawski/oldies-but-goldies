<?
  if ($AUTH['id']<=0) {
    $error="użytkownik bez autoryzacji";
    return;
  }

  $sg = "";

  parse_str($costxt);

  $SKLEP_SESSION['sg'] = $sg;
  session_register('sg');

  $ch = explode(':',$sg);
  
  $ch_view = strstr($sg,":view:")?true:false;
  $ch_generate = strstr($sg,":generate:")?true:false;
  $ch_print = strstr($sg,":print:")?true:false;
  $voucher_type_id = $promocja;
  
  if($voucher_type_id < 1) continue;

  $goto = $_REQUEST['goto'];

  $sql = "SELECT su_id, su_parent, su_login, su_imiona, su_nazwisko, su_email, su_gsm FROM system_user WHERE su_id = ".$AUTH['id'];
  parse_str(ado_query2url($sql));

  $sql = "SELECT su_pesel, su_ulica, su_kod_pocztowy, su_miasto, su_telefon, su_nip, su_adres1, su_adres2, su_adres3, su_wyroznik1, su_wyroznik2, su_wyroznik3, su_nazwisko AS nazwa FROM system_user WHERE su_id = ".$su_parent;
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

  // drukowanie 
  if(in_array('print',$ch)) {
    $bon_input_hidden = 'display:none';
    $bon_print = '<SCRIPT LANGUAGE="JavaScript">window.print();</script>';
  }

  // generowanie 
  if(in_array('generate',$ch)) {
    $sql = "SELECT * FROM voucher WHERE voucher_type_id = '".$voucher_type_id."' AND su_id = ".$AUTH['id'];
    $res = $adodb->execute($sql);

    if(!$res->RecordCount()) {
      $md5 = md5(time().$su_id.$su_imiona.$su_nazwisko);
      $array = 1;
      $array_id = 1;
      $id = 0;
      $re = array();
      for($i = 0; $i <= strlen($md5); $i++) {
        if($id > 0) {
          $re[$array][$array_id] = strtoupper($md5{$i});
          $id = 0;
          $array_id++;

          if($array_id > 4) {
            $array++;
            $array_id = 1;
          }

        }else{
          $id++;
        }
      }

      $code_autoryzacji = $re[4][1].$re[4][2].$re[4][3].$re[4][4];
      $code_autoryzacji .= '-'.$re[1][1].$re[1][2].$re[1][3].$re[1][4];
      $code_autoryzacji .= '-'.$re[3][1].$re[3][2].$re[3][3].$re[3][4];
      $code_autoryzacji .= '-'.$re[2][1].$re[2][2].$re[2][3].$re[2][4];

      $voucher_name =  $code_autoryzacji;
      $voucher_date_dodania = time();
      $voucher_status = 1;

      $query = "INSERT INTO voucher (su_id,voucher_name,voucher_date_dodania,voucher_status,voucher_type_id) VALUES ($su_id,'$voucher_name',$voucher_date_dodania,$voucher_status,$voucher_type_id);";
      $projdb->Execute($query);
    }else{
      parse_str(ado_query2url($sql));
    }
  }
?>
