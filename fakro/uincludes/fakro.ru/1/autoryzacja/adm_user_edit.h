<?
	global $suid, $oddzial_id;

	if (!strlen($oddzial_id)) return;

	if (strlen($suid))
	{
		$sql = "SELECT * FROM system_user WHERE su_id = $suid";
		parse_str(query2url($sql));

		$sql = "SELECT * FROM system_acl_grupa WHERE
				sag_user_id = $suid AND sag_server = $SERVER_ID";
		$res = pg_exec($db,$sql);
		$this_rights = array();
		for ($i=0; $i < pg_numrows($res); $i++)
		{
			parse_str(pg_explodename($res,$i));
			$this_rights[] = $sag_grupa_id;
		}
	}
	else
	{
		$sql = "SELECT su_login FROM system_user WHERE su_id = $oddzial_id";
		parse_str(query2url($sql));
		$su_login.="-";
	}

	$sql = "SELECT * FROM system_grupa
			WHERE sg_server = $SERVER_ID
			ORDER BY sg_nazwa";
	$res = pg_exec($db,$sql);
	$uprawnienia = "";
	for ($i=0;$i <pg_numrows($res);$i++)
	{
		parse_str(pg_explodename($res,$i));
		$chck = "";
		if (is_array($this_rights))
			if (in_array($sg_id,$this_rights))
				$chck = "checked";
		$uprawnienia.= "<INPUT TYPE=\"checkbox\" NAME=\"GRUPY[$sg_id]\" $chck value=\"1\" class=\"cbx\"> $sg_nazwa<br>";
	}


	$query = "SELECT su_nazwisko AS grupa 
				FROM system_user WHERE 
				su_server = $SERVER_ID
				AND su_id = $oddzial_id";
	if (strlen($oddzial_id))
		parse_str(query2url($query));
	if ($su_aktywny)$chck="checked";
	
	echo "
	<FORM METHOD=POST ACTION=\"$next\" onSubmit=\"return validateForm(this)\" name=\"userForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszUsera\">
	<INPUT TYPE=\"hidden\" name=\"AUSER[id]\" value=\"$suid\">
	<INPUT TYPE=\"hidden\" name=\"suid\" value=\"$oddzial_id\">
	<TABLE border=\"0\" cellspacing=\"0\" cellpading=\"0\" class=\"tf\" width=\"100%\">
	<col class=\"cl\">
	<col class=\"cn\">
	<thead>
	<TR><TD colspan=\"2\">Parametry uПytkownika</TD></TR>
	</thead>
	<tbody>
	<TR>
		<TD align=\"right\">Imiona</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"AUSER[imiona]\" class=\"ilong\" id=\"imiona\" value=\"$su_imiona\"></TD>
	</TR>
	<TR>
		<TD align=\"right\">Nazwisko</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"AUSER[nazwisko]\" class=\"ilong\" id=\"osoba\" value=\"$su_nazwisko\"></TD>
	</TR>
	<TR>
		<INPUT TYPE=\"hidden\" name=\"AUSER[parent]\" value=\"$oddzial_id\">
		<TD height=\"27\" align=\"right\" valign=\"top\">Grupa:</TD>
		<TD valign=\"top\">$grupa</TD>
	</TR>
	<TR>
		<TD align=\"right\">Adres email:</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"AUSER[email]\" class=\"ilong\" id=\"email\" value=\"$su_email\"></TD>
	</TR>
	<TR>
		<TD align=\"right\">Login:</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"AUSER[login]\" class=\"ilong\" value=\"$su_login\"></TD>
	</TR>
	<TR>
		<TD align=\"right\">HasГo:</TD>
		<TD><INPUT TYPE=\"password\" NAME=\"AUSER[pass]\" class=\"ilong\" value=\"\"></TD>
	</TR>
<!-- 
	<TR>
		<TD valign=\"top\" align=\"right\">Dozwolone adresy IP (oddzienane przecinkami):</TD>
		<TD><TEXTAREA NAME=\"AUSER[ip]\" value=\"$su_ip\" cols=\"20\" rows=\"2\"></textarea></TD>
	</TR>
 -->
	<TR>
		<TD align=\"right\">Uprawnienia:</TD>
		<TD>$uprawnienia</TD>
	</TR>
	<TR>
		<TD align=\"right\">Aktywny:</TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"AUSER[aktywny]\" value=\"1\" class=\"cbx\" $chck></TD>
	</TR>
	</tbody>
	<tfoot>
	<TR>
		<TD><INPUT TYPE=\"button\" value=\"Anuluj\" class=\"sys_button\" onClick=\"document.goBackForm.submit()\"></TD>
		<TD><INPUT TYPE=\"submit\" class=\"sys_button\" value=\"Zapisz\"></TD>
	</TR>
	</tfoot>
	</TABLE>
	</FORM>
	<FORM METHOD=POST ACTION=\"$next\" name=\"goBackForm\">	
	<INPUT TYPE=\"hidden\" name=\"suid\" value=\"$oddzial_id\">
	</FORM>
	";
?>
<script>
	function validateForm(obj)
	{
		return true;
	}
</script>