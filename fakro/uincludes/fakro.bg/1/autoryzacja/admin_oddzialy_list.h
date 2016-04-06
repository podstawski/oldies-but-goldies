<?
	include("$INCLUDE_PATH/autoryzacja/img.h");

	global $killId, $orderby, $corderby, $cdirect;
	
	if (strlen($killId))
	{
		$sql = "UPDATE system_user SET su_aktywny = NULL WHERE
				su_id = $killId OR su_parent = $killId;
				DELETE FROM system_acl_grupa WHERE
				sag_user_id = $killId";
		pg_exec($db,$sql);
	}
	
	$direct = "ASC";

	if ($corderby == $orderby)
	{
		if ($cdirect == "ASC")
			$direct = "DESC";
		else
			$direct = "ASC";
	}

	if (strlen($corderby) && !strlen($orderby))
		$orderby = $corderby;
	
	if (!strlen($orderby))
		$orderby = "su_login";

	echo "
	<SCRIPT LANGUAGE=\"JavaScript\">
		document.cookie = 'corderby=$orderby;path=/';
		document.cookie = 'cdirect=$direct;path=/';
	</SCRIPT>";

	$sql = "SELECT * FROM system_user WHERE	
			su_server = $SERVER_ID AND su_parent IS NULL
			ORDER BY upper($orderby) $direct";

	$res = 	pg_exec($db,$sql);

	
	echo "
	<TABLE border=\"0\" cellspacing=\"0\" cellpading=\"0\" class=\"tl\" width=\"100%\">
	<col width=\"10%\" align=\"right\">
	<col align=\"center\">
	<col width=\"10%\" align=\"right\"><col width=\"10%\" align=\"center\">
	<tbody>
	<TR>
		<Th>Nr</Th>
		<Th><A HREF=\"$self${next_char}orderby=su_nazwisko\">Nazwa grupy</A></Th>
		<Th>Osoby</Th>
		<Th>[akcje]</Th>
	</TR>";

	for ($i=0; $i < pg_numrows($res);$i++)
	{
		parse_str(pg_explodename($res,$i));
		$nr = $i+1;
		$buttons = "<A HREF=\"$next${next_char}suid=$su_id\"><img alt=\"edytuj\" src=\"$AUTHIMG/i_editmode_n.gif\" border=0></A>";
		$buttons.= "<img onClick=\"killRecord('$su_id')\" alt=\"usuё\" src=\"$AUTHIMG/i_delete_n.gif\" border=0 style=\"cursor:hand\">";
		if (!strlen($su_miejscowosc))$su_miejscowosc = "&nbsp;";
		else $rodzaj = "&nbsp;";
		if (!strlen($su_login)) $su_login = "&nbsp;";

		$disable = "";
		$tr_class="";
		if (!strlen($su_aktywny))
		{
			$disable = "disabled";
			$tr_class = " class=\"del\"";
//			$buttons = "&nbsp;";
		}

		$sql = "SELECT COUNT(*) AS ile FROM system_user WHERE su_parent = $su_id";
		parse_str(query2url($sql));
		echo "
			<TR $disable$tr_class>
				<TD $disable>$nr</TD>
				<TD $disable>$su_nazwisko</TD>
				<TD $disable>$ile</TD>
				<TD $disable>$buttons</TD>
			</TR>";
	}

	echo "</tbody>";
	echo "<tfoot><tr><td colspan=\"4\">";
	
	echo "
	<FORM METHOD=POST ACTION=\"$next\">
	<INPUT TYPE=\"submit\" value=\"Dodaj grupъ\" class=\"sys_button\">
	</FORM>
	";
	
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
		if (confirm('UsunЙц ten oddziaГ ?'))
		{
			document.killForm.killId.value = id;
			document.killForm.submit();
		}
	}
</script>