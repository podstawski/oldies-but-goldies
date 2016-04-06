<?
include($INCLUDE_PATH.'/google_maps/config_maps.php');

global $WEBTD, $punkty, $list;

/*
echo '<pre>';
print_r($WEBTD);
echo '</pre>';
*/
echo "
<fieldset style=\"width:99%; margin-left:2px;\">
<legend>Punkty sprzedazy GOOGLE MAPS wyszukiwarka (FAKRO)</legend>
<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
<tr>
	<td>
	SID - ".$WEBTD->sid."<br>
	</td>
</tr>
</table>
</fieldset>
<br/>";

echo "
<div align=\"right\">
<TABLE>
<TR>
<form method=post action=\"$self\">
	<td><INPUT TYPE=\"text\" size=\"30\" NAME=\"punkty[szukaj]\" value=\"".$punkty['szukaj']."\">
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

if($punkty['pole'] == "szukaj") {
	$LIST = $list;
	if(!$size) $size = 25;
	if($punkty['szukaj']) {
		$cond.= " WHERE ";
		$cond.= "(nazwa LIKE '".addslashes(stripslashes($punkty['szukaj']))."%' OR nazwa LIKE '".addslashes(stripslashes(ucfirst($punkty['szukaj'])))."%' ";
		$cond.= " OR ";
		$cond.= "nazwa LIKE '%".addslashes(stripslashes($punkty['szukaj']))."%' OR nazwa LIKE '%".addslashes(stripslashes(ucfirst($punkty['szukaj'])))."%' ) ";
		}
	
	$lista = $DB_GOOGLE_MAPS->fetch_assoc($DB_GOOGLE_MAPS->query("SELECT * FROM punkty_sprzedazy_nl $cond ORDER BY nazwa,kod LIMIT ".$size." OFFSET ".($LIST['start']+0)." "));
	$lista_count = $DB_GOOGLE_MAPS->getvalues($DB_GOOGLE_MAPS->query("SELECT count(id) AS total FROM punkty_sprzedazy_nl $cond"));
	$LIST['ile'] = $lista_count['total'];
	
	if($KAMELEON_MODE)
		$self_navi.= $self."&punkty[pole]=szukaj&punkty[szukaj]=".$punkty['szukaj'];
		else
		$self_navi.= $self."?punkty[pole]=szukaj&punkty[szukaj]=".$punkty['szukaj'];
	
	$navi=$size?navi($self_navi,$LIST,$size):"";
	
	if(!$lista_count['total']) {
		echo "<br><br><div align=\"center\"><strong>Brak punktów sprzeda¿y spe³niaj±cych podane kryteria.</strong></div>";
		return;
		}
	
	echo "$navi<br><br>
	<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"1\" width=\"100%\"><tbody>";
	$i = 0;
	foreach($lista as $key => $value) {
		echo "
		<TR class=\"".(($i && ($i%2))?"even":"odd")."\">
			<TD class=\"name\" valign=\"top\">".(($lista[$key]['www'])?"<a href=\"http://".$lista[$key]['www']."\" target=\"_blank\"><img src=\"".$INCLUDE_PATH."/punkty_sprzedazy/i_www.gif\" align=\"right\" width=\"19\" height=\"28\" border=\"0\"></a>":"")."<a href=\"$self&punkty[view]=1&punkty[id]=".$lista[$key]['id']."\">".stripslashes($lista[$key]['nazwa'])."</a></TD>
			<TD valign=\"top\">".$lista[$key]['adres']."<br>".$lista[$key]['kod']." ".$lista[$key]['miasto']."</TD>
			<TD valign=\"top\" style=\"font-weight:bold\">".$lista[$key]['tel']."<br>".$lista[$key]['fax']."</TD>
		</TR>";
		$i++;
		}
	echo "</tbody></table>";
	}
if($punkty['view'] == 1) {
	$DB_GOOGLE_MAPS->query("SELECT * FROM punkty_sprzedazy_nl WHERE id='".$punkty['id']."'");
	$DB_GOOGLE_MAPS->getvalues();
	
	echo "<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\"><tbody>";
	echo "
		<tr>
			<td width=\"100\" align=\"right\"><strong>lat:</strong></td>
			<td>".$DB_GOOGLE_MAPS->row['lat']."</td>
		</tr>
		<tr>
			<td width=\"100\" align=\"right\"><strong>lon:</strong></td>
			<td>".$DB_GOOGLE_MAPS->row['lon']."</td>
		</tr>
		<tr>
			<td width=\"100\" align=\"right\"><strong>Nazwa:</strong></td>
			<td>".stripslashes($DB_GOOGLE_MAPS->row['nazwa'])."</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Adres:</strong></td>
			<td>".$DB_GOOGLE_MAPS->row['adres']."</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Kod:</strong></td>
			<td>".$DB_GOOGLE_MAPS->row['kod']."</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Miasto:</strong></td>
			<td>".$DB_GOOGLE_MAPS->row['miasto']."</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Tel:</strong></td>
			<td>".$DB_GOOGLE_MAPS->row['tel']."</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Fax:</strong></td>
			<td>".$DB_GOOGLE_MAPS->row['fax']."</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>WWW:</strong></td>
			<td>".$DB_GOOGLE_MAPS->row['www']."</td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Status:</strong></td>
			<td>".(($DB_GOOGLE_MAPS->row['status'] == 1)?"widoczny":"niewidoczny")."</td>
		</tr>
		</tbody>
		</table>";
	echo "<div align=\"right\"><TABLE><tbody>";
	echo "
		<TR>
		<form method=post action=\"$self\">
			<td><INPUT TYPE=\"submit\" value=\"Edycja\" class=\"k_button\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[pole]\" value=\"edycja\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[id]\" value=\"".$DB_GOOGLE_MAPS->row['id']."\"></td>
		</form>
		<form method=post action=\"$self\">
			<td><INPUT TYPE=\"submit\" value=\"Usun\" class=\"k_button\" onClick=\"return confirm('Czy na pewno chcesz to usunac ?');\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[pole]\" value=\"usun\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[id]\" value=\"".$DB_GOOGLE_MAPS->row['id']."\"></td>
		</form>
		</tr></tbody>
		</TABLE></div>";
	}

