<?
	$id = $LIST[id];

	if (strlen($id))
	{
		$sql= "SELECT * FROM poczta WHERE po_id = $id";
		parse_str(ado_query2url($sql));
	}

	$frm = "
	<FORM METHOD=POST ACTION=\"$next\">	
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszPoczte\">
	<INPUT TYPE=\"hidden\" name=\"POCZTA[id]\" value=\"$po_id\">
	<TABLE class=\"ftab\">
	<TR>
		<TD>Nazwa:</TD>
		<TD><INPUT class=\"fi\" TYPE=\"text\" NAME=\"POCZTA[nazwa]\" value=\"$po_nazwa\"></TD>
	</TR>
	<TR>
		<TD>Cena netto:</TD>
		<TD><INPUT class=\"fi\" TYPE=\"text\" NAME=\"POCZTA[netto]\" value=\"$po_cena_nt\"></TD>
	</TR>
	<TR>
		<TD>Cena brutto:</TD>
		<TD><INPUT class=\"fi\" TYPE=\"text\" NAME=\"POCZTA[brutto]\" value=\"$po_cena_br\"></TD>
	</TR>
	<TR>
		<TD>Darmo powy¿ej</TD>
		<TD><INPUT class=\"fi\" TYPE=\"text\" NAME=\"POCZTA[powyzej]\" value=\"$po_darmo_powyzej\"></TD>
	</TR>
	<TR>
		<TD><input class=\"fb\" type=\"button\" value=\"Anuluj\" onClick=\"document.goBackForm.submit()\"></TD>
		<TD><input class=\"fb\" type=\"submit\" value=\"Zapisz\"></TD>
	</TR>
	</TABLE>
	</FORM>
	<FORM METHOD=POST ACTION=\"$next\" name=\"goBackForm\">	
	</FORM>
	";

	echo $frm;
?>
