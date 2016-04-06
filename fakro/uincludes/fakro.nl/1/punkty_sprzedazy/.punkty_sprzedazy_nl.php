<?
global $WEBTD, $punkty, $list;

#print_r($punkty);

echo "
<fieldset style=\"width:99%; margin-left:2px;\">
<legend>Punkty sprzedazy NL (FAKRO)</legend>
<div align=\"right\">
<TABLE>
<TR>
<form method=post action=\"$self\">
	<td><INPUT TYPE=\"text\" size=\"30\" NAME=\"punkty[szukaj]\" value=\"".$punkty[szukaj]."\">
		<INPUT TYPE=\"submit\" value=\"Szukaj\" class=\"k_button\">
		<INPUT TYPE=\"hidden\" NAME=\"punkty[pole]\" value=\"szukaj\"></td>
</form>
<form method=post action=\"$self\">
	<td><INPUT TYPE=\"submit\" value=\"Dodaj nowy\" class=\"k_button\">
		<INPUT TYPE=\"hidden\" NAME=\"punkty[pole]\" value=\"edycja\">
		<INPUT TYPE=\"hidden\" NAME=\"punkty[id]\" value=\"zmien\">
		<INPUT TYPE=\"hidden\" NAME=\"punkty[form][id]\" value=\"new\"></td>
</form>
</TR>
</TABLE>
</div><br><br>";

if($punkty[pole] == "szukaj") {
	#$fakrodb->debug=1;
	
	$LIST = $list;
	if(!$size) $size=25;
	if($punkty[szukaj]) $cond.= " WHERE nazwa ~* '".addslashes(stripslashes($punkty[szukaj]))."'";
	
	$sql = "SELECT * FROM punkty_sprzedazy_nl $cond ORDER BY nazwa,kod";
	$res = $fakrodb->execute($sql);
	
	if(!$LIST[ile]) {
		$query="SELECT count(id) AS c FROM punkty_sprzedazy_nl $cond ";
		$res2 = $fakrodb->execute($query);
		parse_str(ado_explodename($res2,0));
		$LIST[ile]=$c;
		}
	
	if($KAMELEON_MODE)
		$self_navi.="&punkty[pole]=szukaj&punkty[szukaj]=".$punkty[szukaj];
		else
		$self_navi.="&punkty[pole]=szukaj&punkty[szukaj]=".$punkty[szukaj];
	
	$navi=$size?navi($self_navi,$LIST,$size):"";
	
	if(strlen($navi))
		$res = $fakrodb->SelectLimit($sql,$size,$LIST[start]+0);
		else
		$res = $fakrodb->Execute($sql);
	
	if(!$res->RecordCount()) {
		echo "<br><br><div align=\"center\"><strong>Brak punktów sprzeda¿y spe³niaj±cych podane kryteria.</strong></div>";
		return;
		}
	
	echo "$navi<br><br>
	<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\"><tbody>";
	for($i=0; $i < $res->RecordCount(); $i++) {
		parse_str(ado_explodename($res,$i));
		
		echo "
		<TR class=\"".(($i && ($i%2))?"even":"odd")."\">
			<TD class=\"name\" valign=\"top\">".(($www)?"<a href=\"http://".$www."\" target=\"_blank\"><img src=\"".$INCLUDE_PATH."/punkty_sprzedazy/i_www.gif\" align=\"right\" width=\"19\" height=\"28\" border=\"0\"></a>":"")."<a href=\"$self&punkty[view]=1&punkty[id]=$id\">".stripslashes($nazwa)."</a></TD>
			<TD valign=\"top\">$adres<br>$kod $miasto</TD>
			<TD valign=\"top\" style=\"font-weight:bold\">$tel<br>$fax</TD>
		</TR>";
		}
	echo "</tbody></table>";
	}
if($punkty[view] == 1) {
	#$fakrodb->debug=1;
	$sql = "SELECT * FROM punkty_sprzedazy_nl WHERE id='$punkty[id]'";
	parse_str(query2url($sql));
	
	echo "<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\"><tbody>";
	echo "
		<tr>
			<td width=\"100\" align=\"right\"><strong>Nazwa:</strong></td>
			<td>".stripslashes($nazwa)."</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Adres:</strong></td>
			<td>$adres</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Kod:</strong></td>
			<td>$kod</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Miasto:</strong></td>
			<td>$miasto</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Tel:</strong></td>
			<td>$tel</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Fax:</strong></td>
			<td>$fax</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>WWW:</strong></td>
			<td>$www</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Status:</strong></td>
			<td>".(($status == 1)?"widoczny":"niewidoczny")."</td>
		</tr>
		</tbody>
		</table>";
	echo "<div align=\"right\"><TABLE><tbody>";
	echo "
		<TR>
		<form method=post action=\"$self\">
			<td><INPUT TYPE=\"submit\" value=\"Edycja\" class=\"k_button\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[pole]\" value=\"edycja\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[id]\" value=\"$id\"></td>
		</form>
		<form method=post action=\"$self\">
			<td><INPUT TYPE=\"submit\" value=\"Usun\" class=\"k_button\" onClick=\"return confirm('Czy na pewno chcesz to usunac ?');\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[pole]\" value=\"usun\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[id]\" value=\"$id\"></td>
		</form>
		</tr></tbody>
		</TABLE></div>";
	}

