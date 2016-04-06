<script language="javascript">
function showModFun()
{
}
</script>
<?
	include "$INCLUDE_PATH/.api/banner_stats.h";

	$sql = "SELECT DISTINCT(ab_place) FROM api2_baner
			WHERE ab_server = $SERVER_ID";

	$res = $adodb->execute($sql);

	if (strlen($costxt)) $sel[$costxt] = "selected";

	$select = "<SELECT NAME=\"b_place\" class=\"k_input\">";
	$select.= "<option value=\"-1\">Wybierz nazwê</option>\n";
	
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		if (strlen(trim($ab_place)))
			$select.= "<option value=\"$ab_place\" $sel[$ab_place]>$ab_place</option>\n";
	}
	
	$select.= "</SELECT>";

	echo "<table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
		<TR class=k_form>
			<td class=k_formtitle>".label("banner place").": $select</td>
		</TR>
		<TR class=k_form>
			<td class=k_formtitle>".label("Stats for all banners in this place")."</td>
		</TR>
		</table>
		";

	$sql = "SELECT * FROM api2_baner WHERE ab_place = '$costxt' ORDER BY ab_count DESC";
	$res = $adodb->execute($sql);
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		echo "<br>";
		parse_str(ado_explodename($res,$i));
		$query = "SELECT plain AS baner_content FROM webtd WHERE server = '$SERVER_ID'
					AND sid = $ab_html";
//		$adodb->debug=1;
		parse_str(ado_query2url($query));
		$adodb->debug=0;
		echo "<table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
			<TR class=k_form>
				<td class=k_formtitle>".stripslashes($baner_content)."</td>
			</TR></table>";

		$baner_content = "";

		echo showBannerStat($ab_id);
	}
?>
