<?
	include("$SKLEP_INCLUDE_PATH/autoryzacja/img.h");


	global $killId;

	if (strlen($killId))
	{
		$sql = "DELETE FROM system_grupa WHERE
				sg_id = $killId;
				DELETE FROM system_acl_grupa WHERE
				sag_grupa_id = $killId";
		pg_exec($db,$sql);

	}

	$sql = "SELECT * FROM system_grupa WHERE	
			sg_server = $SERVER_ID
			ORDER BY sg_nazwa";

	$res = 	pg_exec($db,$sql);

	echo "
	<FORM METHOD=POST ACTION=\"$next\">
	<TABLE class=\"sys_table\" width=\"100%\">
	<TR>
	<TD align=\"center\">	
		<INPUT TYPE=\"submit\" value=\"Dodaj grupê uprawnieñ\" class=\"sys_button\">
	</TD></TR></TABLE></FORM>";
	echo "<TABLE class=\"sys_table\" width=\"100%\">
	<col>
	<col>
	<col width=\"10%\">
	<TR class=\"tabletr\">
		<Th class=\"tabletd\"><B>Nr</B></Th>
		<Th class=\"tabletd\"><B>Nazwa grupy</B></Th>
		<Th class=\"tabletd\"><B>[akcje]</B></Th>
	</TR>";

	for ($i=0; $i < pg_numrows($res);$i++)
	{
		parse_str(pg_explodename($res,$i));
		$nr = $i+1;
		$buttons = "<A HREF=\"$next${next_char}sg_id=$sg_id\"><img src=\"$AUTHIMG/i_editmode_n.gif\" border=0></A>";
		$buttons.= "<img onClick=\"killRecord('$sg_id')\" src=\"$AUTHIMG/i_delete_n.gif\" border=0 style=\"cursor:hand\">";
		echo "
			<TR class=\"tabletr\">
				<TD class=\"tabletd\">$nr</TD>
				<TD class=\"tabletd\">$sg_nazwa</TD>
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
		if (confirm('Usun¹æ t¹ grupê ?'))
		{
			document.killForm.killId.value = id;
			document.killForm.submit();
		}
	}
</script>
