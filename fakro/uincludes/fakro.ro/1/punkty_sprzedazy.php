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
	<TABLE class=\"tf\" align=\"right\">
	<form method=$method action=\"$self\">
	<TR>
		<TD>Nazwa (lub fragment) firmy :</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" NAME=\"SZUKAJ_PUNKTU[nazwa]\" value=\"".htmlspecialchars($SZUKAJ_PUNKTU[nazwa])."\"></TD>
	</TR>
	<TR>
		<TD>Nazwa (lub fragment) miasta :</TD>
	</TR>
	<TR>
		<TD><INPUT TYPE=\"text\" NAME=\"SZUKAJ_PUNKTU[miejsce]\" value=\"".htmlspecialchars($SZUKAJ_PUNKTU[miejsce])."\"></TD>
	</TR>
	<TR>
		<TD>
		<p onClick=\"submit()\" style=\"cursor:pointer;font-weight: bold;\">Wyszukaj&nbsp;&nbsp;<img src=\"$IMAGES/h1.gif\" align=\"absmiddle\"></p>		
		</TD>
	</TR>
	</FORM>
	</TABLE>
	";

?>