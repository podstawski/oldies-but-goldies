<?
	$id = $LIST[id];

	if (strlen($id))
	{
		$sql= "SELECT * FROM tr_strefa WHERE tr_strefa_id = $id";
		parse_str(ado_query2url($sql));
	}

	$frm = "
	<FORM METHOD=POST ACTION=\"$next\">	
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszTransportStrefa\">
	<INPUT TYPE=\"hidden\" name=\"TRANSPORT[tr_strefa_id]\" value=\"$tr_strefa_id\">
	<TABLE class=\"ftab\">
	<TR>
		<TD>".sysmsg('Name','post').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[tr_strefa_opis]\" value=\"$tr_strefa_opis\"></TD>
	</TR>
	<TR>
		<TD>".sysmsg('State','post').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[tr_strefa_name]\" value=\"$tr_strefa_name\"></TD>
	</TR>
	<TR>
		<TD>".sysmsg('Tax','post').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[tr_strefa_vat]\" value=\"$tr_strefa_vat\"></TD>
	</TR>
	<TR>
		<TD>".sysmsg('Zone','post').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[tr_strefa_typ]\" value=\"$tr_strefa_typ\"></TD>
	</TR>
	<TR>
		<TD><input class=\"button\" type=\"button\" value=\"".sysmsg('Cancel','post')."\" onClick=\"document.goBackForm.submit()\"></TD>
		<TD><input class=\"button\" type=\"submit\" value=\"".sysmsg('Save','post')."\"></TD>
	</TR>
	</TABLE>
	</FORM>
	<FORM METHOD=POST ACTION=\"$next\" name=\"goBackForm\">	
	</FORM>
	";

	echo $frm;
?>
