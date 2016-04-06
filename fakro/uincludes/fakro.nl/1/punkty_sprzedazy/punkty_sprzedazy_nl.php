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
	<div align=\"right\">
	<table border=\"0\">
	<form method=$method action=\"$self\">
<!--- 	<TR>
		<TD align=\"right\" align=\"right\">Woonplaats :</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"SZUKAJ_PUNKTU[miasto]\" value=\"".htmlspecialchars($SZUKAJ_PUNKTU[miasto])."\"></TD>
	</TR> --->
	<TR>
		<TD align=\"right\" align=\"right\">Postcode (2 cijfers) :</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"SZUKAJ_PUNKTU[kod]\" maxlength=\"2\" value=\"".htmlspecialchars($SZUKAJ_PUNKTU[kod])."\"></TD>
	</TR>
	<TR>
		<TD align=\"right\" colspan=\"2\">
		<INPUT TYPE=\"submit\" value=\"Zoeken\" class=\"k_button\" onClick=\"submit()\"></TD>
	</TR>
	</FORM>
	</TABLE>
	</div>";
?>