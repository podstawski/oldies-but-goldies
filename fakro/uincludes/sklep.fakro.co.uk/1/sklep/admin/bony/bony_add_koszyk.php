<?
  //if($_SERVER['REMOTE_ADDR'] != '37.128.111.165') return;
?>

<?
  $tab = unserialize(stripslashes($costxt));
  $bony_info = $tab["BONY_ADD_KOSZYK"];
?>

<style type="text/css">
  .voucher { overflow:hidden; display: block; height: 45px; }
  .voucher form.add { background:none repeat scroll 0 0 #EBEBEB; overflow: hidden; padding:5px; margin:10px 0; height: 45px; }
    .voucher form.add p { float:right; margin:0; }
  .voucher form.add p * { vertical-align:middle; }

  .msg-error { background-color:#F1F2F4; border:1px solid #DA0F00; }
  .msg-error { color:#DA0F00; margin:10px 0; overflow:hidden; padding:5px 12px; }
</style>

<?
  if($voucher_error['999']==1) {
  ?>
  <div class="msg-error">
    <? echo $bony_info['user_no_login_error']; ?>
  </div>
  <?
  }
  if($voucher_error['997']==1) {
  ?>
  <div class="msg-error">
    <? echo $bony_info['add_error']; ?>
  </div>
  <?
  }
  if($voucher_error['998']==1) {
  ?>
  <div class="msg-error">
    <? echo $bony_info['checkout_error']; ?><br>
    <? echo $koszyk_button_checkout_akceptacja; ?>
  </div>
  <?
  }  
?>

<?
  $voucher_add_koszyk = true;
  
  if($voucher_add_koszyk) {
  ?>
  <div class="voucher">
    <FORM METHOD='POST' class="add" ACTION="<? echo $self; ?>" name="user_action[voucher]">
      <INPUT TYPE="hidden" name="user_action[action]" value="add_voucher">
      <p>
        <label>Discount:
          <input type="text" title="Kod:" value="" name="user_action[voucher_name]">
        </label>
        <input type="submit" class="btn" value="Add" name="submitDiscount">            
      </p>
    </form>  
  </div>
  <br>
  <?
  }
?>