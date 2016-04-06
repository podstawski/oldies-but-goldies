<?php
if(isset($_POST['user_action'])) $user_action = $_POST['user_action'];
elseif(isset($_GET['user_action'])) $user_action = $_GET['user_action'];

// sprawdzenie czy uzytkownik jest zalogowany
if($AUTH['id']>0) {
  
  $ko_opcje_su_id = $AUTH['id'];
  
  if($user_action['action'] == 'add_voucher') {
    // dodac sprawdzanie daty: voucher_date_modyfikacji
    $sql1 = "SELECT * FROM voucher WHERE su_id=".$AUTH['id']." AND voucher_type_id = ".$voucher_type_id." AND voucher_status=2 AND voucher_koszyk_id is null AND voucher_name='".$user_action['voucher_name']."'";
    parse_str(ado_query2url($sql1));

    if(!isset($voucher_id)) {
      $voucher_error['997'] = 1;
    }else{

      $sql2 = "SELECT * FROM voucher_produkt_user WHERE su_id=".$AUTH['id']." AND voucher_id = '".$voucher_id."' ";
      $res2 = $adodb->execute($sql2);

      for($i2=0; $i2 < $res2->RecordCount(); $i2++) {
        parse_str(ado_explodename($res2,$i2));
        
        if(!strlen($voucher_produkt_ean)) return;
        
        $sql3 = "SELECT * FROM towar_sklep WHERE ts_to_id = $voucher_produkt_ean AND ts_sk_id = $SKLEP_ID ";
        parse_str(ado_query2url($sql3));
        if(!strlen($ts_id)) return;
        
        if(!$ts_czas_koszyk) {
          $deadline = $NOW+$WM->towar_czas_zycia($towar_id);
        }else{
          $deadline = $NOW+$ts_czas_koszyk;
        }
        if(!$SYSTEM['czas']) $deadline="NULL";
        
        $sql4 = "SELECT ko_id AS jest, ko_ilosc FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_ts_id = $ts_id";
        parse_str(ado_query2url($sql4));
        
        if(!$jest) {
          $sql5 = "INSERT INTO koszyk (ko_su_id,ko_ts_id,ko_ilosc, ko_deadline, ko_rez_uwagi, ko_opcje) VALUES (".$AUTH['id'].",$ts_id,$voucher_produkt_ilosc,$deadline,$voucher_id,".$AUTH['id'].")";
          $adodb->execute($sql5);
        }

      }

      $sql6 = "SELECT ko_opcje FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_opcje = '".$ko_opcje_su_id."' AND ko_rez_data IS NULL GROUP BY ko_opcje";
      parse_str(ado_query2url($sql6));
      
      if(isset($ko_opcje)) {
        $query = "UPDATE voucher SET voucher_koszyk_id='".$ko_opcje."' WHERE voucher_id='".$voucher_id."' AND su_id='".$AUTH['id']."';";
        $projdb->Execute($query);
        
        $voucher_error['996'] = 1;
      }
      
      
      

    }
    

    

//    $sql2 = "SELECT ko_id FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_rez_data IS NULL ORDER BY ko_id";
//    $res2 = $adodb->execute($sql2);

//    for($i1=0; $i1 < $res2->RecordCount(); $i1++) {
//      parse_str(ado_explodename($res2,$i1));
//      $query = "UPDATE koszyk SET ko_opcje='".$ko_opcje_su_id."' WHERE ko_id='".$ko_id."' ;";
//      $projdb->Execute($query);
//    }

//    $sql3 = "SELECT ko_opcje FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_opcje = '".$ko_opcje_su_id."' AND ko_rez_data IS NULL GROUP BY ko_opcje";
//    parse_str(ado_query2url($sql3));


//    if(isset($ko_opcje) AND isset($voucher_id)) {
//      $query = "UPDATE voucher SET voucher_koszyk_id='".$ko_opcje."' WHERE voucher_id='".$voucher_id."' AND su_id='".$AUTH['id']."';";
//      $projdb->Execute($query);
//    }

//    if(!isset($voucher_id)) {
//      $voucher_error['997'] = 1;
//    }
  }

}else{
  if($user_action['action'] == 'add_voucher') {
    $voucher_error['999'] = 1;
  }
}

?>