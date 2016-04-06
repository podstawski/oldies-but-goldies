<script language="javascript">
function showModFun()
{
}
</script>
<?
	$c_array = explode(":",$costxt);
	
	for($i=0; $i < count($c_array) ; $i++)
	{
		if (strlen(trim($c_array[$i]))) 
			parse_str($c_array[$i]);
	}

	if ($show_lp) $s1 = "checked";
	if ($show_name) $s2 = "checked";
	if ($show_person) $s3 = "checked";
	if ($show_username) $s4 = "checked";
	if ($show_addres) $s5 = "checked";
	if ($show_zip) $s6 = "checked";
	if ($show_city) $s7 = "checked";
	if ($show_country) $s8 = "checked";
	if ($show_tel) $s9 = "checked";
	if ($show_email) $s10 = "checked";
	if ($show_group) $s101 = "checked";

	if ($edit_name) $s11 = "checked";
	if ($edit_person) $s12 = "checked";
	if ($edit_username) $s13 = "checked";
	if ($edit_pass) $s14 = "checked";
	if ($edit_addres) $s15 = "checked";
	if ($edit_zip) $s16 = "checked";
	if ($edit_city) $s17 = "checked";
	if ($edit_country) $s18 = "checked";
	if ($edit_tel) $s19 = "checked";
	if ($edit_email) $s20 = "checked";
	if ($edit_group) $s201 = "checked";

	if ($validate_name) $v1 = "checked";
	if ($validate_person) $v2 = "checked";
	if ($validate_username) $v3 = "checked";
	if ($validate_addres) $v4 = "checked";
	if ($validate_zip) $v5 = "checked";
	if ($validate_city) $v6 = "checked";
	if ($validate_country) $v7 = "checked";
	if ($validate_tel) $v8 = "checked";
	if ($validate_email) $v9 = "checked";
	if ($validate_group) $v91 = "checked";

	if ($add_ok) $s21 = "checked";
	if ($edit_ok) $s22 = "checked";
	if ($delete_ok) $s23 = "checked";
	if ($sort_ok) $s231 = "checked";
	if ($restrict_ok) $s232 = "checked";
//<INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_group]\" $v91 value=\"1\">
	echo "
	  <table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
		<TR>
			<td colspan=3 class=k_formtitle>".label("Show columns")."</td>
		</TR>
		<TR>
			<td colspan=2 class=k_formtitle>".label("Field name")."</td>
			<td class=k_formtitle>".label("Show")."</td>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Order number").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_lp]\" $s1 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Company name").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_name]\" $s2 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Name and lastname").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_person]\" $s3 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Username").":</TD>
			<TD ><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_username]\" $s4 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Group").":</TD>
			<TD ><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_group]\" $s101 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Address").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_addres]\" $s5 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Zip code").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_zip]\" $s6 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("City").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_city]\" $s7 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Country").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_country]\" $s8 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Telephone number").":</TD>
			<TD ><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_tel]\" $s9 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Email address").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[show_email]\" $s10 value=\"1\"></TD>
		</TR>
		<TR>
			<td colspan=3 class=k_formtitle>".label("Edit fields")."</td>
		</TR>
		<TR>
			<td class=k_formtitle>".label("Field name")."</td>
			<td class=k_formtitle>".label("Edit")."</td>
			<td class=k_formtitle>".label("Validate")."</td>
		</TR>

		<TR class=k_form>
			<TD valign=\"top\">".label("Company name").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_name]\" $s11 value=\"1\"></TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_name]\" $v1 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Name and lastname").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_person]\" $s12 value=\"1\"></TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_person]\" $v2 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Username").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_username]\" $s13 value=\"1\"></TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_username]\" $v3 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Group").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_group]\" $s201 value=\"1\"></TD>
			<TD><B>X</B></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Password").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_pass]\" $s14 value=\"1\"></TD>
			<TD><B>X</B></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Address").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_addres]\" $s15 value=\"1\"></TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_addres]\" $v4 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Zip code").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_zip]\" $s16 value=\"1\"></TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_zip]\" $v5 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("City").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_city]\" $s17 value=\"1\"></TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_city]\" $v6 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Country").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_country]\" $s18 value=\"1\"></TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_country]\" $v7 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Telephone number").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_tel]\" $s19 value=\"1\"></TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_tel]\" $v8 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Email address").":</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_email]\" $s20 value=\"1\"></TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[validate_email]\" $v9 value=\"1\"></TD>
		</TR>
		<TR>
			<td colspan=3 class=k_formtitle>".label("Options")."</td>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Enable - add new user")."</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[add_ok]\" $s21 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Enable - edit user")."</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[edit_ok]\" $s22 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Enable - delete user")."</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[delete_ok]\" $s23 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Enable - sort")."</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[sort_ok]\" $s231 value=\"1\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2 valign=\"top\">".label("Enable - restrict")."</TD>
			<TD><INPUT TYPE=\"checkbox\" NAME=\"CRMUSERS[restrict_ok]\" $s232 value=\"1\"></TD>
		</TR>

		<TR class=k_form>
			<td align=\"right\" colspan=\"3\"><img src=\"img/i_save_n.gif\" onClick=\"ZapiszZmiany()\" style=\"cursor:hand\"></td>
		</TR>

		</table>

	";
?>