<?
  if("$FORM[sid]" == "$WEBTD->sid") {
    $pos=strpos($costxt,"&sg=");
    if($pos) $costxt=substr($costxt,0,$pos);

    $sg=":";

    foreach(array_keys($FORM) AS $k) {
      if(substr($k,0,3)!="sg_") continue;

      $sg.=substr($k,3).":";
    }

    $costxt.="&sg=$sg";

    if(!strlen($FORM[confirm])) $FORM[confirm] = "0";

    $sql = "UPDATE webtd SET cos = ".$FORM[confirm].",costxt='$costxt' WHERE sid = $WEBTD->sid";
    $kameleon_adodb->execute($sql);
    $cos = $FORM['confirm'];
  }

  $ch_1 = strstr($sg,":view:")?"checked":"";
  $ch_2 = strstr($sg,":generate:")?"checked":"";
  $ch_3 = strstr($sg,":print:")?"checked":"";
  
  echo "
  <FORM METHOD=POST ACTION=\"$self\">
  <INPUT TYPE=\"hidden\" name=\"form[sid]\" value=\"$WEBTD->sid\">
  <TABLE>
  <tr><td>Podglad danych:</td><td><input type=checkbox value=1 name=\"form[sg_view]\" $ch_1></td></tr>
  <tr><td>Generowanie vouchera:</td><td><input type=checkbox value=1 name=\"form[sg_generate]\" $ch_2></td></tr>
  <tr><td>Drukowanie danych:</td><td><input type=checkbox value=1 name=\"form[sg_print]\" $ch_3></td></tr>
  <TR><TD colspan=2><INPUT TYPE=\"submit\" value=\"Zapisz\"></TD></TR>
  </TABLE>
  </FORM>";
?>