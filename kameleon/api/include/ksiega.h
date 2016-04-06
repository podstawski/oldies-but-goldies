<?
if (strlen($page)==0) return;
include_once("include/forumfun.h");
include_once("captcha/kcaptcha.php");

$KsiegaGrupa=$page;

if (strlen($api_size)==0)
	$limit=10;
else
	$limit=$api_size;


if ($api_em)
{
	$api_mode = set_cos_api_mode();

	$query="SELECT * FROM ksiega_ustawienia WHERE servername='$KEY' LIMIT 1";
	$result=$adodb->Execute($query);	
	if ($result->RecordCount())
		parse_str(ado_ExplodeName($result,0));
	$ustawienia="
		<fieldset style=\"width:99%; margin-left:2px;\">
		<legend>".label('Hyde Park')."</legend>
		<form method=post name=api_ksiega  action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value=''>
		<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
		<tr>
			<td colspan=2> 
				".label("Type email to moderator")."<br>
				<input style=\"width:100%\" class=k_input type=text size=30 name=apiKsiegaEmail value='$email'>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				".label("Subject")."<br>
				<input style=\"width:100%\" class=k_input type=text size=30 name=apiKsiegaSubject value='$subject'>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				".label("Dictionary")."<br>
				<textarea style=\"width:100%\" class=k_input cols=30 rows=5 name=apiKsiegaSlownik>$slownik</textarea>
			</td>
		</tr>
		<tr>
			<td>
				<input type='hidden' value='0' name='api_form_opt[0]'>
				<input type='checkbox' value='$sid' name='api_form_opt[2]' ".(($api_mode & 2)?'checked':'')."> ".label("reverse order")."
				<input type='checkbox' value='$sid' name='api_form_opt[16]' ".(($api_mode & 16)?'checked':'')."> ".label("enable captcha")."<br>
				<input type='checkbox' value='$sid' name='api_form_opt[32]' ".(($api_mode & 32)?'checked':'')."> ".label("image captcha")."<br>
			</td>
			<td align=right>
			<input type='submit' class='k_button' value='".label("Save")."' onClick=\"document.api_ksiega.api_action.value='apiZapiszKsiegaUstawienia'\">
			</td>
		</tr>
		</table>
		</form>	
		</fieldset>
		<br/>&nbsp;	";
	echo $ustawienia;



	if ($api_mode & 2)
    	$war = "id DESC";
	else
		$war = "id";

	$query="SELECT * FROM ksiega WHERE servername='$KEY' ORDER BY grupa, $war LIMIT $limit";

	$result=$adodb->Execute($query);

	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$delete="<a href=javascript:api_zmiana('$id','apiUsunKsiega')>
				<img src=$API_URL/img/ikona-smietnik-b.gif alt='".label("Delete")."' width=12 height=12 border=0></a>";
		if (!$nwpis) $nwpis=$wpis;
		$wpis=FormatujDate($nwpis);
		$osoba=stripslashes($osoba);
		$opis=stripslashes($opis);
		echo "$delete $grupa <b>$email, $osoba </b><br><i>".label("Written").": $wpis</i><br><br>";	
		echo nl2br($opis);
		echo "<hr noshade size=1>";
	}
?>
<form name=api_zmiany method=post action=<?echo $api_next?>>
 <?echo $GLOBAL_HIDDEN?>
 <input type=hidden name=api_action value="">
 <input type=hidden name=api_id value="">
</form>

<script>
function api_zmiana(pri,api_action)
{
	if ( api_action=="apiUsunKsiega"  
		&& !confirm("Jesteœ pewien, ¿e chcesz usun¹æ")) return;

	document.api_zmiany.api_id.value=pri;	
	document.api_zmiany.api_action.value=api_action;	
	document.api_zmiany.submit();
}
</script>
<?
}
else
{
  if ($api_mode & 2)
    $war = "id DESC";
   else
    $war = "id";
	
	$query="SELECT * FROM ksiega WHERE servername='$KEY' AND grupa='$KsiegaGrupa' ORDER BY $war  LIMIT $limit";

	//echo $query;
	$result=$adodb->Execute($query);
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		if (!$nwpis) $nwpis=$wpis;
		$wpis=FormatujDate($nwpis);
		$osoba=stripslashes($osoba);
		$opis=stripslashes($opis);
		echo "<div class=\"api_ksiega_ogloszenie\">";
		echo "<b><a href=mailto:$email>$email</a> $osoba</b><br><i>Wpisano: <b>$wpis</b></i><br>";	
		echo "<div class=\"api_ksiega_tresc\">";
		echo nl2br($opis);
		echo "</div>";
		echo "<hr noshade size=1>";
		echo "</div>";
	}
   echo "
		<form method=post action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value=''>
		<input type=hidden name=apiKsiegaGrupa value='$KsiegaGrupa'>
		<table border=0 cellspacing=0 cellpadding=3>
		<tr>
			<td>
				".label("Text")."<br>
				<textarea class=api_ksiega_input name=apiKsiegaOpis rows=5></textarea>
			</td>
		</tr>
		<tr>
			<td>
				".label("Your name")."<br>
				<input class='api_ksiega_input' name='apiKsiegaOsoba' size=30 value=\"$api_osoba\">
			</td>
		</tr>
		<tr>
			<td>
				".label("Type your email")."<br>
				<input class=api_ksiega_input type=text size=30 name=apiKsiegaEmail value='$api_email'>
			</td>
		</tr>
		<tr>
			<td align=right>
				" . KCAPTCHA::KCAPTCHA() . "
				<input type='submit' class=api_ksiega_button  value='".label("Add")."' onClick=\"api_action.value='apiDodajKsiega'\">
			</td>
		</tr>
	</table>
	</form>";

} //if
?>