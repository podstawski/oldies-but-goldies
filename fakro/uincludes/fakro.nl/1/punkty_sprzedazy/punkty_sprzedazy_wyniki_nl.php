<?
global $SZUKAJ_PUNKTU, $list;

$LIST = $list;
$szukaj=$SZUKAJ_PUNKTU;

if(!$size) $size=25;
if(!is_array($szukaj) && !strlen($tylko_dla)) return;
if(strlen($szukaj[miasto])) $cond.= " AND miasto ~* '".addslashes(stripslashes($szukaj[miasto]))."'";
if(strlen($szukaj[kod])) $cond.= " AND lpad(kod,2) = '".addslashes(stripslashes($szukaj[kod]))."'";

#$fakrodb->debug=1;
$sql = "SELECT * FROM punkty_sprzedazy_nl WHERE status='1' $cond ORDER BY nazwa,kod";
$res = $fakrodb->execute($sql);

if(!$LIST[ile]) {
	$query="SELECT count(id) AS c FROM punkty_sprzedazy_nl WHERE status='1' $cond ";
	$res2 = $fakrodb->execute($query);
	parse_str(ado_explodename($res2,0));
	$LIST[ile]=$c;
	}

if($KAMELEON_MODE)
	$self.="&SZUKAJ_PUNKTU[miasto]=".$SZUKAJ_PUNKTU[miasto]."&SZUKAJ_PUNKTU[kod]=".$SZUKAJ_PUNKTU[kod];
	else
	$self.="?SZUKAJ_PUNKTU[miasto]=".$SZUKAJ_PUNKTU[miasto]."&SZUKAJ_PUNKTU[kod]=".$SZUKAJ_PUNKTU[kod];

$navi=$size?navi($self,$LIST,$size):"";

if(strlen($navi))
	$res = $fakrodb->SelectLimit($sql,$size,$LIST[start]+0);
	else
	$res = $fakrodb->Execute($sql);

#$fakrodb->debug=0;

if(!$res->RecordCount()) {
	echo "<br><br><div align=\"center\"><strong>Geen dealers gevonden die voldoen aan ingeveoerde criteria.</strong></div>";
	return;
	}

echo "$navi<br><br>
	<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
	<tbody>";
for($i=0; $i < $res->RecordCount(); $i++) {
	parse_str(ado_explodename($res,$i));
	echo "
		<TR class=\"".(($i && ($i%2))?"even":"odd")."\">
			<TD class=\"name\" valign=\"top\">".(($www)?"<a href=\"http://".$www."\" target=\"_blank\"><img src=\"".$INCLUDE_PATH."/punkty_sprzedazy/i_www.gif\" align=\"right\" width=\"19\" height=\"28\" border=\"0\"></a>":"")."".stripslashes($nazwa)."</TD>
			<TD valign=\"top\">$adres<br>$kod $miasto</TD>
			<TD valign=\"top\" style=\"font-weight:bold\">$tel<br>$fax</TD>
		</TR>";
		}
echo "</tbody></table>";
?>
