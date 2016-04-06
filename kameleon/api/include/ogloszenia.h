<?

if (strlen($page)==0) return;
include_once("captcha/kcaptcha.php");

$grupa=$page;
if (strlen($api_size)==0)
	$limit=10;
else
	$limit=$api_size;

//if ($api_mode & 1)

if ($api_em)
{
	
	$api_mode = set_cos_api_mode();

	$query="SELECT * FROM ogloszenia_ustawienia WHERE servername='$KEY' LIMIT 1";
//	echo $query;
	$result=$adodb->Execute($query);	
	if ($result->RecordCount())
		parse_str(ado_ExplodeName($result,0));
	$ustawienia="
		<fieldset style=\"width:99%; margin-left:2px;\">
		<legend>".label('Hyde Park')."</legend>
		<form method=post name=api_ogloszenia action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value=''>
		<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
		<tr>
			<td colspan=2>
				".label("Type email to manager")."<br>
				<input style=\"width:100%\" class=\"k_input\" type=text size=30 name=apiOgloszeniaEmail value='$email'>
			</td>
		</tr>
		<tr>
			<td colspan=2>
				".label("Subject")."<br>
				<input style=\"width:100%\" class=\"k_input\" type=text size=30 name=apiOgloszeniaSubject value='$subject'>
			</td>
		</tr>
		<tr>
			<td colspan=2> 
				".label("Dictionary")."<br>
				<textarea style=\"width:100%\" class=\"k_input\" cols=30 rows=5 name=apiOgloszeniaSlownik>$slownik</textarea>
			</td>
		</tr>
		<tr><td>
				<input type='hidden' value='0' name='api_form_opt[0]'>
				<input type='checkbox' value='$sid' name='api_form_opt[2]' ".(($api_mode & 2)?'checked':'')."> ".label("reverse order")."
				<input type='checkbox' value='$sid' name='api_form_opt[16]' ".(($api_mode & 16)?'checked':'')."> ".label("enable captcha")."
				<input type='checkbox' value='$sid' name='api_form_opt[32]' ".(($api_mode & 32)?'checked':'')."> ".label("image captcha")."
			</td>
			<td align=right>
			<input type='submit' class='k_button' value='".label("Save")."' onClick=\"document.api_ogloszenia.api_action.value='apiZapiszOgloszeniaUstawienia'\"></td></tr>
		</table>
		</form>	
		</fieldset>
		<br/>&nbsp;";
	
	echo $ustawienia;

	if ($api_mode & 2)
		$war = "id DESC";
	else
		$war = "id";

	$query="SELECT * FROM ogloszenia WHERE servername='$KEY'
		ORDER BY grupa, $war LIMIT $limit";

	$result=$adodb->Execute($query);

	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$osoba=stripslashes($osoba);
		$opis=stripslashes($opis);
		$delete="<a href=javascript:api_zmiana('$id','apiUsunOgloszenie')>
				<img src=$API_URL/img/ikona-smietnik-b.gif alt='".label("Delete")."' width=12 height=12 border=0></a>";
		$ndeadline=FormatujDate($ndeadline);
		$nwpis=FormatujDate($nwpis);
		echo "$delete $grupa <b>$email, $osoba</b> <i>: $nwpis, ".label("valid to").": $ndeadline</i><br><br>";	
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
	if ( api_action=="apiUsunOgloszenie"  
		&& !confirm("<?echo label("Are you sure")?>")) return;

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
	$query="SELECT * FROM ogloszenia 
		WHERE grupa='$grupa' AND ndeadline>=".time()." AND servername='$KEY'
		ORDER BY $war LIMIT $limit";
	$result=$adodb->Execute($query);
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));
		$osoba=stripslashes($osoba);
		$opis=stripslashes($opis);
		$ndeadline=FormatujDate($ndeadline);
		$nwpis=FormatujDate($nwpis);
		echo "<div class=\"api_ogloszenia_ogloszenie\">";
		echo "<b>$email, $osoba</b><br><i>".label("Written").": <b>$nwpis</b>, ".label("valid to").": <b>$ndeadline</b></i><br>";	
		echo "<div class=\"api_ogloszenia_tresc\">";
		echo nl2br($opis);
		echo "</div>";
		echo "<hr noshade size=1>";
		echo "</div>";
	}
	
	$apiOgloszeniaDeadline=date("d-m-Y",time()+3600*24*14);
	
	echo "
		<form method=post action=$api_next>
		$GLOBAL_HIDDEN
		<input type=hidden name=api_action value=''>
		<input type=hidden name=apiOgloszeniaGrupa value='$grupa'>
		<table border=0 cellspacing=0 cellpadding=3>
		<tr>
			<td>
				".label("Text")."<br>
				<textarea class=api_ogloszenia_input name=apiOgloszeniaOpis rows=5 cols=50></textarea>
			</td>
		</tr>
		<tr>
			<td>
				".label("Your name")."<br>
				<input class=api_ogloszenia_input type=text size=30 name=apiOgloszeniaOsoba value='$api_osoba'>
			</td>
		</tr>
		<tr>
			<td>
				".label("E-mail")."<br>
				<input class=api_ogloszenia_input type=text size=30 name=apiOgloszeniaEmail value='$api_email'>
			</td>
		</tr>
		<tr>
			<td>
				".label("Valid to [dd-mm-yyy]")."<br>
				<input class=api_ogloszenia_input type=text size=12 name=apiOgloszeniaDeadline value='$apiOgloszeniaDeadline'>
			</td>
		</tr>
		<tr>
			<td align=right>
			" . KCAPTCHA::KCAPTCHA() . "
			<input type='submit' class=api_ogloszenia_button  value='".label("Add")."' onClick=\"api_action.value='apiDodajOgloszenie'\">
			</td>
		</tr>
	</table>
	</form>";

} //if
?>
