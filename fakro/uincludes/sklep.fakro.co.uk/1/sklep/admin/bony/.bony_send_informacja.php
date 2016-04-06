<?
  global $WEBTD, $BONY_SEND_INFORMACJA, $ssid;

  //$kameleon_adodb->debug=1;

  $xml = array("BONY_SEND_INFORMACJA"=>$BONY_SEND_INFORMACJA);

  if($ssid == $WEBTD->sid) {
    $sql = "UPDATE webtd SET costxt = '".addslashes(serialize($xml))."' WHERE sid = $ssid AND lang = '$lang' AND ver = $ver AND server = $SERVER_ID";
    $kameleon_adodb->execute($sql);
    $tab = $xml;
  }else{
    $xml = $WEBTD->costxt;
    $tab = unserialize(stripslashes($xml));
  }

  $BONY_SEND_INFORMACJA = $tab["BONY_SEND_INFORMACJA"];
?>

<fieldset style="width:99%; margin-left:2px;">
  <legend>Wysylka maila z informacja o przyznaniu vouchera (FAKRO)</legend>
  <form method=post action="<? echo $self; ?>">
    <INPUT TYPE="hidden" NAME="ssid" value="<? echo $WEBTD->sid; ?>">
    <TABLE>
      <TR>
        <TD>Od:</TD>
        <TD><INPUT TYPE="text" size="50" NAME="BONY_SEND_INFORMACJA[mailfrom]" value="<? echo $BONY_SEND_INFORMACJA['mailfrom']; ?>"></TD>
      </TR>
      <TR>
        <TD>Temat:</TD>
        <TD><INPUT TYPE="text" size="50" NAME="BONY_SEND_INFORMACJA[subject]" value="<? echo $BONY_SEND_INFORMACJA['subject']; ?>"></TD>
      </TR>
      <TR>
        <TD>Treść maila:</TD>
        <TD><textarea cols="70" rows="10" NAME="BONY_SEND_INFORMACJA[tresc]"><? echo htmlspecialchars($BONY_SEND_INFORMACJA['tresc']); ?></textarea></TD>
      </TR>
      <TR>
        <TD colspan=2><br></TD>
      </TR>
      <TR>
        <TD>Informacja OK:</TD>
        <TD><textarea cols="70" rows="3" NAME="BONY_SEND_INFORMACJA[send_ok]"><? echo htmlspecialchars($BONY_SEND_INFORMACJA['send_ok']); ?></textarea></TD>
      </TR>
      <TR>
        <TD>Informacja ERROR:</TD>
        <TD><textarea cols="70" rows="3" NAME="BONY_SEND_INFORMACJA[send_error]"><? echo htmlspecialchars($BONY_SEND_INFORMACJA['send_error']); ?></textarea></TD>
      </TR>
      <TR>
        <TD colspan=2><INPUT TYPE="submit" value="Zapisz" class="k_button"></TD>
      </TR>
    </TABLE>
  </form>
</fieldset>

<fieldset style="width:99%; margin-left:2px;">
  <legend>Zmienne</legend>
  <b>dotyczy tylko promocji - (Wybierz sobie prezent)</b><br>
  $voucher_wartosc - przyznana wartosc <em>(150)</em><br>
  <hr>
  <b>wszystkie promocje</b><br>
  $voucher_name - kod vouchera <em>(FD29-C084-5870-1AE3)</em><br>
  $voucher_date - waznosc do kiedy jest voucher <em>(2011-11-05)</em><br>
</fieldset>

<div align="right">sid: <? echo $WEBTD->sid; ?></div>

