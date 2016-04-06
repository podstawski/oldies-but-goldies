<?
global $lang,$ver,$CHARSET_TAB,$WEBTD,$SERVER_ID;
global $WEBTD, $_SEND_FORM_MODULE, $ssid;

$xml = array("_SEND_FORM_MODULE"=>$_SEND_FORM_MODULE);

if($ssid == $WEBTD->sid) {
	$sql = "UPDATE webtd SET costxt = '".addslashes(serialize($xml))."' WHERE sid = $ssid AND lang = '$lang' AND ver = $ver AND server = $SERVER_ID";
	$adodb->execute($sql);
	$tab = $xml;
	}else{
	$xml = $WEBTD->costxt;
	$tab = unserialize(stripslashes($xml));
	}

$_SEND_FORM_MODULE = $tab["_SEND_FORM_MODULE"];

echo "
<fieldset style=\"width:99%; margin-left:2px;\">
<legend>ZGŁOSZENIE serwisowe - crm (FAKRO) - sid: ".$WEBTD->sid."</legend>
<form method=post action=\"$self\">
	<INPUT TYPE=\"hidden\" NAME=\"ssid\" value=\"".$WEBTD->sid."\">
	<TABLE>
	<TR>
		<TD>Od:</TD>
		<TD><INPUT TYPE=\"text\" size=\"50\" NAME=\"_SEND_FORM_MODULE[mail_from]\" value=\"".$_SEND_FORM_MODULE['mail_from']."\"></TD>
	</TR>
	<TR>
		<TD>Do:</TD>
		<TD><INPUT TYPE=\"text\" size=\"50\" NAME=\"_SEND_FORM_MODULE[mail_to]\" value=\"".$_SEND_FORM_MODULE['mail_to']."\"></TD>
	</TR>
	<TR>
		<TD>Temat:</TD>
		<TD><INPUT TYPE=\"text\" size=\"50\" NAME=\"_SEND_FORM_MODULE[mail_temat]\" value=\"".$_SEND_FORM_MODULE['mail_temat']."\"></TD>
	</TR>
	<TR>
		<TD colspan=2><INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"k_button\"></TD>
	</TR>
	</TABLE>
	</form>
</fieldset>";
?>