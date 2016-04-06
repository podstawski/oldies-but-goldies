<script language="javascript">
function showModFun()
{
}
</script>
<?
	include "$INCLUDE_PATH/.api/winiso.h";
	
	$da_page = $costxt;
	
	echo win2iso("
	<table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
	<TR class=k_form>
			<td colspan=2 class=k_formtitle>".label("Options")."</td>
	</TR>
	<TR class=k_form>
		<TD>".label("Number of page containting messages")."</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"NLETTER[strona]\" class=\"k_input\" value=\"$da_page\"></TD>
	</TR>
	<TR class=k_form>
		<td colspan=\"2\" align=\"right\"><img src=\"img/i_save_n.gif\" style=\"cursor:hand\" onClick=\"ZapiszZmiany()\"></td>
	</TR>

	</TABLE>");
?>
