<?

global $err, $goto, $login_form_displayed;

if ($login_form_displayed || $AUTH[id]>0) return;
$login_form_displayed=1;

if (strlen($goto)) $next = urldecode($goto);
echo "
	<FORM METHOD=\"POST\" ACTION=\"$next\" id=\"LOGIN_FORM\" onSubmit=\"return checkForm_$sid()\" class=\"loginForm\">
	
	<table>   
	<col align=\"right\" width=\"50%\">
	<col align=\"left\" width=\"50%\">";

if (!strlen($costxt))
{
	if ($goto)
	$add = "<TR><TD colspan=2 class=\"sys_err\">$_AUTH_ERROR_LOGIN_REQ</TD></TR>";

	if ($err)
	$add.= "<TR><TD colspan=2 class=\"sys_err\">$_AUTH_ERROR_WRONG_LOGIN</TD></TR>";
}


echo "
	<TR><TD class=\"sys_tdlabel\" nowrap>".sysmsg("user code","system")." </TD>
		<TD><INPUT TYPE=\"text\" class=\"sys_input\" NAME=\"AUTH[user]\" id=\"c_username_$sid\"></TD></TR>
	<TR><TD class=\"sys_tdlabel\">".sysmsg("password","system")." </TD>
		<TD><INPUT TYPE=\"password\" class=\"sys_input\" NAME=\"AUTH[password]\" id=\"c_password_$sid\"></TD></TR>
	<tr><td></td>
		<td><INPUT TYPE=\"submit\" value=\"".sysmsg("login","system")."\" class=\"sys_button\"></td></tr>
	".win2iso($add)."
	</TABLE>
	</FORM>";
	$sql = "SELECT COUNT(*) AS ile FROM system_user";
	parse_str(query2url($sql));

?>
<script>

<? if ($cos) {?>
var obj = document.all['c_username_<? echo $sid ?>'];
obj.focus();
<? } ?>
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
		alert ('Wpisz login i has³o !');
		return false;
	}
}
</script>

