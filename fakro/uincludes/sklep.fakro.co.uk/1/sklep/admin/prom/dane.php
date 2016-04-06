<?
	include ("$SKLEP_INCLUDE_PATH/js/calendar.php");
	$pm_id  = $CIACHO[admin_pm_id];

	$sql = "SELECT * FROM promocja WHERE pm_id = $pm_id";
	parse_str(ado_query2url($sql));
	
	
	$hours1 = "";
	$hours2 = "";

	for ($i=0; $i < 24; $i++)
	{
		if (strlen($i) == 1)
			$h = "0".$i.":00";
		else
			$h = $i.":00";
		$sel1 = "";
		$sel2 = "";

		$g = "";
		if (strlen($pm_poczatek))
		{
			$dataod = date("d-m-Y",$pm_poczatek);
			$g = date("H",$pm_poczatek);
			if ($g == $i)
				$sel1 = "selected";
		}

		$g = "";
		if (strlen($pm_koniec))
		{
			$datado = date("d-m-Y",$pm_koniec);
			$g = date("H",$pm_koniec);
			if ($g == $i)
				$sel2 = "selected";
		}

		$hours1.= "<option value=\"$h\" $sel1>$h</option>";
		$hours2.= "<option value=\"$h\" $sel2>$h</option>";
	}
	

	echo "
	<FORM METHOD=POST ACTION=\"$self\" name=\"promDataForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"PromocjaZapiszDane\">
	<INPUT TYPE=\"hidden\" name=\"form[pm_id]\" value=\"$pm_id\">
	<TABLE class=\"list_table\" cellspacing=0 cellpadding=0 border=0 width=80%>
	<TR>
		<TD class=\"c2\">".sysmsg("th_name","system").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[nazwa]\" value=\"$pm_symbol\" style=\"width:197px\"></TD>
	</TR>
	<TR>
		<TD class=\"c2\">".sysmsg("Begin","admin").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[datap]\" id=\"c_data_od\" value=\"$dataod\">&nbsp;&nbsp;<SELECT NAME=\"form[godzinap]\">$hours1</SELECT></TD>
	</TR>
	<TR>
		<TD class=\"c2\">".sysmsg("End","admin").":</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[datak]\" id=\"c_data_do\" value=\"$datado\">&nbsp;&nbsp;<SELECT NAME=\"form[godzinak]\">$hours2</SELECT></TD>
	</TR>

	<TR>
		<TD class=\"c2\">".sysmsg("Default discount","admin")."</TD>
		<TD class=\"c4\"><INPUT TYPE=\"text\" NAME=\"form[rabat]\" value=\"$pm_rabat_domyslny\" style=\"width:50px\">%</TD>
	</TR>
	<TR>
		<TD class=\"c4\" colspan=\"2\" align=\"right\"><INPUT TYPE=\"submit\" value=\"Zapisz\"></TD>
	</TR>		
	</TABLE>
	</FORM>
	";
	
?>

<script type="text/javascript">

		Calendar.setup({
			inputField     :    "c_data_od",      // id of the input field
			ifFormat       :    "%d-%m-%Y",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "c_data_od",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			firstDay	   :    1,
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});
		Calendar.setup({
			inputField     :    "c_data_do",      // id of the input field
			ifFormat       :    "%d-%m-%Y",       // format of the input field
			showsTime      :    false,            // will display a time selector
			button         :    "c_data_do",   // trigger for the calendar (button ID)
			singleClick    :    true,           // double-click mode
			firstDay	   :    1,
			step           :    1                // show all years in drop-down boxes (instead of every other year as default)
		});

</script>
