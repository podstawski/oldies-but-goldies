<?
global $WEBTD, $MAILF_SEND_FORM, $ssid;

$xml = array("MAILF_SEND_FORM"=>$MAILF_SEND_FORM);

if($ssid == $WEBTD->sid) {
	$sql = "UPDATE webtd SET costxt = '".addslashes(serialize($xml))."' WHERE sid = $ssid AND lang = '$lang' AND ver = $ver AND server = $SERVER_ID";
	$adodb->execute($sql);
	$tab = $xml;
	}else{
	$xml = $WEBTD->costxt;
	$tab = unserialize(stripslashes($xml));
	}

$MAILF_SEND_FORM = $tab["MAILF_SEND_FORM"];

echo "<fieldset style=\"width:99%; margin-left:2px;\">
<legend>WysyГanie odpowiedzi zwrotnej (FAKRO)</legend>
	<form method=post action=\"$self\">
	<INPUT TYPE=\"hidden\" NAME=\"ssid\" value=\"".$WEBTD->sid."\">
	<TABLE>
	<TR>
		<TD>Od:</TD>
		<TD><INPUT TYPE=\"text\" size=\"50\" NAME=\"MAILF_SEND_FORM[mailfrom]\" value=\"".$MAILF_SEND_FORM[mailfrom]."\"></TD>
	</TR>
	<TR>
		<TD>Temat:</TD>
		<TD><INPUT TYPE=\"text\" size=\"50\" NAME=\"MAILF_SEND_FORM[subject]\" value=\"".$MAILF_SEND_FORM[subject]."\"></TD>
	</TR>
	<TR>
		<TD>TreЖц maila:</TD>
		<TD><textarea cols=\"70\" rows=\"5\" NAME=\"MAILF_SEND_FORM[tresc]\">".htmlspecialchars($MAILF_SEND_FORM[tresc])."</textarea></TD>
	</TR>
	<TR>
		<TD colspan=2><INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"k_button\"></TD>
	</TR>
	</TABLE>
	</form></fieldset>
	";
	
echo "<div align=\"right\">sid: ".$WEBTD->sid."</div>";
?>
