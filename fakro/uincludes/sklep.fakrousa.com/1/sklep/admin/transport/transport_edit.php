<?
	$id = $LIST[id];

	if (strlen($id))
	{
		$sql= "SELECT * FROM tr_typ WHERE tr_typ_id = $id";
		parse_str(ado_query2url($sql));
	}

	$frm = "
	<FORM METHOD=POST ACTION=\"$next\">	
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszTransport\">
	<INPUT TYPE=\"hidden\" name=\"TRANSPORT[id]\" value=\"$tr_typ_id\">
	<TABLE class=\"ftab\">
	<TR>
		<TD>".sysmsg('Name','post').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[nazwa]\" value=\"$tr_typ_name\"></TD>
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
