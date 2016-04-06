<?
global $WEBTD, $form, $list;
global $SERVER_ID,$lang,$ver,$adodb,$DEFAULT_PATH_PAGES,$WEBTD;

/************************************************************************************/
function pg_insert_id($pg_result, $serial_column, $table) {
	$oid = pg_getlastoid($pg_result);
	$query = "SELECT $serial_column FROM $table WHERE oid = $oid";
	$result = pg_exec($query);
	$row = pg_fetch_row($result, 0);
	return($row[0]);
	}
/************************************************************************************/

echo '
<fieldset style="width:99%; margin-left:2px;">
<legend>Domy PL (FAKRO)</legend>
<div align="right">
<br>
<table border="0" cellspacing="0" cellpadding="3">
<form method=post action="'.$self.'">
<INPUT TYPE="hidden" NAME="form[pole]" value="szukaj">
<tr>
	<td align="right">wielkość domu </td>
	<td>
	<select name="form[pu]">
		<option value="1" '.((htmlspecialchars($form[pu])==1)?"selected":"").'>Do 100 m2</option>
		<option value="2" '.((htmlspecialchars($form[pu])==2)?"selected":"").'>100 - 150 m2</option>
		<option value="3" '.((htmlspecialchars($form[pu])==3)?"selected":"").'>Powyżej 150 m2</option>
	</select>
	</td>
</tr>
<!---
<tr>
	<td align="right">piwnica </td>
	<td>
	<select name="form[pp]">
		<option value="1" '.((htmlspecialchars($form[pp])==1)?"selected":"").'>TAK</option>
		<option value="0" '.((htmlspecialchars($form[pp])==0)?"selected":"").'>NIE</option>
	</select>
	</td>
</tr>
<tr>
	<td align="right">garaż </td>
	<td>
	<select name="form[pg]">
		<option value="1" '.((htmlspecialchars($form[pg])==1)?"selected":"").'>TAK</option>
		<option value="0" '.((htmlspecialchars($form[pg])==0)?"selected":"").'>NIE</option>
	</select>
	</td>
</tr>
--->
<tr>
	<td align="right"><INPUT TYPE="submit" value="Szukaj" class="k_button"></td>
	</form>
	<form method=post action="'.$self.'">
	<INPUT TYPE="hidden" NAME="form[pole]" value="edycja">
	<INPUT TYPE="hidden" NAME="form[projekt_id]" value="zmien">
	<INPUT TYPE="hidden" NAME="form[form][projekt_id]" value="new">
	<td><INPUT TYPE="submit" value="Dodaj nowy" class="k_button"></td>
	</form>
</tr>
</table>
</div><br>';

