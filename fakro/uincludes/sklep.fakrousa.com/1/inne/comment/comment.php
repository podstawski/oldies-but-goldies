<?
global $db,$lang,$ver,$CHARSET_TAB,$WEBTD,$SERVER_ID,$SERVER_NAME,$mode,$id_comment;

$adodb->debug=0;
$KEY = $SERVER_NAME;
$KsiegaGrupa=$page;

/************************************************************************/
if($action_send == 'dodaj') {
	
	if(strlen($apiKsiegaOsoba)==0) $error['apiKsiegaOsoba'] = 1;
	if(strlen($apiKsiegaRating)==0) $error['apiKsiegaRating'] = 1;
	if(strlen($apiKsiegaOpis)==0) $error['apiKsiegaOpis'] = 1;
	
	if(!$error) {
		$query = "INSERT INTO ksiega2 (opis,osoba,email,ranking,wpis,grupa,servername) VALUES ('$apiKsiegaOpis','$apiKsiegaOsoba','$apiKsiegaEmail','$apiKsiegaRating',".time().",'$apiKsiegaGrupa','$KEY')";
		$adodb->Execute($query);
		
		$query = "DELETE FROM ksiega2_mail_key WHERE mail_key='$mail_key'";
		$adodb->Execute($query);
		}
	}
/************************************************************************/

$query = "SELECT * FROM ksiega2 WHERE servername='$KEY' ORDER BY grupa, id DESC LIMIT 25";
$result = $adodb->execute($query);

echo '<table width=100% cellspacing="2" cellpadding="2" border="0" class=api_ksiega2>';
echo '<col width=30%>';
echo '<col width=70%>';

for($i=0; $i < $result->RecordCount(); $i++) {
	parse_str(ado_explodename($result,$i));
	$wpis=date("Y-m-d",$wpis);
	$osoba=stripslashes($osoba);
	$opis=stripslashes($opis);
	$ranking=stripslashes($ranking);
	
echo '<tr>';
echo '<td><img src='.$INCLUDE_PATH.'/inne/comment/images/comment_star'.$ranking.'.gif width=100 height=16 border=0></td>';
echo '<td align=right><strong>'.sysmsg("Date").':</strong> '.$wpis.'&nbsp;&nbsp;&nbsp; <strong>'.sysmsg("Rated by").':</strong> '.$osoba.'</td>';
echo '</tr>';
echo '<tr>';
echo '<td colspan=2>'.nl2br($opis).'<br><hr noshade size=1><br><br></td>';
echo '</tr>';

	}
echo '</table>';

/************************************************************************/
$query = "SELECT * FROM ksiega2_mail_key WHERE mail_key='".$mail_key."'";
$res = $adodb->execute($query);
if($res->RecordCount()) {
?>

<br><br>
<strong>Thank you for taking the time to drop us a line.</strong>
<br><br>

<form method=post action=<?=$self;?>> 
<input type=hidden name=api_action value=''>
<input type=hidden name=action_send value='dodaj'>
<input type=hidden name=mail_key value='<?=$mail_key;?>'>
<input type=hidden name=apiKsiegaGrupa value='<?=$KsiegaGrupa; ?>'>
<table border=0 cellspacing=2 cellpadding=3 class=api_ksiega2>
<tr>
	<td align=right>
	<? if($error['apiKsiegaOsoba']) { ?>
	<font color="#FF0000"><?=sysmsg("Your name");?>:</font>
	<? }else{ ?>
	<?=sysmsg("Your name");?>:
	<? } ?>*
	</td>
	<td><input class='api_ksiega2_input' name='apiKsiegaOsoba' size=30 value="<?=$apiKsiegaOsoba; ?>"></td>
</tr>
<tr>
	<td align=right><?=sysmsg("Type your email");?>:</td>
	<td><input class=api_ksiega2_input type=text size=30 name=apiKsiegaEmail value='<?=$apiKsiegaEmail; ?>'></td>
</tr>
<tr>
	<td align=right>
	<? if($error['apiKsiegaRating']) { ?>
	<font color="#FF0000"><?=sysmsg("Rating");?>:</font>
	<? }else{ ?>
	<?=sysmsg("Rating");?>:
	<? } ?>*
	</td>
	<td>
	
<table width=100% cellspacing=5 cellpadding=5 class=api_ksiega2>
<tr>
	<td align=center><input type="radio" name="apiKsiegaRating" value="1" <?=($apiKsiegaRating == 1)?'checked':'';?>><br>Bad</td>
	<td align=center><input type="radio" name="apiKsiegaRating" value="2" <?=($apiKsiegaRating == 2)?'checked':'';?>><br>Not Bad</td>
	<td align=center><input type="radio" name="apiKsiegaRating" value="3" <?=($apiKsiegaRating == 3)?'checked':'';?>><br>Moderate</td>
	<td align=center><input type="radio" name="apiKsiegaRating" value="4" <?=($apiKsiegaRating == 4)?'checked':'';?>><br>Almost Excellent</td>
	<td align=center><input type="radio" name="apiKsiegaRating" value="5" <?=($apiKsiegaRating == 5)?'checked':'';?>><br>Excellent</td>
</tr>
</table>
	
	</td>
</tr>
<tr>
	<td align=right valign=top>
	<? if($error['apiKsiegaOpis']) { ?>
	<font color="#FF0000"><?=sysmsg("Feedback");?>:</font>
	<? }else{ ?>
	<?=sysmsg("Feedback");?>:
	<? } ?>*
	</td>
	<td><textarea class=api_ksiega2_input name=apiKsiegaOpis rows=5><?=$apiKsiegaOpis;?></textarea></td>
</tr>
<tr>
	<td colspan=2 align=right><input type='submit' class=api_ksiega2_button  value='<?=sysmsg("Send Feedback");?>' onClick="api_action.value='apiDodajComment'"></td>
	<td></td>
</tr>
</table>
</form>
<? } ?>