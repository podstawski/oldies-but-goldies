<?
  global $WEBTD, $BONY_ADD_KOSZYK, $ssid;

  //$kameleon_adodb->debug=1;

  $xml = array("BONY_ADD_KOSZYK"=>$BONY_ADD_KOSZYK);

  if($ssid == $WEBTD->sid) {
    $sql = "UPDATE webtd SET costxt = '".addslashes(serialize($xml))."' WHERE sid = $ssid AND lang = '$lang' AND ver = $ver AND server = $SERVER_ID";
    $kameleon_adodb->execute($sql);
    $tab = $xml;
  }else{
    $xml = $WEBTD->costxt;
    $tab = unserialize(stripslashes($xml));
  }
  
  $BONY_ADD_KOSZYK = $tab["BONY_ADD_KOSZYK"];

  $promocjaArr = array();
  $promocjaArr[1] = 'jubileuszowa promocja';
  $promocjaArr[2] = 'roleta za zlotowke';

  $ch_promocja = '';
  foreach($promocjaArr as $k => $v) {
    $sel = ($k==$BONY_ADD_KOSZYK['promocja'])?"selected":"";
    $ch_promocja .= '<option value="'.$k.'" '.$sel.' >'.$v.'</option>';
  }
?>

<fieldset style="width:99%; margin-left:2px;">
  <legend>Bony - wyswietlenie formularza dodajacego bon [box] (FAKRO)</legend>
  <form method=post action="<? echo $self; ?>">
    <INPUT TYPE="hidden" NAME="ssid" value="<? echo $WEBTD->sid; ?>"><br>
    <TABLE>
      <TR>
        <TD>Promocja:<br>
          <select name="BONY_ADD_KOSZYK[promocja]" class=formselect ><option value="0">Wybierz promocje</option>
          <?php echo $ch_promocja ?>
          </select>  
        </TD>
      </TR>
      <TR>
        <TD>ERROR przy dodaniu user nie jest zalogowany:<br>
          <textarea cols="95" rows="2" NAME="BONY_ADD_KOSZYK[user_no_login_error]"><? echo htmlspecialchars($BONY_ADD_KOSZYK['user_no_login_error']); ?></textarea></TD>
      </TR>
      <TR>
        <TD>ERROR przy blednym wprowadzeniu:<br>
          <textarea cols="95" rows="2" NAME="BONY_ADD_KOSZYK[add_error]"><? echo htmlspecialchars($BONY_ADD_KOSZYK['add_error']); ?></textarea></TD>
      </TR>
      <TR>
        <TD>OK przy prawidlowym dodaniu:<br>
          <textarea cols="95" rows="2" NAME="BONY_ADD_KOSZYK[add_cart]"><? echo htmlspecialchars($BONY_ADD_KOSZYK['add_cart']); ?></textarea></TD>
      </TR>
      <TR>
        <TD><INPUT TYPE="submit" value="Zapisz" class="k_button"></TD>
      </TR>
    </TABLE>
  </form>
</fieldset>
<div align="right">sid: <? echo $WEBTD->sid; ?></div>