if($form[pole] == "szukaj") {
	#$fakrodb->debug=1;
	
	if(!$size) $size=25;
	
	if($form[pu] == 1) $cond .= " AND projekt_p_uzytkowa <= '100'";
	if($form[pu] == 2) $cond .= " AND (projekt_p_uzytkowa >= '100' AND projekt_p_uzytkowa <= '150')";
	if($form[pu] == 3) $cond .= " AND projekt_p_uzytkowa >= '150'";
	
	# if(strlen($form[pp]))	$cond .= " AND projekt_piwnica = '".addslashes(stripslashes($form[pp]))."'";
	# if(strlen($form[pg]))	$cond .= " AND projekt_garaz = '".addslashes(stripslashes($form[pg]))."'";
	
	$sql = "SELECT * FROM dom_projekt WHERE projekt_status='1' $cond ORDER BY projekt_id,projekt_nazwa";
	$res = $fakrodb->execute($sql);
	
	if(!$list[ile]) {
		$query="SELECT count(projekt_id) AS c FROM dom_projekt WHERE projekt_status='1' $cond ";
		$res2 = $fakrodb->execute($query);
		parse_str(ado_explodename($res2,0));
		$list[ile]=$c;
		}
	
	if($KAMELEON_MODE)
		$self .= "&szukaj[pu]=".$szukaj[pu]."&szukaj[pp]=".$szukaj[pp]."&szukaj[pg]=".$szukaj[pg];
		else
		$self .= "&szukaj[pu]=".$szukaj[pu]."&szukaj[pp]=".$szukaj[pp]."&szukaj[pg]=".$szukaj[pg];
	
	#$navi=$size?navi($self_navi,$LIST,$size):"";
	$navi=$size?navi($self,$list,$size):"";
	
	if(strlen($navi))
		$res = $fakrodb->SelectLimit($sql,$size,$list[start]+0);
		else
		$res = $fakrodb->Execute($sql);
	
	if(!$res->RecordCount()) {
		echo '<br><br><div align="center"><strong>Nie znaleziono żadnych wpisów o podanych kryteriach lub lista jest pusta.</strong></div>';
		return;
		}
	
	echo $navi;
	echo '<br><br>
	<TABLE class="list_table">
	<col>
	<col width="120">
	<col width="120">
	
	<thead>
	<TR>
		<TD align="center">nazwa</TD>
		<TD align="center">pow. użytkowa</TD>
		<TD align="center">pow. zabudowy</TD>
	</TR>
	</thead>
	<tbody>';
	
	for($i=0; $i < $res->RecordCount(); $i++) {
		parse_str(ado_explodename($res,$i));
		echo '
		<TR class="'.(($i && ($i%2))?"even":"odd").'">
			<TD class="name" valign="top"><a href="'.$self.'&form[view]=1&form[projekt_id]='.$projekt_id.'">'.stripslashes($projekt_nazwa).'</a></TD>
			<TD align="center">'.$projekt_p_uzytkowa.' m<sup>2</sup></TD>
			<TD align="center">'.$projekt_p_zabudowy.' m<sup>2</sup></TD>
		</TR>';
		}
	
	echo '</tbody></table>';
	}
