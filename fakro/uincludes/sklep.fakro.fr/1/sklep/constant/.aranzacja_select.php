<?

if ($ver==10) return;

global $kolor, $typ, $ssid;
global $WEBTD;

if ($ssid == $WEBTD->sid)
{
	$costxt = "kolor=$kolor&typ=$typ";

	$sql = "UPDATE webtd SET costxt = '$costxt' WHERE sid = $ssid AND server = $SERVER_ID
			AND lang = '$lang' AND ver = $ver";
	$kameleon_adodb->execute($sql);
}

parse_str($costxt);

$sql = "SELECT ka_nazwa, ka_id FROM kategorie WHERE ka_parent IS NULL ORDER BY ka_nazwa";

$res = $adodb->execute($sql);

$sel_kolor = "<select name=\"kolor\"><option>Kolor</option>";
$sel_typ = "<select name=\"typ\"><option>Typ</option>";

for ($i=0; $i < $res->RecordCount(); $i++)
{
	parse_str(ado_explodename($res,$i));
	$sel_kolor.= "<option value=\"$ka_id\" ".($kolor==$ka_id?"selected":"").">$ka_nazwa</option>";
	$sel_typ.= "<option value=\"$ka_id\" ".($typ==$ka_id?"selected":"").">$ka_nazwa</option>";
}

$sel_kolor.= "</select>";
$sel_typ.= "</select>";

$ret.= "<table class=\"asel\">";
$ret.= "<tr>";
$ret.= "<td>Wybierz</td>";
$ret.= "<td>";
$ret.= $sel_kolor;
$ret.= "</td>";
$ret.= "<td>";
$ret.= $sel_typ;
$ret.= "</td>";
$ret.= "<td>";
$ret.= "<INPUT TYPE=\"submit\" value=\"Zapisz\">";
$ret.= "</td>";
$ret.= "</tr>";
$ret.= "</table>";

echo "<FORM METHOD=POST ACTION=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"ssid\" value=\"".$WEBTD->sid."\">".$ret."</FORM>";
?>