if($punkty[pole] == "edycja") {
	#$fakrodb->debug=1;
	
	if($punkty[id] != "zmien") {
		$sql = "SELECT * FROM punkty_sprzedazy_nl WHERE id='$punkty[id]'";
		parse_str(query2url($sql));
		
		$punkty[form][id] = $id;
		$punkty[form][nazwa] = $nazwa;
		$punkty[form][adres] = $adres;
		$punkty[form][kod] = $kod;
		$punkty[form][miasto] = $miasto;
		$punkty[form][tel] = $tel;
		$punkty[form][fax] = $fax;
		$punkty[form][status] = $status;
		$punkty[form][www] = $www;
		}
	
	echo "<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\"><tbody>";
	echo "<form method=post action=\"$self\">
		<tr>
			<td width=\"100\" align=\"right\"><strong>Nazwa:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][nazwa]\" value=\"".$punkty[form][nazwa]."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Adres:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][adres]\" value=\"".$punkty[form][adres]."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Kod:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][kod]\" value=\"".$punkty[form][kod]."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Miasto:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][miasto]\" value=\"".$punkty[form][miasto]."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Tel:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][tel]\" value=\"".$punkty[form][tel]."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Fax:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][fax]\" value=\"".$punkty[form][fax]."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>WWW:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][www]\" value=\"".$punkty[form][www]."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Status:</strong></td>
			<td>
			<select name=\"punkty[form][status]\">
				<option value=\"1\" ".(($punkty[form][status] == 1)?"selected":"").">widoczny</option>
				<option value=\"2\" ".(($punkty[form][status] == 2)?"selected":"").">niewidoczny</option>
			</select></td>
		</tr>
		</tbody>
		</table>";
	echo "<div align=\"right\"><TABLE><tbody>";
	echo "
		<TR>
			<td><INPUT TYPE=\"submit\" value=\"".(($punkty[form][id] == 'new')?"Dodaj":"Edycja")."\" class=\"k_button\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[pole]\" value=\"zmien\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[form][id]\" value=\"".$punkty[form][id]."\"></td>
		</form>
		</tr></tbody>
		</TABLE></div>";
	}

if($punkty[pole] == "usun" && $punkty[id]) {
	#$fakrodb->debug=1;
	$sql = "DELETE FROM punkty_sprzedazy_nl WHERE id='$punkty[id]'";
	pg_exec($db,$sql);
	echo "<div align=\"center\"><strong>Dane zostaly usuniete</strong></div><br><br>";
	}
if($punkty[pole] == "zmien") {
	#$fakrodb->debug=1;
	if($punkty[form][id] == 'new') {
		$sql = "INSERT INTO punkty_sprzedazy_nl
			(nazwa,adres,kod,miasto,tel,fax,status,www)
			VALUES
			('".$punkty[form][nazwa]."','".$punkty[form][adres]."','".$punkty[form][kod]."','".$punkty[form][miasto]."','".$punkty[form][tel]."',
			'".$punkty[form][fax]."','".$punkty[form][status]."','".$punkty[form][www]."')";
		pg_exec($db,$sql);
		echo "<div align=\"center\"><strong>Dane zostaly wprowadzone</strong></div><br><br>";
		}else{
		$sql = "UPDATE punkty_sprzedazy_nl SET
			nazwa = '".$punkty[form][nazwa]."',
			adres = '".$punkty[form][adres]."',
			kod = '".$punkty[form][kod]."',
			miasto = '".$punkty[form][miasto]."',
			tel = '".$punkty[form][tel]."',
			fax = '".$punkty[form][fax]."',
			status = '".$punkty[form][status]."',
			www = '".$punkty[form][www]."'
			WHERE
			id = '".$punkty[form][id]."'";
		pg_exec($db,$sql);
		echo "<div align=\"center\"><strong>Dane zostaly zmienione</strong></div><br><br>";
		}
	}

echo "</fieldset>";
echo "<div align=\"right\">sid: ".$WEBTD->sid."</div>";
?>