if($form[view] == 1) {
	#$fakrodb->debug=1;
	$sql = "SELECT * FROM dom_projekt LEFT JOIN dom_firma ON (dom_projekt.firma_id = dom_firma.firma_menu) WHERE projekt_id='$form[projekt_id]'";
	parse_str(query2url($sql));
	
	echo '<table class="list_table"><tbody><col align="right" width="40%"><col class="cd">';
	echo '
	<tr>
		<td width="40%"></td>
		<td width="60%"></td>
	</tr>
	<tr>
		<td colspan="2" align="center" bgcolor="'.(($projekt_status == 1)?"#339900":"#cc0000").'"><strong>'.(($projekt_status == 1)?"widoczny":"niewidoczny").'</strong></td>
	</tr>
	<tr>
		<td align="right"><strong>Firma:</strong></td>
		<td>'.stripslashes($firma_nazwa).'</td>
	</tr>
	<tr>
		<td align="right"><strong>Nazwa projektu:</strong></td>
		<td>'.$projekt_nazwa.'</td>
	</tr>
	<tr>
		<td align="right"><strong>Powierzchnia użytkowa:</strong></td>
		<td>'.$projekt_p_uzytkowa.' m<sup>2</sup></td>
	</tr>
	<tr>
		<td align="right"><strong>Powierzchnia zabudowy:</strong></td>
		<td>'.$projekt_p_zabudowy.' m<sup>2</sup></td>
	</tr>
	<tr>
		<td align="right"><strong>Kąt nachylenia dachu:</strong></td>
		<td>'.$projekt_k_nachylenia.' &ordm;</td>
	</tr>
	<tr>
		<td align="right"><strong>Wysokość budynku:</strong></td>
		<td>'.$projekt_w_budynku.' m<sup>2</sup></td>
	</tr>
	<tr>
		<td align="right"><strong>Minimalne wymiary działki:</strong></td>
		<td>'.$projekt_p_dzialki.' m<sup>2</sup></td>
	</tr>
	<!---  --->
	<tr>
		<td align="right"><strong>Ściany:</strong></td>
		<td>'.$projekt_sciany.'</td>
	</tr>
	<tr>
		<td align="right"><strong>Stropy:</strong></td>
		<td>'.$projekt_stropy.'</td>
	</tr>
	<tr>
		<td align="right"><strong>Dach:</strong></td>
		<td>'.$projekt_dach.'</td>
	</tr>
	<tr>
		<td align="right"><strong>Pokrycie dachu:</strong></td>
		<td>'.$projekt_pokrycie.'</td>
	</tr>
	<tr>
		<td align="right"><strong>Elewacje:</strong></td>
		<td>'.$projekt_elewacje.'</td>
	</tr>
	<tr>
		<td align="right"><strong>Okna dachowe:</strong></td>
		<td>'.$projekt_okna.'</td>
	</tr>
	<!---  --->
	<tr>
		<td align="right"><strong>Piwnica:</strong></td>
		<td>'.(($projekt_piwnica == 1)?"TAK":"NIE").'</td>
	</tr>
	<tr>
		<td align="right"><strong>Garaż:</strong></td>
		<td>'.(($projekt_garaz == 1)?"TAK":"NIE").'</td>
	</tr>
	<tr>
		<td colspan="2" align="left"><br><strong>Charakterystyka domu:</strong><br>'.$projekt_charakterystyka.'</td>
	</tr>
	</tbody>
	</table>';
	echo '<div align="right"><TABLE><tbody>';
	echo '
	<TR>
	<form method=post action="'.$self.'">
		<td><INPUT TYPE="submit" value="Edycja" class="k_button">
		<INPUT TYPE="hidden" NAME="form[pole]" value="edycja">
		<INPUT TYPE="hidden" NAME="form[projekt_id]" value="'.$projekt_id.'"></td>
	</form>
	<form method=post action="'.$self.'">
		<td><INPUT TYPE="submit" value="Usun" class="k_button" onClick="return confirm(\'Czy na pewno chcesz to usunac ?\');">
		<INPUT TYPE="hidden" NAME="form[pole]" value="usun">
		<INPUT TYPE="hidden" NAME="form[projekt_id]" value="'.$projekt_id.'"></td>
	</form>
	</tr></tbody>
	</TABLE></div>';
	}

