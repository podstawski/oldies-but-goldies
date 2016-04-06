<?
	$pm_id  = $CIACHO[admin_pm_id];

	$sql = "SELECT * FROM promocja WHERE pm_id = $pm_id";
	parse_str(ado_query2url($sql));

	$sql = "SELECT * FROM promocja_towaru
			LEFT JOIN towar_sklep ON pt_ts_id = ts_id
			WHERE pt_pm_id = $pm_id
			ORDER BY pt_id";
	$res = $adodb->execute($sql);


	$tow = "<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>";
	
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$sql = "SELECT to_nazwa, to_indeks FROM towar WHERE to_id = $ts_to_id";
		parse_str(ado_query2url($sql));

		$button = "<img src=\"$SKLEP_IMAGES/save.gif\" onClick=\"submit()\" border=0 style=\"cursor:hand\">";
		$button.= "&nbsp;&nbsp;&nbsp;<img src=\"$SKLEP_IMAGES/del.gif\" onClick=\"deleteTow('$pt_id')\" style=\"cursor:hand\">";
		$tow.= "				
		<FORM METHOD=POST ACTION=\"$self\">		
		<INPUT TYPE=\"hidden\" name=\"action\" value=\"PromocjaModTow\">
		<INPUT TYPE=\"hidden\" NAME=\"form[pt_id]\" value=\"$pt_id\">
		<TR>
			<TD class=\"c2\" title=\"$to_nazwa\">$to_indeks</TD>
			<TD class=\"c2\"><INPUT TYPE=\"text\" NAME=\"form[rabat]\" value=\"$pt_cena\"></TD>
			<TD class=\"c4\">$button</TD>
		</TR>
		</FORM>		
		";

	}
	$indx = sysmsg("Article index","admin");
	$tow.= "
	<FORM METHOD=POST ACTION=\"$self\" name=\"addTow\">		
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"PromocjaDodajTow\">
	<INPUT TYPE=\"hidden\" NAME=\"form[rabat]\" value=\"$pm_rabat_domyslny\">
	<INPUT TYPE=\"hidden\" NAME=\"form[pm_id]\" value=\"$pm_id\">
	<INPUT TYPE=\"hidden\" NAME=\"form[pm_poczatek]\" value=\"$pm_poczatek\">
	<INPUT TYPE=\"hidden\" NAME=\"form[pm_koniec]\" value=\"$pm_koniec\">
	<TR>
		<TD class=\"c2\"><INPUT onClick=\"indxOnClick_$sid(this)\" onBlur=\"indxOnBlur_$sid(this)\" TYPE=\"text\" NAME=\"form[dodaj]\" value=\"$indx\"></TD>
		<TD class=\"c4\" colspan=\"2\"><INPUT TYPE=\"submit\" class=\"button\" value=\"".sysmsg("Add article","admin")."\"></TD>
	</TR>	
	</FORM>	
	</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" name=\"promDelForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"PromocjaUsunTow\">
	<INPUT TYPE=\"hidden\" NAME=\"form[pt_id]\" id=\"kill_id\" value=\"\">
	</FORM>
	";

	echo $tow;

?>
<script>
	function deleteTow(id)
	{
		if (confirm('Na pewno usunБц ten towar z promocji ?'))
		{
			document.promDelForm.kill_id.value = id;
			document.promDelForm.submit();
		}
	}

	function indxOnClick_<?echo $sid?>(obj)
	{
		if (obj.value=='<?echo $indx?>') obj.value='';
	}

	function indxOnBlur_<?echo $sid?>(obj)
	{
		if (obj.value.length == 0) obj.value='<?echo $indx?>';
	}
</script>
