<?php
  //if($_SERVER['REMOTE_ADDR'] != '37.128.111.165') return;
?>

<?php
  $tab = unserialize(stripslashes($costxt));
  $bony_info = $tab["BONY_ADD_KOSZYK"];
  $voucher_type_id = $bony_info['promocja'];

  if($voucher_type_id < 1) continue;

  $file_promocja = $INCLUDE_PATH.'/sklep/admin/bony/podglad_promocja_'.$voucher_type_id.'.php';

  if(!file_exists($file_promocja)) continue;
  include($file_promocja);
?>

<style type="text/css">
  .voucher { overflow:hidden; display: block; height: 45px; }
  .voucher form.add { background:none repeat scroll 0 0 #EBEBEB; overflow: hidden; padding:5px; margin:10px 0; height: 45px; }
  .voucher form.add p { float:right; margin:0; }
  .voucher form.add p * { vertical-align:middle; }

  .msg-error { background-color:#F1F2F4; border:1px solid #DA0F00; }
  .msg-error { color:#DA0F00; margin:10px 0; overflow:hidden; padding:5px 12px; }
  
  .msg-ok { background-color:#F1F2F4; border:1px solid #008000; }
  .msg-ok { color:#008000; margin:10px 0; overflow:hidden; padding:5px 12px; }
</style>

<?php
  if($voucher_error['999']==1) {
  ?>
  <div class="msg-error">
    <? echo $bony_info['user_no_login_error']; ?>
  </div>
  <?php
  }
  if($voucher_error['997']==1) {
  ?>
  <div class="msg-error">
    <?php echo $bony_info['add_error']; ?>
  </div>
  <?php
  }
  if($voucher_error['996']==1) {
  ?>
  <div class="msg-ok">
    <a href="<?php echo $next; ?>"><?php echo $bony_info['add_cart']; ?></a>
  </div>
  <?php
  }
?>

<div class="voucher">
  <FORM METHOD='POST' class="add" ACTION="<? echo $self; ?>" name="user_action[voucher]">
    <INPUT TYPE="hidden" name="user_action[action]" value="add_voucher">
    <p>
      <label>Numer Vouchera:
        <input type="text" title="Kod:" value="" name="user_action[voucher_name]">
      </label>
      <input type="submit" class="btn" value="Dodaj" name="submitDiscount">            
    </p>
  </form>  
  </div>
  <br>
