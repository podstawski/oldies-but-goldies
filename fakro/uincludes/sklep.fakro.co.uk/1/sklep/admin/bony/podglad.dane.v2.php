<?
  //$projdb->debug=1;

  global $bon_wysylka_maila_2;
  
  if(!$kat_id) {
		echo sysmsg('Invalid characters where used when searching','admin')." !!!<br>";
		return;
	}
	
	//  dodanie produktu do usera
if($user_action['action'] == 'select_product') {
	
	if($user_action['product']['id'] > 0 && $user_action['product']['ilosc'] > 0) {
		$sql_action_select_product = "SELECT voucher_produkt_ean FROM voucher_produkt_user WHERE voucher_id='".$user_action['voucher_id']."' AND su_id='".$user_action['id']."' AND voucher_produkt_ean='".$user_action['product']['id']."';";
		$res_action_select_product = $projdb->Execute($sql_action_select_product);
		$voucher_punkty = $res_action_select_product->RecordCount();
		
		if(!$voucher_punkty) {
			$query = "INSERT INTO voucher_produkt_user (voucher_id,su_id,voucher_produkt_id,voucher_produkt_ilosc,voucher_produkt_ean) VALUES (".$user_action['voucher_id'].",".$user_action['id'].",0,".$user_action['product']['ilosc'].",".$user_action['product']['id'].");";
			$projdb->Execute($query);
		}
	}
}

//  usuniecie produktu usera
if($user_action['action'] == 'delete_product') {
	
	if($user_action['voucher_produkt_user_id'] > 0) {
		$query_delete = "DELETE FROM voucher_produkt_user WHERE voucher_produkt_user_id='".$user_action['voucher_produkt_user_id']."';";
		$projdb->Execute($query_delete);    
	}
}

//  dodanie wartosci do usera
if($user_action['action'] == 'wartosc') {
	$query = "UPDATE voucher SET voucher_wartosc= NULL, voucher_date_modyfikacji='".time()."', voucher_status='2' WHERE voucher_id='".$user_action['voucher_id']."' AND su_id='".$user_action['id']."';";
	$projdb->Execute($query);
	
	$sql = "SELECT * FROM voucher WHERE voucher_id='".$user_action['voucher_id']."' AND su_id=".$user_action['id'];
	parse_str(ado_query2url($sql));
}
?>

