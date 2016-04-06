<?
if (strlen($page)==0) return;
$grupa=$page;
if (strlen($api_size)==0)
	$limit=10;
else
	$limit=$api_size;

$href=$api_next;
include_once("captcha/kcaptcha.php");

if ($api_em)
{
	
	$api_mode = set_cos_api_mode();

	$ustawienia="
		<fieldset style=\"width:99%; margin-left:2px;\">
		<legend>".label('Friend email')."</legend>
		<form method=post name=api_ogloszenia action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value=''>
		<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
		<tr><td>
				<input type='hidden' value='0' name='api_form_opt[0]'>
				<input type='checkbox' value='$sid' name='api_form_opt[1]' ".(($api_mode & 1)?'checked':'')."> ".label("tryb admina")."
				<input type='checkbox' value='$sid' name='api_form_opt[16]' ".(($api_mode & 16)?'checked':'')."> ".label("enable captcha")."
				<input type='checkbox' value='$sid' name='api_form_opt[32]' ".(($api_mode & 32)?'checked':'')."> ".label("image captcha")."
			</td>
			<td align=right>
			<input type='submit' class='k_button' value='".label("Save")."'></td></tr>
		</table>
		</form>	
		</fieldset>
		<br/>&nbsp;";
	
	echo $ustawienia;
}

if ($api_mode & 1) 
{
	$query="SELECT * FROM polecam WHERE servername='$KEY' ORDER BY id LIMIT $limit";
	$result=$adodb->Execute($query);
	echo "<table width=100% border=1 cellspacing=3 cellpadding=0>";
	echo "<tr><td>&nbsp;</td><td>".label("subject")."</td><td>".label("message")."</td><td>".label("answer text")."</td></tr>";
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
      		$dzieki=stripslashes($dzieki);
      		$subject=stripslashes($subject);
      		$opis=stripslashes($msg);

		$delete="<a href=javascript:api_zmiana('$id','apiUsunPolecam')>
				<img src=$API_URL/img/ikona-smietnik-b.gif alt='".label("Delete")."' width=12 height=12 border=0></a>";
		echo "<tr>
		  <td>$delete <b>$id</b></td>
		  <td>".label("From").":<b>$od</b><br>".label("subject").":<b>$subject</b></td>
                  <td>$opis</td>
		  <td>$dzieki</td>
		 </tr>";
	}
	echo "</table>";
   echo "
		<form method=post name=api_polecam action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value='apiDodajPolecam'>
		<table border=0 cellspacing=0 cellpadding=3>
		<tr>
			<td>
				".label("Type your answer text").":<br>
				<textarea class=api_polecam_input name=apiPolecamDzieki rows=3 cols=50></textarea>
			</td>
		</tr>
		<tr>
			<td>".label("Sender email").":<br>
			<input size=40 class=api_polecam_input name=apiPolecamFrom value=''>
		</tr>
		<tr>
			<td>
				".label("Subject").":<br>
				<textarea class=api_polecam_input name=apiPolecamSubject rows=3 cols=50></textarea>
			</td>
		</tr>
		<tr>
			<td>
				".label("Message").":<br>
				<textarea class=api_polecam_input name=apiPolecamMsg rows=3 cols=50></textarea>
			</td>
		</tr>
		<tr>
			<td align=right>
			" . KCAPTCHA::KCAPTCHA() . "
			<input type='submit' class='api_polecam_button' value='".label("Add")."'\">
			</td>
		</tr>
	</table>
	</form>";
?>
<form name=api_zmiany method=post action="<?echo $api_next?>">
 <?echo $GLOBAL_HIDDEN?>
 <input type=hidden name=api_action value="">
 <input type=hidden name=api_id value="">
</form>
<script>
function api_zmiana(pri,api_action)
{
	if ( api_action=="apiUsunPolecam"  
		&& !confirm("<?echo label("Are you sure?")?>")) return;
	document.api_zmiany.api_id.value=pri;	
	document.api_zmiany.api_action.value=api_action;	
	document.api_zmiany.submit();
}
</script>
<?
}
else
{
  echo "
		<table border=0 cellspacing=0 cellpadding=3>
		<form method=post action=$api_next>
		  $GLOBAL_HIDDEN
		  <input type=hidden name=api_action value='apiZapiszPolecam'>
		  <input type=hidden name=serwisID value='$serwisID'>
		  <input type=hidden name=pokazuj value='$pokazuj'>
		<tr>
			<td>
				".label("Friend email")."<br>
				<input class=api_polecam_input type=text size=20 name=apiPolecamEmail value=''>
			</td>
		</tr>
		<tr>
			<td>
				".label("Your name")."<br>
				<input class=api_polecam_input type=text size=20 name=apiPolecamOsoba value=''>
			</td>
		</tr>
		<tr>
			<td align=right>
				" . KCAPTCHA::KCAPTCHA() . "
				<input type='submit' class='api_polecam_button' value='".label("Send")."' onClick=\"api_action.value='apiZapiszPolecam'\">
			</td>
		</tr>
	</form>
	</table>";
 } //if
?>