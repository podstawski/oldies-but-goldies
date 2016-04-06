<?
if ($SERVICE!="news")
{
	$api_action="";
	return;
}
$api_action="";
?>
<form method=post action=<?echo $api_next?> >
<?echo $GLOBAL_HIDDEN?>
<input type=hidden name=api_action value="apiZapiszAktual">
<input type=hidden name=api_pri value="<?echo $api_pri?>">
<table border=1 cellspacing=0 cellpadding=3 width=100% bordercolor=#000000>
<?
$query="SELECT * FROM webaktual WHERE servername='$KEY' AND pri=$api_pri";
$res=$adodb->Execute($query);
parse_str(ado_ExplodeName($res,0));
$headline=stripslashes($headline);
$akt=stripslashes($akt);
$more=stripslashes($more);
$nd_akt=FormatujDate($nd_akt);
?>
<tr>
	<td align=right><?echo label("Date")?></td>
	<td>
		<input type=text size=11 name=api_data value="<?echo $nd_akt?>">
	</td>
</tr>
<tr>
	<td align=right><?echo label("Headline")?></td>
	<td>
	<input type=text size=50 name=api_headline value="<?echo $headline?>">
	</td>
</tr>
<tr>
	<td align=right><?echo label("Intro")?></td>
	<td>
		<textarea name=api_akt rows=10 style="width:100%"><?echo $akt?></textarea>
	</td>
</tr>
<tr>
	<td align=right><?echo label("More")?></td>
	<td>
		<textarea name=api_more rows=10 style="width:100%"><?echo $more?></textarea>
	</td>
</tr>
<tr>
	<td align=right><?echo label("Foto image")?></td>
	<td><input type=text size=50 name=api_img value="<?echo $img?>"></td>
</tr> 
</table>
<br>
&nbsp; <input type=submit value="<?echo label("Save")?>">
</form>