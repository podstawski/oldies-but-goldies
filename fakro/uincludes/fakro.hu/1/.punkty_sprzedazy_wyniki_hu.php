<?
	$tylko_dla = $_POST["tylko_dla"];
	$_sid = $_POST["_sid"];
	
	global $WEBTD;
	
	if($_sid == $WEBTD->sid) {
		$sql = "UPDATE webtd SET costxt = 'tylko_dla=$tylko_dla' WHERE sid = $_sid AND lang = '$lang' AND ver = $ver AND server = $SERVER_ID";
		$adodb->execute($sql);
		}else{
		parse_str($costxt);
		}
	
	$sql = "SELECT id_woj FROM punkty_sprzedazy_hu WHERE type = 'S' GROUP BY id_woj ORDER BY id_woj";
	$res = $fakrodb->execute($sql);
	
	$sel = "<option value=\"\">wszystkie</option>";
	
	for($i=0; $i < $res->RecordCount(); $i++) {
		parse_str(ado_explodename($res,$i));
		$sel.= "<option value=\"$id_woj\" ".($id_woj==$tylko_dla?"selected":"").">$id_woj</option>";
		}
	
	echo "
		<form method=post action=\"$self\">
		<INPUT TYPE=\"hidden\" name=\"_sid\" value=\"".$WEBTD->sid."\">
		Wyszukuj punktów sprzedaży tylko dla danego województwa:<br>
		<select name=\"tylko_dla\" onChange=\"submit()\">$sel</select>
		</form>";
?>