if($punkty['pole'] == "edycja") {
	
	if($punkty['id'] != "zmien") {
		$DB_GOOGLE_MAPS->query("SELECT * FROM punkty_sprzedazy_nl WHERE id='".$punkty['id']."'");
		$DB_GOOGLE_MAPS->getvalues();
		
		$punkty['form']['id'] = $DB_GOOGLE_MAPS->row['id'];
		$punkty['form']['lat'] = $DB_GOOGLE_MAPS->row['lat'];
		$punkty['form']['lon'] = $DB_GOOGLE_MAPS->row['lon'];
		$punkty['form']['nazwa'] = $DB_GOOGLE_MAPS->row['nazwa'];
		$punkty['form']['adres'] = $DB_GOOGLE_MAPS->row['adres'];
		$punkty['form']['kod'] = $DB_GOOGLE_MAPS->row['kod'];
		$punkty['form']['miasto'] = $DB_GOOGLE_MAPS->row['miasto'];
		$punkty['form']['tel'] = $DB_GOOGLE_MAPS->row['tel'];
		$punkty['form']['fax'] = $DB_GOOGLE_MAPS->row['fax'];
		$punkty['form']['status'] = $DB_GOOGLE_MAPS->row['status'];
		$punkty['form']['www'] = $DB_GOOGLE_MAPS->row['www'];
		}
	
	echo "<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\"><tbody>";
	echo "<form method=post action=\"$self\">
		<tr>
			<td width=\"100\" align=\"right\"><strong>lat:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][lat]\" value=\"".$punkty['form']['lat']."\"></td>
		</tr>
		<tr>
			<td width=\"100\" align=\"right\"><strong>lon:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][lon]\" value=\"".$punkty['form']['lon']."\"></td>
		</tr>
		<tr>
			<td width=\"100\" align=\"right\"><strong>Nazwa:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][nazwa]\" value=\"".$punkty['form']['nazwa']."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Adres:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][adres]\" value=\"".$punkty['form']['adres']."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Kod:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][kod]\" value=\"".$punkty['form']['kod']."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Miasto:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][miasto]\" value=\"".$punkty['form']['miasto']."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Tel:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][tel]\" value=\"".$punkty['form']['tel']."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Fax:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][fax]\" value=\"".$punkty['form']['fax']."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>WWW:</strong></td>
			<td><INPUT TYPE=\"text\" size=\"40\" NAME=\"punkty[form][www]\" value=\"".$punkty['form']['www']."\"></td>
		</tr>
		<tr>
			<td align=\"right\"><strong>Status:</strong></td>
			<td>
			<select name=\"punkty[form][status]\">
				<option value=\"1\" ".(($punkty['form']['status'] == 1)?"selected":"").">widoczny</option>
				<option value=\"2\" ".(($punkty['form']['status'] == 2)?"selected":"").">niewidoczny</option>
			</select></td>
		</tr>
		</tbody>
		</table>";
	echo "<div align=\"right\"><TABLE><tbody>";
	echo "
		<TR>
			<td><INPUT TYPE=\"submit\" value=\"".(($punkty['form']['id'] == 'new')?"Dodaj":"Edycja")."\" class=\"k_button\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[pole]\" value=\"zmien\">
			<INPUT TYPE=\"hidden\" NAME=\"punkty[form][id]\" value=\"".$punkty['form']['id']."\"></td>
		</form>
		</tr></tbody>
		</TABLE></div>";
	}

if($punkty['pole'] == "usun" && $punkty['id']) {
	$DB_GOOGLE_MAPS->query("DELETE FROM punkty_sprzedazy_nl WHERE id = '".$punkty['id']."'");
	echo "<div align=\"center\"><strong>Dane zostaly usuniete</strong></div><br><br>";
	}
if($punkty['pole'] == "zmien") {
	if($punkty['form']['id'] == 'new') {
		$DB_GOOGLE_MAPS->sqlaction('insert','punkty_sprzedazy_nl','',
			array('lat','lon','nazwa','adres','kod','miasto','tel','fax','status','www'),
			array($punkty['form']['lat'],$punkty['form']['lon'],$punkty['form']['nazwa'],$punkty['form']['adres'],$punkty['form']['kod'],$punkty['form']['miasto'],$punkty['form']['tel'],
			$punkty['form']['fax'],$punkty['form']['status'],$punkty['form']['www']));
		echo "<div align=\"center\"><strong>Dane zostaly wprowadzone</strong></div><br><br>";
		}else{
		$DB_GOOGLE_MAPS->sqlaction('update','punkty_sprzedazy_nl',' id="'.$punkty['form']['id'].'"',
			array('lat','lon','nazwa','adres','kod','miasto','tel','fax','status','www'),
			array($punkty['form']['lat'],$punkty['form']['lon'],$punkty['form']['nazwa'],$punkty['form']['adres'],$punkty['form']['kod'],$punkty['form']['miasto'],$punkty['form']['tel'],
			$punkty['form']['fax'],$punkty['form']['status'],$punkty['form']['www']));
		echo "<div align=\"center\"><strong>Dane zostaly zmienione</strong></div><br><br>";
		}
	}
?>