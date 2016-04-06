<?
//echo "lang=".$lang;
$defaultlang='e';

$langsel.="<select class=k_select 
		onchange=\"document.location.href=this[this.selectedIndex].value\">";




$LANGS=array('i','p','e','d','r','f','t','s','w');
$kcht=$adodb->GetCookie('KAMELEON_CHARSET_TAB');
if (is_array($kcht)) $LANGS=array_keys($kcht);


for ($i=0;$i<count($LANGS);$i++)
{
	if (!strlen($LANGS[$i])) continue;
	$SetLangSelected="";
		if ($lang==$LANGS[$i]) $SetLangSelected = "selected";
	$langsel.= "<option class=k_select value='$SCRIPT_NAME?SetLang=$LANGS[$i]' $SetLangSelected>".label($LANGS[$i])." ($LANGS[$i])</opiton>";
}

$langsel.="</select>";
if (!isset($lang)) return;
$labelsearch=trim($labelsearch);
?>

<table width=100% cellspacing=0 cellpadding=0 border=1>
<tr><td class=k_td>
<table><tr>
	<td class=k_td><?echo label("Select language").": ".$langsel;?></td>
	<td><img src=../img/i_separator.gif border=0 width=4 height=22 align="absmiddle"></td>
</tr></table>
</td></tr>
</table>

<?


if (strlen($labelsearch)) $AND = "AND value !~* '$labelsearch'";

$query="SELECT label,id FROM label deflabel 
	 WHERE lang='$defaultlang' AND label<>''
	 AND label NOT IN (SELECT label FROM label 
				WHERE label=deflabel.label AND lang='$lang' $AND)
	 ORDER BY label
	 ";

$res=$adodb->Execute($query);

echo "
	<table align=center border=1 width=450>\n
		<tr class=k_formtitle><td align=center colspan=2>".label('Search in language').": ".label($lang)."</td></tr>
		<tr><td class=k_td align=center width=100%>
		<form  style=\"padding: 0px; margin: 0px;\"> 
		<input class=\"k_input\" name=\"labelsearch\" value=\"$labelsearch\" size=60>
		</td><td>
		<input type=\"image\" src=\"../img/i_find_n.gif\" alt='".label("Search in language").label($lang)."' 
				onmouseover=\"this.src='../img/i_find_a.gif'\" onmouseout=\"this.src='../img/i_find_n.gif'\">
		</form>
		</td></tr>
	</table>
	<br>
";

if ($res->RecordCount())
	echo "
	    <table align=center border=1 width=450>\n
		<tr class=k_formtitle>
		<td>".label('Id')."</td>
		<td>".label('Curent text')."</td>
		<td colspan=3>".label('New text')."</td>
		</tr>";
else		
	echo "<table align=center border=1 width=450>\n
		<tr class=k_formtitle>
		<td align=center>".label('No labels')."</td>
		</tr></table>";
	
for ($i=0;$i<$res->RecordCount();$i++)
{
	parse_str(ado_ExplodeName($res,$i));
	$label=stripslashes($label);
	$label_s=urlencode($labelsearch);

	$mlt_bgcolor="bgcolor=\"#D0D0D0\"";
	if (($i&1)==0) $mlt_bgcolor="bgcolor=\"#E0E0E0\"";

	echo " <tr $mlt_bgcolor class=k_td>\n";
	echo "  <td>$id</td>\n";
	echo "  <td>$label</td>\n";
	
	echo "<td>";
	echo "<form method=post action=$SCRIPT_NAME style=\"padding: 0px; margin: 0px;\">";
	echo "<input type=hidden name=action value=modlabel>";
	echo "<input type=hidden name=label value=\"$label\">";

	$label=addslashes($label);
	$value="";
	$query="SELECT value FROM label WHERE label='$label' AND lang='$lang'";
	parse_str(ado_query2url($query));
	echo "<input class=k_input name=value size=40 value='$value'></td>";
	echo "<td><input type=\"image\" src=\"../img/i_save_n.gif\" alt='".label("Save").": $label' 
				onmouseover=\"this.src='../img/i_save_a.gif'\" onmouseout=\"this.src='../img/i_save_n.gif'\">";
	echo "</form></td>\n";
	echo "<td valign=top><a href=$SCRIPT_NAME?action=dellabel&id=$id&labelsearch=$labelsearch>
			<img align=\"absmiddle\" class=k_imgbutton border=0 src=\"../img/i_delete_n.gif\" alt='".label(Delete).": $label'
				onmouseover=\"this.src='../img/i_delete_a.gif'\" 
				onmouseout=\"this.src='../img/i_delete_n.gif'\"></a></td>";
	
	echo " </tr>\n";
}
if ($res->RecordCount())
	echo "</table>\n";




?>

<fieldset style="width:100%; margin: 5px 5px 0px 5px">
<legend class="k_td" id="kameleon_multi_legend" onclick="document.kameleon_multi_form.style.display=''"
	style="cursor:pointer"><?echo label("Batch")?></legend>

	<form name="kameleon_multi_form" action="<?echo $SCRIPT_NAME?>" method="post" class="k_td" style="margin:0px; display:none;">
		<input type="hidden" name="action" value="batchlangs">
		<p align="center">
		<textarea name="labellabel" style="height:200px; width:49%"></textarea>
		<textarea name="labelvalue" style="height:200px; width:49%"></textarea>
		</p>
		
		<p align="right">
		
		<input type="text" name="batchlangs" value="<?echo $lang?>" size=5 class="k_input">
		<input type="submit" class="k_button" value="<?echo label('Save')?>">
		</p>
	</form>
</fieldset>
<img src="img/spacer.gif" width="1" height="1">
