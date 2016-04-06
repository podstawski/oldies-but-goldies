<?
	
	$tylko_dla = $_POST["tylko_dla"];
	$_sid = $_POST["_sid"];

	global $WEBTD;

	if ($_sid == $WEBTD->sid)
	{
		$sql = "UPDATE webtd SET costxt = 'tylko_dla=$tylko_dla' WHERE sid = $_sid
				AND lang = '$lang' AND ver = $ver AND server = $SERVER_ID";
		$adodb->execute($sql);
	}
	else
		parse_str($costxt);

	$sql = "SELECT ps_wojewodztwo FROM punkty_sprzedazy 
			GROUP BY ps_wojewodztwo ORDER BY ps_wojewodztwo";

	$res = $fakrodb->execute($sql);

	
	$sel= "<option value=\"\">wszystkie</option>";

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$sel.= "<option value=\"$ps_wojewodztwo\" ".($ps_wojewodztwo==$tylko_dla?"selected":"").">$ps_wojewodztwo</option>";
	}
	
	echo "
	<form method=post action=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"_sid\" value=\"".$WEBTD->sid."\">
	Wyszukuj punktѓw sprzedaПy tylko dla danego wojewѓdztwa:<br>
	<select name=\"tylko_dla\" onChange=\"submit()\">$sel</select>
	</form>
	";
?>