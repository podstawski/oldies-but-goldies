<?
if (strlen($page)==0) return;
$grupa=$page;
if (strlen($api_size)==0)
	$limit=10;
else
	$limit=$api_size;
include_once("captcha/kcaptcha.php");


$href=$api_href;


$sql="SELECT costxt FROM webtd WHERE sid=".$API_REQUEST[sid];
parse_str(ado_query2url($sql));

if ($api_em)
{
	
	$api_mode = set_cos_api_mode();

	if ($api_form_mode)
	{
		$costxt=$api_form_mode;
		$sql="UPDATE webtd SET costxt=$api_form_mode WHERE sid=".$API_REQUEST[sid];
		$adodb->execute($sql);
	}
	



	$ustawienia="
		<fieldset style=\"width:99%; margin-left:2px;\">
		<legend>".label('Friend email')."</legend>
		<form method=post name=api_ogloszenia action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value=''>
		<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
		<tr><td>
				<input type='hidden' value='0' name='api_form_opt[0]'>
				<input type='text' value='$costxt' size=3 name='api_form_mode'> ".label("choice")."
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

if ($api_em) 
{
	$query="SELECT * FROM kontakt WHERE  servername='$KEY'	ORDER BY id LIMIT $limit";
	$result=$adodb->Execute($query);
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$subject=stripslashes($subject);
		$opis=stripslashes($opis);

		$delete="<a href=javascript:api_zmiana('$id','apiUsunKontakt')>
				<img src=$API_URL/img/ikona-smietnik-b.gif alt='".label("Delete")."' width=12 height=12 border=0></a>";
		echo "$delete <b>$id</b> $email<i> $subject</i><br>$opis";	
		echo "<hr noshade size=1>";
	}
   echo "
		<form method=post name=api_kontakt action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value=''>
		<table border=0 cellspacing=0 cellpadding=3>
		<tr>
			<td>
				".label("Type your answer text")."<br>
				<textarea class=api_kontakt_input name=apiKontaktOdpowiedz rows=8 cols=50></textarea>
			</td>
		</tr>
		<tr>
			<td>
				".label("Subject")."<br>
				<textarea class=api_kontakt_input name=apiKontaktSubject rows=8 cols=50></textarea>
			</td>
		</tr>
		<tr>
			<td>
				".label("E-mail")."<br>
				<input class=api_kontakt_input type=text size=30 name=apiKontaktEmail value=''>
			</td>
		</tr>
		<tr>
			<td align=right>
			   " . KCAPTCHA::KCAPTCHA() . "
			<input type='submit' class='api_kontakt_button' value='".label("Add new contact")."' onClick=\"document.api_kontakt.api_action.value='apiDodajKontakt'\">
			</td>
		</tr>
	</table>
	</form>";
?>
<form name=api_zmiany method=post action=<?echo $api_next?>>
 <?echo $GLOBAL_HIDDEN?>
 <input type=hidden name=api_action value="">
 <input type=hidden name=api_id value="">
</form>
<script>
function api_zmiana(pri,api_action)
{
	if ( api_action=="apiUsunKontakt"  
		&& !confirm("<?echo label("Are you sure ?")?>")) return;

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
		<input type=hidden name=api_action value=''>
		<input type=hidden name=page value='$page'>	
		<tr>
			<td>
				".label("E-mail")."<br>
				<input class=api_kontakt_input type=text size=20 name=apiKontaktEmail value=''>
			</td>
		</tr>
		<tr>
			<td>
				".label("Subject")."<br>
				<input class=api_kontakt_input type=text size=20 name=apiKontaktSubject value=''>
			</td>
		</tr>
		<tr>
			<td>
				".label("Text")."<br>
				<textarea class=api_kontakt_input name=apiKontaktOpis rows=5 cols=20></textarea>
			</td>
		</tr>
		<tr>
			<td align=right>
			   " . KCAPTCHA::KCAPTCHA() . "
			<input type='submit' class='api_kontakt_button' value='".label("Send")."' onClick=\"api_action.value='apiZapiszKontakt'\">
			</td>
		</tr>
	</form>
	</table>";
 } //if
?>