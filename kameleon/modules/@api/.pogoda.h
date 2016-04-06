<script language="javascript">
function showModFun()
{
}
</script>
<?

	$string = explode(";",$costxt);

	$city_code = $string[0];
	$type = eregi_replace("/","_",$string[1]);

	$ch[$type] = "checked";
	echo "
	  <table width=\"100%\" border=1 align=center bgcolor=white cellpadding=2 cellspacing=2>
	";

	if (!strlen($city_code)) $city_code = "12330";
//Rodzaj prezentacji
	echo "
		<TR class=k_form>
			<td colspan=3 class=k_formtitle>".label("Presentation type").":</td>
		</TR>
		<TR class=k_form>
			<TD colspan=2>
				<a href=\"http://polish.wunderground.com/global/stations/$city_code.html\">
				<img src=\"http://banners.wunderground.com/banner/bigwx_metric_cond/language/www/global/stations/$city_code.gif\"
				alt=\"Kliknij tu\" border=0 height=60 width=468></a>
			</TD>
			<TD><INPUT TYPE=\"radio\" NAME=\"WEATHER[type]\" $ch[bigwx_metric_cond_language_www] value=\"bigwx_metric_cond/language/www\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2>
				<a href=\"http://polish.wunderground.com/global/stations/$city_code.html\">
				<img src=\"http://banners.wunderground.com/banner/default_metric/language/www/global/stations/$city_code.gif\"
				alt=\"Kliknij tu\" border=0 height=60 width=468></a>			
			</TD>
			<TD><INPUT TYPE=\"radio\" NAME=\"WEATHER[type]\" $ch[default_metric_language_www] value=\"default_metric/language/www\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2>
				<a href=\"http://polish.wunderground.com/global/stations/$city_code.html\">
				<img src=\"http://banners.wunderground.com/banner/gizmotimetemp_metric/language/www/global/stations/$city_code.gif\"
				alt=\"Kliknij tu\" border=0 height=41 width=127></a>
			</TD>
			<TD><INPUT TYPE=\"radio\" NAME=\"WEATHER[type]\" $ch[gizmotimetemp_metric_language_www] value=\"gizmotimetemp_metric/language/www\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2>
				<a href=\"http://polish.wunderground.com/global/stations/$city_code.html\">
				<img src=\"http://banners.wunderground.com/banner/infoboxtr_metric/language/www/global/stations/$city_code.gif\"
				alt=\"Kliknij tu\" border=0 height=108 width=144></a>			
			</TD>
			<TD><INPUT TYPE=\"radio\" NAME=\"WEATHER[type]\" $ch[infoboxtr_metric_language_www] value=\"infoboxtr_metric/language/www\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2>
				<a href=\"http://www.wunderground.com/global/stations/$city_code.html\">
				<img src=\"http://banners.wunderground.com/banner/bigtemptr_metric/language/www/global/stations/$city_code.gif\"
				alt=\"Kliknij tu\" border=0 height=94 width=185></a>
			</TD>
			<TD><INPUT TYPE=\"radio\" NAME=\"WEATHER[type]\" $ch[bigtemptr_metric_language_www] value=\"bigtemptr_metric/language/www\"></TD>
		</TR>
		<TR class=k_form>
			<TD colspan=2>
				<a href=\"http://www.wunderground.com/global/stations/$city_code.html\">
				<img src=\"http://banners.wunderground.com/banner/sunandmoontransblack/language/www/global/stations/$city_code.gif\"
				alt=\"Kliknij tu\" border=0 height=150 width=256></a>
			</TD>
			<TD><INPUT TYPE=\"radio\" NAME=\"WEATHER[type]\" $ch[sunandmoontransblack_language_www] value=\"sunandmoontransblack/language/www\"></TD>
		</TR>
		<TR class=k_form>
			<td colspan=3 class=k_formtitle>".label("Options").":</td>
		</TR>
		<TR class=k_form>
			<td align=\"right\">".label("City code")." :</td>
			<td ><INPUT TYPE=\"text\" class=k_input NAME=\"WEATHER[city_code]\" value=\"$city_code\"></td>
			<td align=\"right\"><img src=\"img/i_save_n.gif\" onClick=\"ZapiszZmiany()\" style=\"cursor:hand\"></td>
		</TR>
		<TR class=k_form>
			<td align=\"right\">".label("City search")." :</td>
			<td ><INPUT TYPE=\"text\" class=k_input NAME=\"city_name\" value=\"\"></td>
			<td align=\"right\"><img src=\"img/i_previewmode_n.gif\" onClick=\"citySearch()\" style=\"cursor:hand\"></td>
		</TR>
		</TABLE>
	";
?>

<script>
	function citySearch()
	{
		if (document.all["city_name"].value != '')
			document.location.href = "http://www.wunderground.com/cgi-bin/findweather/getForecast?query="+document.all["city_name"].value;
	}
</script>