<?
  //  lista produktow w promocji przypisane do klienta
  $sql_voucher_produkt_user = "SELECT * FROM voucher_produkt_user WHERE voucher_id='".$user_action['voucher_id']."' AND su_id='".$user_action['id']."';";
  $res_voucher_produkt_user = $projdb->Execute($sql_voucher_produkt_user);

  $voucher_punkty = $res_voucher_produkt_user->RecordCount();
  
  if($voucher_status>1) {
    ?>
    <div style="text-align: center; margin-top: 5px; margin-bottom: 5px;">
      <h2>
      Kod vouchera: <? echo $voucher_name; ?><br>
      Waznosc vouchera: <? echo date("Y-m-d", ($voucher_date_modyfikacji+(86400*365))); ?>
      </h2>
    </div>

    <div style="text-align: center; margin-top: 5px; margin-bottom: 5px; <?php echo $bon_input_hidden ?>">
      <FORM METHOD=POST ACTION="<? echo $bon_wysylka_maila_2; ?>" name="user_action[bon_wysylka_maila]">
        <INPUT TYPE="hidden" name="user_action[id]" value="<? echo $user_action['id']; ?>">
		<INPUT TYPE="hidden" name="user_action[voucher_id]" value="<? echo $user_action['voucher_id']; ?>">
        <INPUT TYPE="submit" NAME="" VALUE="<? echo sysmsg('Wysylka maila z informacja o przyznaniu vouchera','bony'); ?>" class="button" style="width:300px;">
      </FORM>
    </div>
    <?
    }elseif($voucher_punkty>0) {
    ?>
    <br><br>
    <FORM METHOD='POST' ACTION="<? echo $self; ?>" name="user_action[wartosc]">
      <INPUT TYPE="hidden" name="user_action[action]" value="wartosc">
      <INPUT TYPE="hidden" name="user_action[id]" value="<? echo $user_action['id']; ?>">
      <INPUT TYPE="hidden" name="user_action[voucher_id]" value="<? echo $voucher_id; ?>">
      <div style="text-align: center; margin-top: 5px; margin-bottom: 5px;">
        <INPUT TYPE="submit" NAME="" VALUE="<? echo sysmsg('Zatwierdzenie - przekazanie do klienta','bony'); ?>" class="button" style="width:250px;" onclick="return confirm('Jesteś pewien wykonaia tej operacji? Po zatwierdzeniu zostanie wyslany email od klienta!');">
      </div>
    </FORM>
    <?
    }
  ?>


    <table id="wydruk" class="list_table">
      <col>
      <col width="100">
      <col width="100">
      <TR>
        <th class="name" valign="top">Produkt</th>
		<th class="name" valign="top" align="center">cena</th>
        <th class="name" valign="top" align="center">ilosc</th>
        <th class="name" valign="top"></th>
      </TR>
      <?
        if (!$res_voucher_produkt_user->RecordCount())  {
		
		}else{

        for($i=0; $i < $res_voucher_produkt_user->RecordCount(); $i++) {
          parse_str(ado_explodename($res_voucher_produkt_user,$i));
		  
		  $sql_towar = "SELECT * FROM towar LEFT JOIN towar_sklep ON to_id=ts_to_id WHERE to_id='".$voucher_produkt_ean."';";
		  parse_str(ado_query2url($sql_towar));
		  
		  $cena_br = round($ts_cena*(100+$to_vat))/100;
		  $cena_br = $cena_br.' PLN';

          if($voucher_status>1) {
              echo '<TR class="'.(($i && ($i%2))?'even':'odd').'">';
              echo '<TD>'.$to_indeks.'</TD>';
              echo '<TD align="center">'.$cena_br.'</TD>';
			  echo '<TD align="center">'.$voucher_produkt_ilosc.'</TD>';
              echo '<TD></TD>';
              echo '</TR>';
          }else{
		  ?>
		  <FORM METHOD='POST' ACTION="<? echo $self; ?>" name="user_action[product]">
		  <INPUT TYPE="hidden" name="user_action[action]" value="delete_product">
		  <INPUT TYPE="hidden" name="user_action[id]" value="<? echo $user_action['id']; ?>">
		  <INPUT TYPE="hidden" name="user_action[voucher_id]" value="<? echo $user_action['voucher_id']; ?>">
		  <INPUT TYPE="hidden" name="user_action[voucher_produkt_user_id]" value="<? echo $voucher_produkt_user_id; ?>">
		  <?
			echo '<TR class="'.(($i && ($i%2))?'even':'odd').'">';
            echo '<TD>'.$to_indeks.'</TD>';
            echo '<TD align="center">'.$cena_br.'</TD>';
			echo '<TD align="center">'.$voucher_produkt_ilosc.'</TD>';
            echo '<TD><INPUT TYPE="submit" NAME="" VALUE="x" class="button" style="width:20px;" onclick="return confirm(\'Jesteś pewien wykonaia tej operacji?\');"></TD>';
            echo '</TR>';
			?>
			</FORM>
			<?
          }
        }
		
		}
      ?>
    </table>


<?
if($voucher_status == 1) {

  //  lista produktow w promocji
  $sql_towar_kategoria = "SELECT * FROM towar_kategoria LEFT JOIN towar ON tk_to_id=to_id LEFT JOIN towar_sklep ON to_id=ts_to_id AND ts_sk_id='".$SKLEP_ID."' WHERE tk_ka_id='".$kat_id."' AND ts_aktywny>0";
  $res_towar_kategoria = $projdb->Execute($sql_towar_kategoria);
  
  if(!is_object($res_towar_kategoria)) {
	echo sysmsg('Invalid characters where used when searching','admin')." !!!<br>";
	return;
	}
	
	if (!$res_towar_kategoria->RecordCount()) return;
	
	$select_lista_produktow = '';
	$cena_br = '';
	
	for($i=0; $i < $res_towar_kategoria->RecordCount(); $i++) {
          parse_str(ado_explodename($res_towar_kategoria,$i));
		  
		  $cena_br = round($ts_cena*(100+$to_vat))/100;
		  $cena_br = $cena_br.' PLN';
		  $select_lista_produktow .= "\n<option value='".$to_id."'>".$to_indeks.' - '.$cena_br."</option>";
	}
?>

<br><hr><br>

<FORM METHOD='POST' ACTION="<? echo $self; ?>" name="user_action[product]">
<INPUT TYPE="hidden" name="user_action[action]" value="select_product">
<INPUT TYPE="hidden" name="user_action[id]" value="<? echo $user_action['id']; ?>">
<INPUT TYPE="hidden" name="user_action[voucher_id]" value="<? echo $voucher_id; ?>">
<p align=left>
	<select name="user_action[product][id]" class="formselect" >
	<option value="0">Wybierz produkt</option>
	<? echo $select_lista_produktow; ?>
	</select>
	
	<input type="text" size="5" maxlength="2" value="" name="user_action[product][ilosc]">
	<input type="submit" value="Zapisz" class="formbutton">
</p>
</FORM>

<?
}
?>
