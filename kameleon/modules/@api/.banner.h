<script language="javascript">
function showModFun()
{
}
</script>
<?
	include "$INCLUDE_PATH/.api/banner_stats.h";

	$sql = "SELECT * FROM api2_baner
			WHERE ab_html = $sid AND ab_server = $SERVER_ID";	
	parse_str(ado_query2url($sql));

	//echo ado_query2url($sql);

	if ($ab_target == "_new") $s1 = "selected";

	$banner_target = "<SELECT NAME=\"BANNER[target]\" class=\"k_input\">
						<option value=\"_top\">To samo okno</option>
						<option value=\"_new\" $s1>Nowe okno</option>
						</SELECT>";

	$sql = "SELECT ab_place AS p_nazwa FROM api2_baner
			WHERE ab_server = $SERVER_ID
			GROUP BY ab_place";

	$res = $adodb->execute($sql);

	$select = "<SELECT NAME=\"BANNER[place]\" class=\"k_input\" id=\"place_select\" onChange=\"rewritePlace()\">";
//	$select.= "<option value=\"-1\">Wybierz nazwê</option>\n";

	if (strlen($ab_place)) $sel[$ab_place] = "selected";
	
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		if (strlen(trim($p_nazwa)))
			$select.= "<option value=\"$p_nazwa\" $sel[$p_nazwa]>$p_nazwa</option>\n";
	}
	
	$select.= "</SELECT>";
	
	$lista = "<img src=\"img/i_tree_n.gif\" id=\"select_img\" onclick=\"setSelectVisible()\" style=\"cursor:hand\">";
	$da_input = "<img src=\"img/i_new_n.gif\" id=\"input_img\" onclick=\"setInputVisible()\" style=\"cursor:hand\">";


	
	if (strlen($ab_d_start)) $dataod = date('d-m-Y H:i',$ab_d_start);
	if (strlen($ab_d_end)) $datado = date('d-m-Y H:i',$ab_d_end);

	//echo "$dataod ... $ab_d_start";

	if (!strlen($ab_limit)) $ab_limit=0;

	echo "<table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
			<INPUT TYPE=\"hidden\" name=\"BANNER[sid_nr]\" value=\"$sid\">
			<TR class=k_form>
				<td colspan=2 class=k_formtitle>".label("Banner properities")."</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Begin of campaign")." <I>(dd-mm-rrrr):</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[data_od]\" id=\"banner_data_od\" value=\"$dataod\"></td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("End of campaign")." <I>(dd-mm-rrrr):</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[data_do]\" id=\"banner_data_do\" value=\"$datado\"></td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Show limit")." <I>(0 - ".label("no limit")."):</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[limit]\" value=\"$ab_limit\"></td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Baner id").":</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[id]\" value=\"$ab_textid\" style=\"width:200px\"></td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Show place").": $lista $da_input</td>
				<td>$select<INPUT TYPE=\"text\" class=\"k_input\" id=\"place_input\" NAME=\"BANNER[place]\" value=\"$ab_place\"></td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">Link:</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[href]\" id=\"banner_href\" style=\"width:200px\" value=\"$ab_href\">
					<img class=k_imgbutton src=\"img/i_tree_n.gif\" onClick=\"openTree('banner_href',document.edytujtd.banner_href.value,'')\" style=\"cursor:hand;\" onmouseover=\"this.src='img/i_tree_a.gif'\" onmouseout=\"this.src='img/i_tree_n.gif'\" border=0 alt='Eksplorator' width=23 height=22 align=absmiddle>				
				</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">Target:</td>
				<td>$banner_target</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">Banner PopUp:</td>
				<td><INPUT TYPE=\"checkbox\" NAME=\"BANNER[popup]\" ID=\"popup_show\" onClick=\"setPopUpVisible()\" value=1></td>
			</TR>
			<TR class=k_form>
				<td colspan=\"2\" align=\"right\"><img src=\"img/i_save_n.gif\" style=\"cursor:hand\" onClick=\"ZapiszZmiany()\"></td>
			</TR>

		</table>
		";
