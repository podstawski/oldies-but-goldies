<?
	include("$INCLUDE_PATH/autoryzacja/img.h");

	global $killId;
	
	if (strlen($killId))
	{
		$sql = "UPDATE system_user SET su_aktywny = NULL WHERE
				su_id = $killId;
				DELETE FROM system_acl_grupa WHERE
				sag_user_id = $killId";
		pg_exec($db,$sql);
	}

	if (!strlen($oddzial_id)) return;

	$sql = "SELECT * FROM system_user WHERE	
			su_server = $SERVER_ID
			AND su_parent = $oddzial_id
			ORDER BY su_nazwisko";

	$res = 	pg_exec($db,$sql);

//	if (!pg_numrows($res)) return;
	echo "<TABLE border=\"0\" cellspacing=\"0\" cellpading=\"0\" class=\"tl\" width=\"100%\">
	<col width=\"10%\" align=\"right\">
	<col>
	<col>
	<col>
	<col width=\"10%\">
	<col width=\"10%\">
	<tbody>
	<TR>
		<Th>Nr</Th>
		<Th>Login</Th>
		<Th>Osoba</Th>
		<Th>Email</Th>
		<Th>[akcje]</Th>
	</TR>";

	for ($i=0; $i < pg_numrows($res);$i++)
	{
		parse_str(pg_explodename($res,$i));
		$nr = $i+1;

		if (!strlen($su_email)) $su_email = "&nbsp;";
		
		
		$buttons = "<A HREF=\"$more${next_char}suid=$su_id&oddzial_id=$oddzial_id\"><img $disable src=\"$AUTHIMG/i_editmode_n.gif\" border=0></A>";
		$buttons.= "<img $disable onClick=\"killRecord('$su_id')\" src=\"$AUTHIMG/i_delete_n.gif\" border=0 style=\"cursor:hand\">";
		
		$disable = "";
		$tr_class= "";
		if (!strlen($su_aktywny))
		{
			$disable = "disabled";
			$tr_class = " class=\"del\"";
//			$buttons = "&nbsp;";
		}


		echo "
			<TR $disable$tr_class>
				<TD $disable>$nr</TD>
				<TD $disable>$su_login</TD>
				<TD $disable>$su_imiona $su_nazwisko</TD>
				<TD $disable><A HREF=\"mailto:$su_email\">$su_email</A></TD>
				<TD $disable style=\"height:23px\">$buttons</TD>
			</TR>";
	}
	echo "</tbody>";
	echo "<tfoot><tr><td colspan=\"5\">";
	
	echo "
	<FORM METHOD=POST ACTION=\"$more\">
	<INPUT TYPE=\"hidden\" name=\"oddzial_id\" value=\"$oddzial_id\">
	<INPUT TYPE=\"submit\" class=\"sys_button\" value=\"Dodaj u¿ytkownika\">
	</FORM>
	";
		
	echo "</td></tr></tfoot>";
	echo "</table>";

	echo "
	<FORM METHOD=POST ACTION=\"$self\" name=\"killForm\">
	<INPUT TYPE=\"hidden\" name=\"killId\">
	<INPUT TYPE=\"hidden\" name=\"suid\" value=\"$oddzial_id\">
	</FORM>
	";
?>
<script>
	function killRecord(id)
	{
		if (confirm('Usun¹æ tego u¿ytkownika ?'))
		{
			document.killForm.killId.value = id;
			document.killForm.submit();
		}
	}
</script>