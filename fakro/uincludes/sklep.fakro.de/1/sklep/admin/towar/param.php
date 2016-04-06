<?
	$LIST[id]=$CIACHO[admin_to_id];


	$to_id = $LIST[id];
	if (!strlen($to_id)) return;

	$sql = "SELECT * FROM towar_parametry 
			WHERE tp_to_id = $to_id";

	parse_str(ado_query2url($sql));


	$table = "
	<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<tbody>";

	if (sysmsg("title_tp_a","towar-param") != "title_tp_a")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_a","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_a]\" value=\"$tp_a\" style=\"width:150px\">
		".sysmsg("tp_a_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_b","towar-param") != "title_tp_b")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_b","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_b]\" value=\"$tp_b\" style=\"width:150px\"> ".sysmsg("tp_b_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_c","towar-param") != "title_tp_c")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_c","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_c]\" value=\"$tp_c\" style=\"width:150px\"> ".sysmsg("tp_c_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_d","towar-param") != "title_tp_d")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_d","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_d]\" value=\"$tp_d\" style=\"width:150px\">
		".sysmsg("tp_d_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_l","towar-param") != "title_tp_l")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_l","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_l]\" value=\"$tp_l\" style=\"width:150px\">
		".sysmsg("tp_l_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_r1","towar-param") != "title_tp_r1")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_r1","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_r1]\" value=\"$tp_r1\" style=\"width:150px\">
		".sysmsg("tp_r1_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_r2","towar-param") != "title_tp_r2")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_r2","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_r2]\" value=\"$tp_r2\" style=\"width:150px\">
		".sysmsg("tp_r2_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_o","towar-param") != "title_tp_o")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_o","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_o]\" value=\"$tp_o\" style=\"width:150px\">
		".sysmsg("tp_o_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_gatunek","towar-param") != "title_tp_gatunek")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_gatunek","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_gatunek]\" value=\"$tp_gatunek\" style=\"width:250px\">
		".sysmsg("tp_gatunek_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_stan","towar-param") != "title_tp_stan")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_stan","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_stan]\" value=\"$tp_stan\" style=\"width:250px\">
		".sysmsg("tp_stan_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_m_m","towar-param") != "title_tp_m_m")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_m_m","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_m_m]\" value=\"$tp_m_m\" style=\"width:150px\">
		".sysmsg("tp_mmm_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_m_m2","towar-param") != "title_tp_m_m2")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_m_m2","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_m_m2]\" value=\"$tp_m_m2\" style=\"width:150px\">
		".sysmsg("tp_mmm_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_m_szt","towar-param") != "title_tp_m_szt")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_m_szt","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_m_szt]\" value=\"$tp_m_szt\" style=\"width:150px\">
		".sysmsg("tp_mmm_jm","towar-param")."</TD>
	</TR>";

	if (sysmsg("title_tp_m_jm","towar-param") != "title_tp_m_jm")
	$table.= "
	<TR>
		<TD class=\"c2\">".sysmsg("title_tp_m_jm","towar-param").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[tp_m_jm]\" value=\"$tp_m_jm\" style=\"width:150px\">
		".sysmsg("tp_mmm_jm","towar-param")."</TD>
	</TR>";

	$table.= "
	</tbody>
	<tfoot>
	<TR>
		<TD class=\"c4\" colspan=2><INPUT TYPE=\"submit\" class=\"button\" value=\"".sysmsg("Save","admin")."\"></TD>
	</TR>
	</tfoot>
	</table>";

	echo "
	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\" name=\"gobackform\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	</form>
	<FORM METHOD=POST ACTION=\"$self\" method=\"POST\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ZapiszTowarParametry\">
	<INPUT TYPE=\"hidden\" name=\"seteditmode\" value=\"0\">
	<INPUT TYPE=\"hidden\" name=\"form[id]\" value=\"$to_id\">
	$table
	</FORM>
	";
?>
