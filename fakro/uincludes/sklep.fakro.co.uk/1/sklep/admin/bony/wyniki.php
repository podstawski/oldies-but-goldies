<?
  
  $promocjaArr = array();
  $promocjaArr[1] = 'jubileuszowa promocja';
  $promocjaArr[2] = 'roleta za zlotowke';
  
  $bony_status = array();
  $bony_status[1] = 'przystopienie do programu';
  $bony_status[2] = 'przypisane';
  $bony_status[3] = 'wykorzystane';

  if(!$size) $size=20;

    /*
	echo '<pre>';
    var_dump($_POST);
    var_dump($CIACHO);
    echo '</pre>';
	*/
?>
<div align="right">
  <FORM METHOD='POST' ACTION="<? echo $self; ?>" name="list_promocja_form">
    <INPUT TYPE="hidden" name="action" value="select">
    <select name="ciacho[promocja]" onChange="document.cookie=this.name+'='+this.value+';path=/'; document.list_promocja_form.submit()">
      <option value='0' <? echo ($CIACHO['promocja']==0)?"selected":"";?>>prosze wybrać</option>
	  <option value='1' <? echo ($CIACHO['promocja']==1)?"selected":"";?>>jubileuszowa promocja</option>
      <option value='2' <? echo ($CIACHO['promocja']==2)?"selected":"";?>>roleta za zlotowke</option>
    </select>
  </FORM>
</div>

<?
if(isset($CIACHO['promocja']) AND $CIACHO['promocja'] > 0) {
?>
<div align="right">
  <FORM METHOD='POST' ACTION="<? echo $self; ?>" name="list_sort_form">
    <INPUT TYPE="hidden" name="action" value="select">
    <select name="ciacho[bony]" onChange="document.cookie=this.name+'='+this.value+';path=/'; document.list_sort_form.submit()">
      <option value='0' <? echo ($CIACHO['bony']==0)?"selected":"";?>>prosze wybrać</option>
      <option value='1' <? echo ($CIACHO['bony']==1)?"selected":"";?>>przystopienie do programu</option>
      <option value='2' <? echo ($CIACHO['bony']==2)?"selected":"";?>>przypisane</option>
      <option value='3' <? echo ($CIACHO['bony']==3)?"selected":"";?>>wykorzystane</option>
    </select>
  </FORM>
</div>
<?
	}
  //$projdb->debug=1;

  if(isset($CIACHO['bony']) AND $CIACHO['bony'] > 0 AND $CIACHO['promocja'] > 0) {
    $voucher_status = "AND voucher_status='".$CIACHO['bony']."' AND voucher_type_id='".$CIACHO['promocja']."' ";
  }else{
    return;
  }

  $sql1 = "SELECT * FROM system_user, voucher WHERE system_user.su_id=voucher.su_id ".$voucher_status." ORDER BY voucher.su_id";
  $res1 = $projdb->Execute($sql1);

  if(!$LIST[ile]) {
    $sql2 = "SELECT count(system_user.su_id) AS c FROM system_user, voucher WHERE system_user.su_id=voucher.su_id ".$voucher_status;
    $res2 = $projdb->Execute($sql2);
    parse_str(ado_explodename($res2,0));
    $LIST[ile]=$c;
  }

  if($KAMELEON_MODE) {
    $self.="&bon[type]=1";
  }else{
    $self.="?bon[type]=2";
  }

  $navi=$size?navi($self,$LIST,$size):"";

  if(strlen($navi)) {
    $res1 = $projdb->SelectLimit($sql1,$size,$LIST[start]+0);
  }else{
    $res1 = $projdb->Execute($sql1);
  }

  if(!is_object($res1)) {
    echo sysmsg('Invalid characters where used when searching','admin')." !!!<br>";
    return;
  }

  if (!$res1->RecordCount()) return;

  echo $navi;
?>

<br><br>
<table id="wydruk" class="list_table">
  <col>
  <col>
  <col>
  <col align="right" width="50">
  <TR>
    <th class="name" valign="top">ID</th>
    <th class="name" valign="top">Imie</th>
    <th class="name" valign="top">Nazwisko</th>
    <th class="name" valign="top"></th>
  </TR>
  <?
    for($i=0; $i < $res1->RecordCount(); $i++) {
      parse_str(ado_explodename($res1,$i));

      $options_link = $next;
      if($KAMELEON_MODE) {
        $options = '<a href="'.$options_link.'&user_action[voucher_id]='.$voucher_id.'&user_action[id]='.$su_id.'"><img src="'.$SKLEP_IMAGES.'/i_zobacz.gif" border="0" alt="'.sysmsg("Look","admin").'"></a>';
      }else{
        $options = '<a href="'.$options_link.'?user_action[voucher_id]='.$voucher_id.'&user_action[id]='.$su_id.'"><img src="'.$SKLEP_IMAGES.'/i_zobacz.gif" border="0" alt="'.sysmsg("Look","admin").'"></a>';
      }

      echo '<TR class="'.(($i && ($i%2))?'even':'odd').'">';
      echo '<TD>'.$su_id.'</TD>';
      echo '<TD>'.$su_imiona.'</TD>';
      echo '<TD>'.$su_nazwisko.'</TD>';
      echo '<TD>'.$options.'</TD>';
      echo '</TR>';
    }
  ?>
</table>
