<?
	function showBannerStat($ab_id, $edit_style = 1)
	{
		global $adodb;

		if (!strlen($ab_id)) return;

		$sql = "SELECT * FROM api2_baner 
				WHERE ab_id = $ab_id";

		parse_str(ado_query2url($sql));
		
		if ($edit_style)
		{
			if (strlen($ab_ccount) || $ab_count == 0) $ctc = 0;
				else $ctc = $ab_click / $ab_count * 100;

			$ctc = round($ctc,2);

			$res = "
			<table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
			<TR class=k_form>
				<td colspan=2 class=k_formtitle>".label("Banner stats")."</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Show count").":</td>
				<td>$ab_count</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Click count").":</td>
				<td>$ab_click</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Show to click ratio")." (StC):</td>
				<td>$ctc%</td>
			</TR>
			</table>
			";
		}
		return $res;

	}

?>