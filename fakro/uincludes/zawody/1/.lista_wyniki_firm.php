<?
global $lang,$ver,$CHARSET_TAB,$WEBTD,$SERVER_ID;
global $_data_zawodow;
global $fakro,$ssid;

$xml = array("fakro"=>$fakro);

if($fakro['ssid'] == $WEBTD->sid) {
	echo '<strong>zapisano zmiany</strong><br>';
	$sql = "UPDATE webtd SET costxt = '".addslashes(serialize($xml))."' WHERE sid = '".$WEBTD->sid."' AND lang = '".$lang."' AND ver = '".$ver."' AND server = '".$SERVER_ID."'";
	$kameleon_adodb->execute($sql);
	$tab = $xml;
	}else{
	$xml = $WEBTD->costxt;
	$tab = unserialize(stripslashes($xml));
	}

$fakro_tab = $tab["fakro"];

/*********************************************************************/
$path = $UFILES.'/wyniki/'.$_data_zawodow;

$sel = '<option value="null">--select------------</option>';
if(is_dir($path)) {
	if($contents = opendir($path)) {
		while(($node = readdir($contents)) !== false) {
			if($node!="." && $node!="..") {
				if(eregi(".*\.xls",$node))
					$sel .= '<option value="'.$node.'" '.($node==$fakro_tab['plik']?"selected":"").'>'.$node.'</option>';
				}
			}
		}
	}

echo "
<fieldset style=\"width:99%; margin-left:2px;\">
<legend>lista wynikow firm zawody (FAKRO) - sid: ".$WEBTD->sid."</legend>
<table>
<colgroup><col style=\"width: 70px\"></col></colgroup>
<form method=post action=\"$self\">
<INPUT TYPE=\"hidden\" NAME=\"fakro[ssid]\" value=\"".$WEBTD->sid."\">
<INPUT TYPE=\"hidden\" NAME=\"fakro[data]\" value=\"".$_data_zawodow."\">
<tr>
	<td>lista:</td>
	<td><select name=\"fakro[plik]\">".$sel."</select></td>
</tr>
<tr>
	<td colspan=\"2\"><INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"k_button\"></td>
</tr>
</form>
</tr>
</table>
</fieldset>";
?>
