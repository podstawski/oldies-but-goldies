<?
	global $suid;
	
//	$sql = "ALTER TABLE system_action_log ADD sal_server integer";
//	pg_exec($db,$sql);

	$where = $self;
	if (strlen($suid))
	{
		$where = $next;
		$sql = "SELECT * FROM system_user WHERE su_id = $suid";
		parse_str(query2url($sql));

		if ($su_aktywny)
			$chck = "checked";

	}
	else
		$chck = "checked";
	
	echo "
	<FORM METHOD=POST ACTION=\"$where\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszGrupe\">
	<INPUT TYPE=\"hidden\" name=\"ODDZIAL[id]\" value=\"$suid\">
	<TABLE border=\"0\" cellspacing=\"0\" cellpading=\"0\" class=\"tf\" width=\"100%\">
	<col class=\"cl\">
	<col class=\"cn\">
	<thead>
	<TR><TD colspan=\"2\">Parametry grup u¿ytkowników</TD></TR>
	</thead>
	<tbody>
	<TR>
		<TD align=\"right\">Nazwa grupy:</TD>
		<TD><INPUT TYPE=\"text\" class=\"ilong\" NAME=\"ODDZIAL[nazwa]\" value=\"$su_nazwisko\"></TD>
	</TR>
	<TR>
		<TD align=\"right\">Adres:</TD>
		<TD><INPUT TYPE=\"text\" class=\"ilong\" NAME=\"ODDZIAL[ulica]\" value=\"$su_ulica\"></TD>
	</TR>
	<TR>
		<TD align=\"right\">Miasto:</TD>
		<TD><INPUT TYPE=\"text\" class=\"ilong\" NAME=\"ODDZIAL[miasto]\" value=\"$su_miasto\"></TD>
	</TR>
	<TR>
		<TD align=\"right\">Kod pocztowy:</TD>
		<TD><INPUT TYPE=\"text\" class=\"ilong\" NAME=\"ODDZIAL[kod]\" size=\"6\" value=\"$su_kod_pocztowy\" ></TD>
	</TR>
	<TR>
		<TD align=\"right\">Aktywny:</TD>
		<TD><INPUT TYPE=\"checkbox\" NAME=\"ODDZIAL[aktywny]\" class=\"cbx\" value=\"1\" $chck></TD>
	</TR>
	</tbody>
	<tfoot>
	<TR>
		<TD align=\"center\"><INPUT TYPE=\"button\" value=\"Anuluj\" onClick=\"document.goBackForm.submit()\" class=\"sys_button\"></TD>
		<TD align=\"center\"><INPUT TYPE=\"submit\" value=\"Zapisz\" class=\"sys_button\"></TD></TR>
	</tfoot>
	</TABLE>
	</FORM>
	<FORM METHOD=POST ACTION=\"$next\" name=\"goBackForm\">	
	</FORM>
	";
/*
	<TR>
		<TD align=\"right\">Grupy uprawnienieñ:</TD>
		<TD>$uprawnienia</TD>
	</TR>
*/

//	global $oddzial_id;
	$oddzial_id = $su_id;
	$su_id = "";
	include ("$INCLUDE_PATH/autoryzacja/adm_user_list.h");
?>