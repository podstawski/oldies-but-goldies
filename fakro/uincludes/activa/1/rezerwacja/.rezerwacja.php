<?
global $lang,$ver,$CHARSET_TAB,$WEBTD,$SERVER_ID,$WEBTD,$rezerwacja,$ssid;;


$xml = array("rezerwacja"=>$rezerwacja);

if($ssid == $WEBTD->sid) {
	$sql = "UPDATE webtd SET costxt = '".addslashes(serialize($xml))."' WHERE sid = $ssid AND lang = '$lang' AND ver = $ver AND server = $SERVER_ID";
	$adodb->execute($sql);
	$tab = $xml;
	}else{
	$xml = $WEBTD->costxt;
	$tab = unserialize(stripslashes($xml));
	}

$rezerwacja = $tab["rezerwacja"];

echo "
<fieldset style=\"width:99%; margin-left:2px;\">
<legend>REZERWACJA ONLINE (ACTIVA) - sid: ".$WEBTD->sid."</legend>
<form method=post action=\"$self\">
	<INPUT TYPE=\"hidden\" NAME=\"ssid\" value=\"".$WEBTD->sid."\">
	<TABLE>
	<TR>
		<TD>Adres e-mail:</TD>
		<TD><INPUT TYPE=\"text\" size=\"50\" NAME=\"rezerwacja[mailto]\" value=\"".$rezerwacja['mailto']."\"></TD>
	</TR>
		<TR>
		<TD>Tytul maila w systemie SOHIS:</TD>
		<TD><INPUT TYPE=\"text\" size=\"50\" NAME=\"rezerwacja[subject]\" value=\"".$rezerwacja['subject']."\"></TD>
	</TR>
	<TR>
		<TD>Informacja OK:</TD>
		<TD><textarea cols=\"70\" rows=\"3\" NAME=\"rezerwacja[send_ok]\">".htmlspecialchars($rezerwacja['send_ok'])."</textarea></TD>
	</TR>
	<TR>
		<TD>Informacja ERROR:</TD>
		<TD><textarea cols=\"70\" rows=\"3\" NAME=\"rezerwacja[send_error]\">".htmlspecialchars($rezerwacja['send_error'])."</textarea></TD>
	</TR>
	
	<TR>
		<TD colspan=2><INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"k_button\"></TD>
	</TR>
	</TABLE>
</form>
</fieldset>";
?>