<?
	include("$INCLUDE_PATH/autoryzacja/img.h");


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

	
	echo "<TABLE border=\"0\" cellspacing=\"0\" cellpading=\"0\" class=\"tl\" width=\"100%\">
	<col width=\"10%\" align=\"right\">
	<col align=\"center\">
	<col width=\"10%\" align=\"right\">
	<tbody>
	<TR>
		<Th>Nr</Th>
		<Th>Nazwa grupy</Th>
		<Th>[akcje]</Th>
	</TR>";

	for ($i=0; $i < pg_numrows($res);$i++)
	{
		parse_str(pg_explodename($res,$i));
		$nr = $i+1;
		$buttons = "<A HREF=\"$next${next_char}sg_id=$sg_id\"><img src=\"$AUTHIMG/i_editmode_n.gif\" border=0></A>";
		$buttons.= "<img onClick=\"killRecord('$sg_id')\" src=\"$AUTHIMG/i_delete_n.gif\" border=0 style=\"cursor:hand\">";
		echo "
			<TR>
				<TD>$nr</TD>
				<TD>$sg_nazwa</TD>
				<TD>$buttons</TD>
			</TR>";
	}
	echo "</tbody>";
	echo "<tfoot><tr><td colspan=\"4\">";
	
	echo "
	<FORM METHOD=POST ACTION=\"$next\" class=\"ex_table\">
	<INPUT TYPE=\"submit\" value=\"Dodaj grupê uprawnieñ\" class=\"sys_button\">
	</FORM>";
	
	echo "</td></tr></tfoot>";
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