if($form[pole] == "edycja") {
	#$fakrodb->debug=1;
	
	if($form[projekt_id] != "zmien") {
		$sql = "SELECT * FROM dom_projekt WHERE projekt_id='$form[projekt_id]'";
		parse_str(query2url($sql));
		
		$form[form][projekt_id] = $projekt_id;
		$form[form][firma_id] = $firma_id;
		$form[form][projekt_nazwa] = $projekt_nazwa;
		$form[form][projekt_p_uzytkowa] = $projekt_p_uzytkowa;
		$form[form][projekt_p_zabudowy] = $projekt_p_zabudowy;
		$form[form][projekt_k_nachylenia] = $projekt_k_nachylenia;
		$form[form][projekt_w_budynku] = $projekt_w_budynku;
		$form[form][projekt_p_dzialki] = $projekt_p_dzialki;
		$form[form][projekt_sciany] = $projekt_sciany;
		$form[form][projekt_stropy] = $projekt_stropy;
		$form[form][projekt_dach] = $projekt_dach;
		$form[form][projekt_pokrycie] = $projekt_pokrycie;
		$form[form][projekt_elewacje] = $projekt_elewacje;
		$form[form][projekt_okna] = $projekt_okna;
		$form[form][projekt_piwnica] = $projekt_piwnica;
		$form[form][projekt_garaz] = $projekt_garaz;
		$form[form][projekt_charakterystyka] = $projekt_charakterystyka;
		$form[form][projekt_status] = $projekt_status;
		}
	
	echo '<table class="list_table">';
	echo '<tbody>';
	echo '<form method=post action="'.$self.'">
	<tr>
		<td align="right"><strong>Status:</strong></td>
		<td>
		<select class="sys_input" name="form[form][projekt_status]">
			<option value="1" '.(($form[form][projekt_status] == 1)?"selected":"").'>widoczny</option>
			<option value="0" '.(($form[form][projekt_status] == 0)?"selected":"").'>niewidoczny</option>
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><strong>Firma:</strong></td>
		<td><select class="sys_input" name="form[form][firma_id]">';
	
	$sql = "SELECT * FROM dom_firma ORDER BY firma_menu";
	$res = $fakrodb->execute($sql);
	
	for($i=0; $i < $res->RecordCount(); $i++) {
		parse_str(ado_explodename($res,$i));
		
		echo '<option value="'.$firma_menu.'" '.(($form[form][firma_id] == $firma_menu)?"selected":"").'>'.$firma_nazwa.'</option>';
		}
	
	echo '
		</select>
		</td>
	</tr>
	<tr>
		<td align="right"><strong>Nazwa projektu:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_nazwa]" value="'.$form[form][projekt_nazwa].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Powierzchnia użytkowa:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_p_uzytkowa]" value="'.$form[form][projekt_p_uzytkowa].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Powierzchnia zabudowy:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_p_zabudowy]" value="'.$form[form][projekt_p_zabudowy].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Kąt nachylenia dachu:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_k_nachylenia]" value="'.$form[form][projekt_k_nachylenia].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Wysokość budynku:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_w_budynku]" value="'.$form[form][projekt_w_budynku].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Minimalne wymiary działki:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_p_dzialki]" value="'.$form[form][projekt_p_dzialki].'"></td>
	</tr>
	<!---  --->
	<tr>
		<td align="right"><strong>Ściany:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_sciany]" value="'.$form[form][projekt_sciany].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Stropy:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_stropy]" value="'.$form[form][projekt_stropy].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Dach:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_dach]" value="'.$form[form][projekt_dach].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Pokrycie dachu:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_pokrycie]" value="'.$form[form][projekt_pokrycie].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Elewacje:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_elewacje]" value="'.$form[form][projekt_elewacje].'"></td>
	</tr>
	<tr>
		<td align="right"><strong>Okna dachowe:</strong></td>
		<td><INPUT TYPE="text" class="sys_input" style="width:300" size="40" NAME="form[form][projekt_okna]" value="'.$form[form][projekt_okna].'"></td>
	</tr>
	<!---  --->
	<tr>
		<td align="right"><strong>Piwnica:</strong></td>
		<td>
		<select class="sys_input" name="form[form][projekt_piwnica]">
			<option value="1" '.(($form[form][projekt_piwnica] == 1)?"selected":"").'>TAK</option>
			<option value="0" '.(($form[form][projekt_piwnica] == 0)?"selected":"").'>NIE</option>
		</select></td>
	</tr>
	<tr>
		<td align="right"><strong>Garaż:</strong></td>
		<td>
		<select class="sys_input" name="form[form][projekt_garaz]">
			<option value="1" '.(($form[form][projekt_garaz] == 1)?"selected":"").'>TAK</option>
			<option value="0" '.(($form[form][projekt_garaz] == 0)?"selected":"").'>NIE</option>
		</select></td>
	</tr>
	<tr>
		<td><strong>Charakterystyka domu:</strong></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="2"><textarea rows="5" name="form[form][projekt_charakterystyka]" style="OVERFLOW-Y: scroll; width: 470px;">'.$form[form][projekt_charakterystyka].'</textarea></td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
	</tbody>
	</table>';
	echo '<div align="right"><TABLE><tbody>';
	echo '
		<TR>
			<td><INPUT TYPE="submit" value="Zapisz" class="k_button">
			<INPUT TYPE="hidden" NAME="form[pole]" value="zmien">
			<INPUT TYPE="hidden" NAME="form[form][projekt_id]" value="'.$form[form][projekt_id].'"></td>
		</form>
		</tr></tbody>
		</TABLE></div>';
	}

