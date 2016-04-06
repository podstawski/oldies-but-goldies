<script language="javascript">
function showModFun()
{
}
</script>
<?
	if (strlen($costxt)) 
	{
		$tab = explode(";",$costxt);
		$grupa = $tab[0];
		$wypisz_from = $tab[1];
		$outpage = $tab[2];
		$szablon_out = $tab[3];
		$nl_info = $tab[4];
		$inpage = $tab[5];
		$szablon_in = $tab[6];
		$host_addres = $tab[7];
	}

	echo"
		<table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
		<TR>
			<td colspan=2 class=k_formtitle >".label("Properities").":</td>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Group")."</TD>
			<TD valign=\"top\"><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"NLETTER[group]\" value=\"$grupa\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Mail from")."</TD>
			<TD valign=\"top\"><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"NLETTER[outmail]\" value=\"$wypisz_from\" style=\"width:170px\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Host")."</TD>
			<TD valign=\"top\"><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"NLETTER[host_addr]\" value=\"$host_addres\" style=\"width:170px\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Out confirm page")."</TD>
			<TD valign=\"top\"><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"NLETTER[outpage]\" value=\"$outpage\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("In confirm page")."</TD>
			<TD valign=\"top\"><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"NLETTER[inpage]\" value=\"$inpage\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Message_out action")."</TD>
			<TD valign=\"top\"><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"NLETTER[msgpage]\" value=\"$szablon_out\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Message_in action")."</TD>
			<TD valign=\"top\"><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"NLETTER[msginpage]\" value=\"$szablon_in\"></TD>
		</TR>
		<TR class=k_form>
			<TD valign=\"top\">".label("Info")."</TD>
			<TD><TEXTAREA NAME=\"NLETTER[info]\" id=\"aq_answer\" ROWS=\"6\" COLS=\"30\" class=\"k_input\">$nl_info</TEXTAREA></TD>
		</tr>
		<TR class=k_form>
			<td colspan=\"2\" align=\"right\"><img src=\"img/i_save_n.gif\" style=\"cursor:hand\" onClick=\"ZapiszZmiany()\"></td>
		</tr>
		</table>
	";
?>