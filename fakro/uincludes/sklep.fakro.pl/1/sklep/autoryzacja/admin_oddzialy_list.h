<?
	include("$SKLEP_INCLUDE_PATH/autoryzacja/img.h");

	global $killId;
	
	if (strlen($killId))
	{
		$sql = "DELETE FROM system_user WHERE
				su_id = $killId OR su_parent = $killId;
				DELETE FROM system_acl_grupa WHERE
				sag_user_id = $killId";
		pg_exec($db,$sql);
	}

	$sql = "SELECT * FROM system_user WHERE	
			su_server = $SERVER_ID AND su_parent IS NULL
			ORDER BY su_data_dodania";

	$res = 	pg_exec($db,$sql);

	echo "
	<FORM METHOD=POST ACTION=\"$next\">
	<TABLE width=\"100%\" class=\"sys_table\">
	<TR>
		<TD align=\"center\"><INPUT TYPE=\"submit\" value=\"Dodaj firmę\" class=\"sys_button\"></TD>
	</TR>
	</TABLE>
	</FORM>
	";
	echo "
	<TABLE class=\"sys_table\" width=\"100%\">
	<col>
	<col>
	<col width=\"10%\">
	<TR class=\"tabletr\">
		<Th class=\"tabletd\">Nr</Th>
		<Th class=\"tabletd\">Nazwa firmy</Th>
		<Th class=\"tabletd\">[akcje]</Th>
	</TR>";

	for ($i=0; $i < pg_numrows($res);$i++)
	{
		parse_str(pg_explodename($res,$i));
		$nr = $i+1;
		$buttons = "<A HREF=\"$next${next_char}suid=$su_id\"><img alt=\"edytuj\" src=\"$AUTHIMG/i_editmode_n.gif\" border=0></A>";
		$buttons.= "<img onClick=\"killRecord('$su_id')\" alt=\"usuń\" src=\"$AUTHIMG/i_delete_n.gif\" border=0 style=\"cursor:hand\">";
		if (!strlen($su_miejscowosc))$su_miejscowosc = "&nbsp;";
		else $rodzaj = "&nbsp;";
		echo "
			<TR class=\"tabletr\">
				<TD class=\"tabletd\">$nr</TD>
				<TD class=\"tabletd\">$su_nazwisko</TD>
				<TD class=\"tabletd\">$buttons</TD>
			</TR>";
	}
	echo "</table>";
	echo "
	<FORM METHOD=POST ACTION=\"$self\" name=\"killForm\">
	<INPUT TYPE=\"hidden\" name=\"killId\">
	</FORM>
	";
?>
<script>
	function killRecord(id)
	{
		if (confirm('Usunšć ten oddział ?'))
		{
			document.killForm.killId.value = id;
			document.killForm.submit();
		}
	}
</script>
