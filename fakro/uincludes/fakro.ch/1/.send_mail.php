<?
	global $WEBTD, $MAILF, $ssid;

	$xml = array("MAILF"=>$MAILF);
	
	if ($ssid == $WEBTD->sid)
	{
		$sql = "UPDATE webtd SET costxt = '".addslashes(serialize($xml))."' WHERE sid = $ssid
				AND lang = '$lang' AND ver = $ver AND server = $SERVER_ID";
		$adodb->execute($sql);
		$tab = $xml;
	}
	else
	{
		$xml = $WEBTD->costxt;
		$tab = unserialize(stripslashes($xml));
	}

	$MAILF = $tab["MAILF"];

	echo "
	<form method=post action=\"$self\">
	<INPUT TYPE=\"hidden\" NAME=\"ssid\" value=\"".$WEBTD->sid."\">
	<TABLE>
	<TR>
		<TD>Tyu³:</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"MAILF[subject]\" value=\"".$MAILF[subject]."\"></TD>
	</TR>
	<TR>
		<TD>Adres:</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"MAILF[mailto]\" value=\"".$MAILF[mailto]."\"></TD>
	</TR>
	<TR>
		<TD colspan=2><INPUT TYPE=\"submit\" value=\"Zapisz\"></TD>
	</TR>
	</TABLE>
	</form>
	";
?>