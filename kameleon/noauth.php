<?
	include ("const.h");
	include ("include/const.h");
?>
<html>

<head>
    <title>KAMELEON: AUTH</title>
    <link href="<?echo $CONST_SKINS_DIR?>/kameleon/kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-2">
</head>
<body bgcolor="#c0c0c0" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>

<table bgcolor="silver" valign=top width="100%" border="1" cellspacing="0" cellpadding="0">
<tr>
	<td class=k_td>
<?
	echo "<div align=right class=k_text><A href='http://www.gammanet.pl/' 
		style='TEXT-DECORATION: none' target=_blank><FONT 
		color=#02461f face=Arial size=2><B>gamma</B></FONT><FONT 
		color=#fc7116 face=Arial size=2><B>net</B></FONT></A> - Web Kameleon: 
		<b>$KAMELEON_VERSION</b>&nbsp;&nbsp;</div>";
	echo "<div align=right class=k_text><A href='javascript:licence()' 
		style='TEXT-DECORATION: none; font-size:7pt' >Copyright &copy; 2001 - 2002 
		Gammanet Sp. z o.o. All right reserved</A></div>";


?>
	<br>
	<br>
	<table bgcolor="silver" valign=top align="center" border="3" cellspacing="0" cellpadding="5">
	<tr>
		<td class=k_td>
		<b>Brak autoryzacji / No rights</b>
		</td>
	</tr>
		<tr>
		<td class=k_td>
		Podane hasło lub użytkownik są nieprawidłowe!. Spróbuj <a href="index.php">jeszcze</a> raz.<br>
		You have no rights or you typed wrong username and password! Try <a href="index.php">again</a>.
		</td>
	</tr>
	</table>
	<br>
	<br>
	
	</td>
</tr>
</table>

</body>
</html>

<script>
function licence()
{
	a=open("/licence.<?echo $KAMELEON_EXT?>","licence",
	"toolbar=0,location=0,directories=0,status=0,menubar=0,scrollbars=0,resizable=0,width=560,height=350");
}

</script>
