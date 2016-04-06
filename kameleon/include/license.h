<?
// autor: Robert Posiadala
// date: 23-04-2003, 15:00

// modification	format - date: user, what?
// 24-04-2003: Michal Basinski, przerobienie z button�w na cbx
// 29-05-2003: Robson, dostosowanie pliku do ver4.x

//michal wersje jezykowe plik�w
if (($lang == 'p') || ($lang == 'i')) $lic_lang = "";
else $lic_lang = "_e";
//end michal

$license_file="license".$lic_lang.".html";
if ($license_action=="no")
{
	$license_file="license_no".$lic_lang.".html";
}

if ($license_action=="yes")
{
	$license_file="license_yes".$lic_lang.".html";
	//tu jeszcze action
	$query="
		UPDATE passwd SET nlicense_agreement_date=".time()." 
		WHERE username='$USERNAME'";
	
	$adodb->Execute($query);


	if ($AUTH_BY_ACL_PLUGIN) 
	{
		$query="
			UPDATE acl_user SET au_license_agreement=".time()." 
			WHERE au_login='$USERNAME'";
		
		$auth_acl->adb->Execute($query);

		unset($auth_acl->session['login.user_info']);

	}
	//	echo $query;return;
	
}


?>
<html>
<head>
    <title>KAMELEON: AUTH</title>
    <link href="<?echo $CONST_SKINS_DIR.'/'.$kameleon->user[skin]?>/kameleon.css" rel="stylesheet" type="text/css">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body style="background-color:#f3f3f3" topmargin=0 leftmargin=0 marginwidth=0 marginheight=0>

<table style="background-color:#f3f3f3" valign=top width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
	<td class=k_td>
	<?include("include/identity.h");?>
	</td>
</tr>
<tr>
	<td class=k_td align="center">
<br>
<?include ("license/$license_file")?>
<?
	$button_begin="";
	$button_quest="";
	if ($license_action=="yes")	{
		$button_begin = "
			<div class=\"license_akcept_ok\">
				<div class=\"btn1\"></div>
				<div class=\"btn2\"><input type=\"submit\" value=\"".label("next")."\" style=\"width: 100px;\"></div>
			</div>";
	}
	
	if (!strlen($license_action))	{
		$button_quest="
			<div class=\"license_akcept\">
				<div class=\"btn1\">
					<input type=\"checkbox\" name=\"cbx_license_action\" value=\"yes\">
					".label("I accept the terms and conditions of the licence")."
				</div>
				<div class=\"btn2\">
					<input type=\"button\" value=\"".label("next")."\" onclick=\"license()\" style=\"width: 100px;\">
				</div>
			</div>
			";
	}

?>


<form name="license_form" method="post">
	<INPUT TYPE="hidden" name="license_action" value="">
<?
	echo $button_quest;
	echo $button_begin;
?>
</form>


<script>
function license()
{
	if (license_form.cbx_license_action.checked)
		license_form.license_action.value='yes';
	else	
		license_form.license_action.value='no';
	license_form.submit();
}
</script>

	</td>
</tr>
</table>

</body>
</html>