if($form[pole] == "usun" && $form[projekt_id]) {
	#$fakrodb->debug=1;
	$sql = "DELETE FROM dom_projekt WHERE projekt_id='$form[projekt_id]'";
	pg_exec($db,$sql);
	echo "<div align=\"center\"><strong>Dane zostaly usuniete</strong></div><br><br>";
	}

if($form[pole] == "zmien") {
	#$fakrodb->debug=1;
	if($form[form][projekt_id] == 'new') {
		
		$sql = "INSERT INTO dom_projekt
			(firma_id,projekt_nazwa,projekt_p_uzytkowa,projekt_p_zabudowy,projekt_k_nachylenia,
			projekt_w_budynku,projekt_p_dzialki,projekt_sciany,
			projekt_stropy,projekt_dach,projekt_pokrycie,
			projekt_elewacje,projekt_okna,projekt_piwnica,
			
			projekt_garaz,projekt_charakterystyka,projekt_status)
			VALUES
			(
			'".$form[form][firma_id]."',
			'".$form[form][projekt_nazwa]."',
			'".ereg_replace(",",".",$form[form][projekt_p_uzytkowa])."',
			'".ereg_replace(",",".",$form[form][projekt_p_zabudowy])."',
			'".ereg_replace(",",".",$form[form][projekt_k_nachylenia])."',
			'".ereg_replace(",",".",$form[form][projekt_w_budynku])."',
			'".$form[form][projekt_p_dzialki]."',
			'".$form[form][projekt_sciany]."',
			'".$form[form][projekt_stropy]."','".$form[form][projekt_dach]."','".$form[form][projekt_pokrycie]."',
			'".$form[form][projekt_elewacje]."','".$form[form][projekt_okna]."','".$form[form][projekt_piwnica]."',
			'".$form[form][projekt_garaz]."','".$form[form][projekt_charakterystyka]."','".$form[form][projekt_status]."')";
		$re = pg_exec($db,$sql);
		
		$id = pg_insert_id($re, "projekt_id", "dom_projekt");
		echo "Here is the last_insert_id -> $id <P>";
		
		echo "<div align=\"center\"><strong>Dane zostaly wprowadzone</strong></div><br><br>";
		}else{
		$sql = "UPDATE dom_projekt SET
			firma_id = '".$form[form][firma_id]."',
			projekt_nazwa = '".$form[form][projekt_nazwa]."',
			projekt_p_uzytkowa = '".ereg_replace(",",".",$form[form][projekt_p_uzytkowa])."',
			projekt_p_zabudowy = '".ereg_replace(",",".",$form[form][projekt_p_zabudowy])."',
			projekt_k_nachylenia = '".ereg_replace(",",".",$form[form][projekt_k_nachylenia])."',
			projekt_w_budynku = '".ereg_replace(",",".",$form[form][projekt_w_budynku])."',
			projekt_p_dzialki = '".$form[form][projekt_p_dzialki]."',
			projekt_sciany = '".$form[form][projekt_sciany]."',
			projekt_stropy = '".$form[form][projekt_stropy]."',
			projekt_dach = '".$form[form][projekt_dach]."',
			projekt_pokrycie = '".$form[form][projekt_pokrycie]."',
			projekt_elewacje = '".$form[form][projekt_elewacje]."',
			projekt_okna = '".$form[form][projekt_okna]."',
			projekt_piwnica = '".$form[form][projekt_piwnica]."',
			projekt_garaz = '".$form[form][projekt_garaz]."',
			projekt_charakterystyka = '".$form[form][projekt_charakterystyka]."',
			projekt_status = '".$form[form][projekt_status]."'
			WHERE
			projekt_id = '".$form[form][projekt_id]."'";
		pg_exec($db,$sql);
		echo "<div align=\"center\"><strong>Dane zostaly zmienione</strong></div><br><br>";
		}
	}

echo "</fieldset>";
echo "<div align=\"right\">sid: ".$WEBTD->sid."</div>";
?>