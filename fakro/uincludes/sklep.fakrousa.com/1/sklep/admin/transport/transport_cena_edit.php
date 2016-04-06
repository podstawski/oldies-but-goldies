<?
# tr_ceny_id tr_typ_id tr_strefa_typ tr_waga_od tr_waga_do tr_objetosc_od tr_objetosc_do tr_ceny 

	$id = $LIST[id];

	if (strlen($id))
	{
		$sql= "SELECT * FROM tr_ceny WHERE tr_ceny_id = $id";
		parse_str(ado_query2url($sql));
	}

	$sql_tr_strefa = "SELECT tr_strefa_typ AS tr_typ FROM tr_strefa GROUP BY tr_strefa_typ ORDER BY tr_strefa_typ";
	$res_tr_strefa = $adodb->execute($sql_tr_strefa);
	
	$sql_tr_typ = "SELECT tr_typ_id AS tr_id, tr_typ_name AS tr_name FROM tr_typ ORDER BY tr_typ_name";
	$res_tr_typ = $adodb->execute($sql_tr_typ);

	$frm = "
	<FORM METHOD=POST ACTION=\"$next\">	
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszTransportCena\">
	<INPUT TYPE=\"hidden\" name=\"TRANSPORT[tr_ceny_id]\" value=\"$tr_ceny_id\">
	<TABLE class=\"ftab\">
	<TR>
		<TD>".sysmsg('Zone','post').":</TD>
		<TD><select name=\"TRANSPORT[tr_strefa_typ]\">
	";
	
	for ($i=0; $i < $res_tr_strefa->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res_tr_strefa,$i));
		($tr_typ==$tr_strefa_typ)? $sel = 'selected': $sel = '';
		$frm .= "<option value=\"$tr_typ\" $sel>$tr_typ</option>";
	}
	
	$frm .= "
		</select></TD>
	</TR>
	<TR>
		<TD>".sysmsg('Name','post').":</TD>
		<TD><select name=\"TRANSPORT[tr_typ_id]\">
	";
	
	for ($i=0; $i < $res_tr_typ->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res_tr_typ,$i));
		($tr_id==$tr_typ_id)? $sel = 'selected': $sel = '';
		$frm .= "<option value=\"$tr_id\" $sel>$tr_name</option>";
	}
	
	$frm .= "
		</select></TD>
	</TR>
	<TR>
		<TD>".sysmsg('Weight MIN','post').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[tr_waga_od]\" value=\"$tr_waga_od\"></TD>
	</TR>
		<TR>
		<TD>".sysmsg('Weight MAX','post').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[tr_waga_do]\" value=\"$tr_waga_do\"></TD>
	</TR>
	
	<TR>
		<TD>".sysmsg('Volume MIN','post').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[tr_objetosc_od]\" value=\"$tr_objetosc_od\"></TD>
	</TR>
		<TR>
		<TD>".sysmsg('Volume MAX','post').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[tr_objetosc_do]\" value=\"$tr_objetosc_do\"></TD>
	</TR>
	
	<TR>
		<TD>".sysmsg('Price','article').":</TD>
		<TD><INPUT class=\"fi\" size=\"50\" TYPE=\"text\" NAME=\"TRANSPORT[tr_ceny]\" value=\"$tr_ceny\"></TD>
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