//<INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[target]\" value=\"$ab_target\">
	if (strlen($ab_place))
	{

		echo "<script>
				document.all[\"place_input\"].style.display = 'none';
				document.all[\"place_select\"].style.display = 'inline';
				document.all[\"input_img\"].style.display = 'inline';
				document.all[\"select_img\"].style.display = 'none';
				</script>";

	} else
	{
		echo "<script>
				document.all[\"place_input\"].style.display = 'inline';
				document.all[\"place_select\"].style.display = 'none';
				document.all[\"input_img\"].style.display = 'none';
				document.all[\"select_img\"].style.display = 'inline';
				</script>";
	}

	
	if (strlen($costxt))
	{
		$linie = explode(":",$costxt);
		$top = $linie[0];
		$left = $linie[1];
		$width = $linie[2];	
		$height = $linie[3];
		$b_place = $linie[4];
		$b_c_text = $linie[5];
		$b_c_img = $linie[6];
		$b_close = $linie[7];
		$b_c_pixels = $linie[8];

		$dp[$b_place] = "selected";
		$dc[$b_close] = "checked";
	}
	if (!strlen($b_c_pixels)) $b_c_pixels = 0;

	$da_place = "<SELECT NAME=\"BANNER[banner_place]\" class=\"k_input\" id=\"banner_place\" onChange=\"setPositionVisible()\">
				<option value=\"-1\" >".label("Set in pixels")."</option>
				<option value=\"1\" $dp[1]>".label("Center")."</option>
				<option value=\"2\" $dp[2]>".label("Center top")."</option>
				<option value=\"3\" $dp[3]>".label("Center bottom")."</option>
				<option value=\"4\" $dp[4]>".label("Center right")."</option>
				<option value=\"5\" $dp[5]>".label("Center left")."</option>
				<option value=\"6\" $dp[6]>".label("Left top")."</option>
				<option value=\"7\" $dp[7]>".label("Left bottom")."</option>
				<option value=\"8\" $dp[8]>".label("Right top")."</option>
				<option value=\"9\" $dp[9]>".label("Right bottom")."</option>
				</SELECT>";
	
	$da_close = "
				<INPUT TYPE=\"radio\" NAME=\"BANNER[closeplace]\" value=\"1\" $dc[1]>
				&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=\"radio\" NAME=\"BANNER[closeplace]\" value=\"2\" $dc[2]>
				&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=\"radio\" NAME=\"BANNER[closeplace]\" value=\"3\" $dc[3]>
				<TABLE border=1>
				<TR>
					<TD width=95 height=70 align=\"center\">Baner</TD>
				</TR>
				</TABLE>
				<INPUT TYPE=\"radio\" NAME=\"BANNER[closeplace]\" value=\"4\" $dc[4]>
				&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=\"radio\" NAME=\"BANNER[closeplace]\" value=\"5\" $dc[5]>
				&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE=\"radio\" NAME=\"BANNER[closeplace]\" value=\"6\" $dc[6]>
				";

	echo "
			<div id=\"popup_div\" style=\"position:relative;display:none\">
			<table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
			<TR class=k_form>
				<td colspan=2 class=k_formtitle>PopUp</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Banner position").":</td>
				<td>$da_place</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Pixels from top").":</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[pop_top]\" id=\"pop_top\" value=\"$top\"> px</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Pixels from left").":</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[pop_left]\" id=\"pop_left\" value=\"$left\"> px</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Height").":</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[pop_width]\" value=\"$width\"> px</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Width").":</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[pop_height]\" value=\"$height\"> px</td>
			</TR>
			<TR class=k_form>
				<td colspan=2 class=k_formtitle>".label("Close button")."</td>
			</TR>
			<TR class=k_form>
				<td align=\"right\">".label("Button label").":</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[close_text]\" value=\"$b_c_text\"></td>
			</TR>
			<tr class=k_form>
				<td align=right>".label("Button picture").":</td>
				<td><input class='k_input' type=text size=30 
					name=BANNER[close_img] id=\"close_baner_img\" value=\"$b_c_img\">
				<img src=img/i_image_n.gif align=absmiddle onClick=\"openGalery('close_baner_img','ufiles.php?page=1000&galeria=2')\" 
					style=\"cursor:hand;\" onmouseover=\"this.src='img/i_image_a.gif'\" 
					onmouseout=\"this.src='img/i_image_n.gif'\" 
					border=0 alt='Wstaw lub modyfikuj obrazek' width=23 height=22>
				</td>
		   </tr>
			<TR class=k_form>
				<td align=\"right\">".label("Button pixels from top").":</td>
				<td><INPUT TYPE=\"text\" class=\"k_input\" NAME=\"BANNER[close_pixels]\" value=\"$b_c_pixels\"> px</td>
			</TR>
			<TR class=k_form>
				<td valign=top align=\"right\">".label("Button align")." :</td>
				<td>$da_close</td>
			</TR>

			</table>
			</div>";
		echo "
		<INPUT TYPE=\"hidden\" NAME=\"BANNER[sid]\" value=\"$sid\">
		<INPUT TYPE=\"hidden\" NAME=\"BANNER[ab_id]\" value=\"$ab_id\">
		";

		echo showBannerStat($ab_id);

	if (strlen($costxt))
	{
		?>
			<script>
				document.all["popup_show"].checked = true;
				document.all["popup_div"].style.display = 'inline';
			</script>
		<?
	}
?>

<script>

	function setPopUpVisible()
	{
		if (document.all["popup_show"].checked == true)
		{
			document.all["popup_div"].style.display = 'inline';
		}
		else 
		{
			document.all["popup_div"].style.display = 'none';
		}
	}

	function setPositionVisible()
	{
		if (document.all["banner_place"].value != -1)
		{
			document.all["pop_top"].style.background = '#CCCCCC';
			document.all["pop_left"].style.background = '#CCCCCC';
			document.all["pop_top"].disabled = true;
			document.all["pop_left"].disabled = true;
		}
		else
		{
			document.all["pop_top"].style.background = '#F0F0F0';
			document.all["pop_left"].style.background = '#F0F0F0';
			document.all["pop_top"].disabled = false;
			document.all["pop_left"].disabled = false;
		}
	}

	setPositionVisible();

	function setSelectVisible()
	{
		document.all["place_input"].style.display = 'none';
		document.all["place_select"].style.display = 'inline';
		document.all["input_img"].style.display = 'inline';
		document.all["select_img"].style.display = 'none';

	}

	function setInputVisible()
	{
		document.all["place_input"].style.display = 'inline';
		document.all["place_select"].style.display = 'none';
		document.all["input_img"].style.display = 'none';
		document.all["select_img"].style.display = 'inline';

	}

	function rewritePlace()
	{
		document.all["place_input"].value = document.all["place_select"].value;
	}


    Calendar.setup({
        inputField     :    "banner_data_od",   // id of the input field
        ifFormat       :    "%d-%m-%Y %H:%M",       // format of the input field
        showsTime      :    true,
		align          :    "Tl",           
        timeFormat     :    "24"
    });
    Calendar.setup({
        inputField     :    "banner_data_do",   // id of the input field
        ifFormat       :    "%d-%m-%Y %H:%M",       // format of the input field
        showsTime      :    true,
		align          :    "Tl",           
        timeFormat     :    "24"
    });

</script>
