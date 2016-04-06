<?
	global $suid;

	if (!strlen($suid) && strlen($FORM["parent_id"])) $suid = $FORM["parent_id"];

	$where = $self;
	$jakie_sklepy = array();
	if (strlen($suid))
	{
		$where = $more;
		$sql = "SELECT * FROM system_user WHERE su_id = $suid";
		parse_str(query2url($sql));
		$sql = "SELECT ks_sk_id FROM kontrahent_sklep WHERE ks_su_id = $suid";
		$res = $projdb->execute($sql);
		for ($i=0; $i < $res->RecordCount(); $i++)
		{
			parse_str(ado_explodename($res,$i));
			$jakie_sklepy[] = $ks_sk_id;
		}

	}

	$sql = "SELECT * FROM sklep ORDER BY sk_nazwa";
	$res = $projdb->execute($sql);
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$chck = "";
		if (in_array($sk_id,$jakie_sklepy)) $chck = "checked";
		$sklepy.= "<INPUT TYPE=\"checkbox\" NAME=\"SKLEPY[$sk_id]\" value=\"1\" $chck> $sk_nazwa<br>";
	}

	echo "
	<FORM METHOD=POST ACTION=\"$where\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszGrupe\">
	<INPUT TYPE=\"hidden\" name=\"ODDZIAL[id]\" value=\"$suid\">
	<TABLE class=\"sys_table\" width=\"100%\">
	<TR class=\"tabletr\">
		<TD class=\"tabletd\" align=\"right\">Nazwa firmy:</TD>
		<TD class=\"tabletd\"><INPUT TYPE=\"text\" class=\"sys_input\" NAME=\"ODDZIAL[nazwa]\" value=\"$su_nazwisko\"></TD>
	</TR>
	<TR class=\"tabletr\">
		<TD class=\"tabletd\" align=\"right\">Strona gГѓwna firmy:</TD>
		<TD class=\"tabletd\"><INPUT TYPE=\"text\" class=\"sys_input\" NAME=\"ODDZIAL[strona]\" value=\"$su_email\"></TD>
	</TR>
	<TR class=\"tabletr\">
		<TD class=\"tabletd\" align=\"right\" valign=\"top\">Sklepy:</TD>
		<TD class=\"tabletd\" valign>$sklepy</TD>
	</TR>
	<TR class=\"tabletr\">
		<TD class=\"tabletd\" align=\"center\"><INPUT TYPE=\"button\" value=\"Anuluj\" onClick=\"document.goBackForm.submit()\" class=\"sys_button\"></TD>
		<TD class=\"tabletd\" align=\"center\"><INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"sys_button\"></TD>
	</TR>
	</TABLE>
	</FORM>
	<FORM METHOD=POST ACTION=\"$next\" name=\"goBackForm\">	
	</FORM>
	";
/*
	<TR>
		<TD align=\"right\">Grupy uprawnienieё:</TD>
		<TD>$uprawnienia</TD>
	</TR>
*/

//	global $oddzial_id;
	$oddzial_id = $su_id;
	$su_id = "";
?>
