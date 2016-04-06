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
?>

<fieldset style="width:99%; margin-left:2px;">
  <legend>Bony - wyswietlenie formularza dodajacego bon (FAKRO)</legend>
  <form method=post action="<? echo $self; ?>">
    <INPUT TYPE="hidden" NAME="ssid" value="<? echo $WEBTD->sid; ?>">
    <TABLE>
      <TR>
        <TD>ERROR przy dodaniu user nie jest zalogowany:<br>
          <textarea cols="95" rows="2" NAME="BONY_ADD_KOSZYK[user_no_login_error]"><? echo htmlspecialchars($BONY_ADD_KOSZYK['user_no_login_error']); ?></textarea></TD>
      </TR>
      <TR>
        <TD>ERROR przy blednym wprowadzeniu:<br>
          <textarea cols="95" rows="2" NAME="BONY_ADD_KOSZYK[add_error]"><? echo htmlspecialchars($BONY_ADD_KOSZYK['add_error']); ?></textarea></TD>
      </TR>
      <TR>
        <TD>ERROR zatwierdzeniu koszyka (min wartosc 1 PLN):<br>
          <textarea cols="95" rows="2" NAME="BONY_ADD_KOSZYK[checkout_error]"><? echo htmlspecialchars($BONY_ADD_KOSZYK['checkout_error']); ?></textarea></TD>
      </TR>
      <TR>
        <TD><INPUT TYPE="submit" value="Zapisz" class="k_button"></TD>
      </TR>
    </TABLE>
  </form>
</fieldset>
<div align="right">sid: <? echo $WEBTD->sid; ?></div>