<?
	if ( "$FORM[sid]" == "$WEBTD->sid")
	{
		$pos=strpos($costxt,"&nazwa=");
		if ($pos) $costxt=substr($costxt,0,$pos);

		$costxt.= "&nazwa=".$FORM[importer_nazwa]."&wymagany=".$FORM[importer_wymagany];
		$sql = "UPDATE webtd SET costxt='$costxt' WHERE sid = $WEBTD->sid";
		$kameleon_adodb->execute($sql);
	}	

	parse_str($costxt);

	if ($wymagany) $ch = "checked";

	echo "
	<FORM METHOD=POST ACTION=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"form[sid]\" value=\"".$WEBTD->sid."\">
	<TABLE>
	<TR>
		<TD><INPUT TYPE=\"text\" NAME=\"form[importer_nazwa]\" value=\"$nazwa\"></TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"form[importer_wymagany]\" $ch value=\"1\"> importer wymagany</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"submit\" value=\"Zapisz\"></TD>
	</TR>
	</TABLE>

	</FORM>	";
?>
