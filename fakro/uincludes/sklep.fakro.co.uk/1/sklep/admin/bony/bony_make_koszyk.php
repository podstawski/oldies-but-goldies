<?php
  //if($_SERVER['REMOTE_ADDR'] != '37.128.111.165') return;
?>
<?

  if(isset($_POST['user_action'])) $user_action = $_POST['user_action'];
  elseif(isset($_GET['user_action'])) $user_action = $_GET['user_action'];
  
  // sprawdzenie czy uzytkownik jest zalogowany
  if($AUTH['id']>0) {
    
    $voucher_type_id = 1;
    
    $ko_opcje_su_id = $AUTH['id'];
    
    if($user_action['action'] == 'add_voucher') {
      $sql1 = "SELECT * FROM towar t
      LEFT JOIN towar_kategoria kt ON (t.to_id = kt.tk_to_id)
      LEFT JOIN kategorie k ON (kt.tk_ka_id = k.ka_id)
      LEFT JOIN rabat_ilosciowy ri ON (k.ka_id = ri.ri_ka_id)
      WHERE t.to_indeks='".$user_action['voucher_name']."'
      AND k.ka_nazwa='Coupons'";
      parse_str(ado_query2url($sql1));
      
      $sql2 = "SELECT ko_id FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_rez_data IS NULL ORDER BY ko_id";
      $res2 = $adodb->execute($sql2);
      
      for($i1=0; $i1 < $res2->RecordCount(); $i1++) {
        parse_str(ado_explodename($res2,$i1));
        
        $query = "UPDATE koszyk SET ko_opcje='".$ko_opcje_su_id."', ko_rez_uwagi='".$user_action['voucher_name']."' WHERE ko_id='".$ko_id."' ;";
        $projdb->Execute($query);
      }

      /* niepotrzebne jest tylko 1 bon towarowy
      $sql3 = "SELECT ko_opcje FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_opcje = '".$ko_opcje_su_id."' AND ko_rez_data IS NULL GROUP BY ko_opcje";
      parse_str(ado_query2url($sql3));

      if(isset($ko_opcje) AND isset($voucher_id)) {
        $query = "UPDATE voucher SET voucher_koszyk_id='".$ko_opcje."' WHERE voucher_id='".$voucher_id."' AND su_id='".$AUTH['id']."';";
        $projdb->Execute($query);
      }
      */

      if(!isset($to_id)) {
        $voucher_error['997'] = 1;
      }
    }
    
    $voucher_type_id = '';
    
    if($user_action['action'] == 'BonUsun') {
      $sql1 = "SELECT ko_opcje FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_rez_data IS NULL GROUP BY ko_opcje";
      parse_str(ado_query2url($sql1));
      
      $sql2 = "SELECT ko_id FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_rez_data IS NULL ORDER BY ko_id";
      parse_str(ado_query2url($sql2));
      
      if($ko_opcje && $ko_id) {
        $query = "UPDATE koszyk SET ko_opcje=NULL, ko_rez_uwagi=NULL WHERE ko_id='".$ko_id."' ;";
        $projdb->Execute($query);
      }
      
//      $sql2 = "SELECT * FROM voucher WHERE voucher_koszyk_id='".$ko_opcje."' AND voucher_status=2 AND su_id='".$AUTH['id']."' ";
//      parse_str(ado_query2url($sql2));
//
      if(isset($ko_opcje)) {
//        $query = "UPDATE voucher SET voucher_koszyk_id=NULL WHERE voucher_koszyk_id='".$ko_opcje."' AND voucher_status=2 AND su_id='".$AUTH['id']."';";
//        $projdb->Execute($query);
//        
//        if($voucher_type_id == 1) {
//          $sql5 = "SELECT ko_id FROM koszyk WHERE ko_su_id = ".$AUTH['id']." AND ko_rez_data IS NULL ORDER BY ko_id";
//          $res5 = $adodb->execute($sql5);
//          
//          for($i1=0; $i1 < $res5->RecordCount(); $i1++) {
//            parse_str(ado_explodename($res5,$i1));
//            $query = "UPDATE koszyk SET ko_opcje=NULL, ko_rez_uwagi=NULL WHERE ko_id='".$ko_id."' ;";
//            $projdb->Execute($query);
//          }
//        }
//        
//        if($voucher_type_id == 2) {
//          $sql3 = "SELECT * FROM voucher_produkt_user WHERE su_id=".$AUTH['id']." AND voucher_id = '".$voucher_id."' ";
//          $res3 = $adodb->execute($sql3);
//          
//          for($i3=0; $i3 < $res3->RecordCount(); $i3++) {
//            parse_str(ado_explodename($res3,$i3));
//            
//            $sql4 = "SELECT * FROM towar_sklep WHERE ts_to_id = $voucher_produkt_ean AND ts_sk_id = $SKLEP_ID ";
//            parse_str(ado_query2url($sql4));
//            if(!strlen($ts_id)) return;
//            
//            $query = "DELETE FROM koszyk WHERE ko_opcje='".$AUTH['id']."' AND ko_su_id='".$AUTH['id']."' AND ko_ts_id = '".$ts_id."'; ";
//            $projdb->Execute($query);
//          }
//        }
      }
      
      ?>
      <script language="JavaScript">
      document.location.href ="./<? echo $self; ?>";;
      </script>
      <?
      
    }
  }else{
    if($user_action['action'] == 'add_voucher') {
      $voucher_error['999'] = 1;
    }
  }
  
  $sysmsg_sure = sysmsg("Are You sure, You want to delete this article ?","cart");
?>

<FORM METHOD="POST" ACTION="<? echo $self; ?>" id="deleteBonForm">
  <INPUT TYPE="hidden" name="user_action[action]" value="BonUsun">
</FORM>

<script language="JavaScript">
  var delBonForm = getObject('deleteBonForm');

  var confirmDelete = true;

  function deleteBon() {
    if (confirmDelete) if (!confirm('<? echo $sysmsg_sure; ?>')) return;
    delBonForm.submit();
  }
</script>