<style>
.tf input {
	border:1px solid black;
	width:180px;
	}

.tf .bt {
	border:1px solid black;
	width:100px;
}
</style>
<?
$method=($KAMELEON_MODE?"POST":"GET");

global $SZUKAJ_PUNKTU;
echo "
	<div align=\"left\">
	<TABLE border=\"0\">
	<form method=$method action=\"$self\">
	<TR>
		<TD>Cég neve (vagy névtöredék):</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" NAME=\"SZUKAJ_PUNKTU[nazwa]\" value=\"".htmlspecialchars($SZUKAJ_PUNKTU[nazwa])."\"></TD>
	</TR>
	<TR>
		<TD>Helység neve (vagy névtöredék):</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" NAME=\"SZUKAJ_PUNKTU[miejsce]\" value=\"".htmlspecialchars($SZUKAJ_PUNKTU[miejsce])."\"></TD>
	</TR>
	<TR>
		<TD>
		<INPUT TYPE=\"submit\" value=\"Keresés\" class=\"k_button\" onClick=\"submit()\"></TD>
		</TD>
	</TR>
	</FORM>
	</TABLE>
	</div>";
?>