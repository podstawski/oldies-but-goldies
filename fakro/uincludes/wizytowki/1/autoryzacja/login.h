<?
global $err, $goto, $NO_SESSION_DESTROY, $LOGIN_PHP;

if ($page != 0 && is_array($AUTH) && $AUTH[id] > 0)
{
	$LOGIN_PHP = str_replace("err=1","",$LOGIN_PHP)."action=logout";
	echo "Zalogowany: ".$AUTH[imiona]." ".$AUTH[nazwisko].". <h1><A style=\"font-size:12px\" HREF=\"$LOGIN_PHP\">Wyloguj</A></h1>";
	return;
}

//if (strlen($goto)) $next = kameleon_href('','',$goto);
if (strlen($goto)) $next = urldecode($goto);
echo "
	<FORM METHOD=\"POST\" ACTION=\"$next\" id=\"LOGIN_FORM\" onLoad=\"this.ilong.focus()\" onSubmit=\"return checkForm_$sid()\">
	
	<table cellspacing=1 cellpadding=3 class=\"tf\" border=\"0\" >   
	<col align=\"right\" class=\"left\">
	<col align=\"left\" class=\"right\">";

if (!strlen($costxt))
{
	if ($goto)
	$add = "<TR><TD colspan=2 class=\"sys_err\">$_AUTH_ERROR_LOGIN_REQ</TD></TR>";

	if ($err)
	$add.= "<TR><TD colspan=2 class=\"sys_err\">$_AUTH_ERROR_WRONG_LOGIN</TD></TR>";
}


echo "
	<tbody>
	<TR>
		<TD class=\"sys_tdlabel\" nowrap>login </TD>
		<TD><INPUT TYPE=\"text\" class=\"ilong\" NAME=\"AUTH[user]\" id=\"c_username_$sid\" ></TD>
	</TR>
	<TR>
		<TD class=\"sys_tdlabel\">hasło </TD>
		<TD><INPUT TYPE=\"password\" class=\"ilong\" NAME=\"AUTH[password]\" id=\"c_password_$sid\" ></TD>
	</TR>
	

	<tr><td></td>
	<td>
	<INPUT TYPE=\"submit\" value=\"Login\" class=\"sys_button\">
	</td></tr>
	".$add."
	</tbody>
	</TABLE>
	</FORM>
";
	$sql = "SELECT COUNT(*) AS ile FROM system_user";
	parse_str(query2url($sql));

?>
<script>

function checkForm_<? echo $sid ?>()
{
	<?
	if (!$ile) 
		echo "return true";
	?>

	var login=document.all['c_username_<? echo $sid ?>'];
	var pass=document.all['c_password_<? echo $sid ?>'];
	if (login.value.length>0 && pass.value.length>0)
		return true;
	else
	{
		alert ('Wpisz login i hasło !');
		return false;
	}
}
</script>


<?
return;
if (!$NO_SESSION_DESTROY)
	@session_destroy();
?>
