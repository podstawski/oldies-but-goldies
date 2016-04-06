<?
	
	if (!strlen($CIACHO[admin_su_id])) return;

	$LIST[sort_f]="ad_adres";
	$sort=$LIST[sort_f];
	if ($LIST[sort_d]) $sort.=" DESC";	

	$FROMWHERE = "FROM adresy WHERE ad_su_id = ".$CIACHO[admin_su_id];

	$sql = "SELECT * $FROMWHERE ORDER BY ".$sort;

	if (!$LIST[ile])
	{
		$query="SELECT count(*) AS c $FROMWHERE";
		parse_str(ado_query2url($query));
		$LIST[ile]=$c;
	}
	$navi=$size?navi($self,$LIST,$size):"";
//	$projdb->debug=1;
	if (strlen($navi))
		$res= $projdb->SelectLimit($sql,$size,$LIST[start]+0);
	else
		$res = $projdb->Execute($sql);	
//	$projdb->debug=0;
	
	$table = "$navi
	<table id=\"tadr\" class=\"list_table\">
	<TR>
		<th>Lp.</th>
		<th sort='ad_adres'>Adres</th>
		<th width=\"1%\">Akcje</th>
	</TR>";

	$qs=sort_navi_qs($LIST);
	$style = "style=\"font-size:11px;width:440px;\"";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$buttons = "
		<img src=\"$SKLEP_IMAGES/save.gif\" border=0 onClick=\"submit()\" style=\"cursor:hand\">
		<img src=\"$SKLEP_IMAGES/del.gif\" border=0 onClick=\"usunAdres($ad_id)\" style=\"cursor:hand\">
		";
		$ad_adres = stripslashes($ad_adres);

		$table.= "
		<FORM METHOD=POST ACTION=\"$self\">
		<INPUT TYPE=\"hidden\" name=\"action\" value=\"AdresZapisz\">
		<INPUT TYPE=\"hidden\" name=\"form[adr_id]\" value=\"$ad_id\">
		<INPUT TYPE=\"hidden\" name=\"form[su_id]\" value=\"".$CIACHO[admin_su_id]."\">
		<TR>
			<td valign=\"top\">".($i+1)."</td>
			<td valign=\"top\">
			<TEXTAREA NAME=\"form[adres]\" ROWS=\"2\" $style>$ad_adres</TEXTAREA>
			</td>
			<td valign=\"top\" width=\"1%\" nowrap>$buttons</td>
		</TR>
		</FORM>
		";
	}
	$buttons = "
	<img src=\"$SKLEP_IMAGES/save.gif\" border=0 onClick=\"submit()\" style=\"cursor:hand\">";
	$table.= "
	<FORM METHOD=POST ACTION=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"AdresZapisz\">
	<INPUT TYPE=\"hidden\" name=\"form[su_id]\" value=\"".$CIACHO[admin_su_id]."\">
	<TR>
		<td valign=\"top\">nowy</td>
		<td valign=\"top\">
		<TEXTAREA NAME=\"form[adres]\" ROWS=\"2\"  $style></TEXTAREA>
		</td>
		<td valign=\"top\" width=\"1%\">$buttons</td>
	</TR>
	</FORM>
	</table>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killAdres\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"AdresUsun\">	
	<INPUT TYPE=\"hidden\" name=\"form[adr_id]\" id=\"adrId\" value=\"\">	
	</FORM>
	";

	echo $table;
?>
<script>
	//list_table_init('tadr','<?echo $LIST[sort_f]?>',<?echo 0+$LIST[sort_d]?>);


	function usunAdres(id)
	{
		if (confirm('Na pewno usunБц ten adres ?'))
		{
			document.killAdres.adrId.value = id;
			document.killAdres.submit();
		}
	}

</script>
