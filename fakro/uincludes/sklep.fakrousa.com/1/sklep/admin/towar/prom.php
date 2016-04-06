<?
	include ("$SKLEP_INCLUDE_PATH/js/calendar.php");

	$LIST[id]=$CIACHO[admin_to_id];

	$to_id = $LIST[id];
	
	$sql = "SELECT pm_id, pm_symbol, pm_rabat_domyslny, pm_poczatek, pm_koniec FROM promocja
			WHERE pm_koniec > ".time()." OR pm_koniec IS NULL";

	$res = $adodb->execute($sql);

	if (!$res->RecordCount())
	{
		echo sysmsg("No Promotion","admin");
		return;
	}

	$tow = "<TABLE class=\"towar_tab\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<TR>
		<Th class=\"c2\">&nbsp;</Th>
		<Th class=\"c2\">".sysmsg("Promotion","admin")."</Th>
		<Th class=\"c2\">".sysmsg("Begin","admin")."</Th>
		<Th class=\"c2\">".sysmsg("End","admin")."</Th>
		<Th class=\"c2\">".sysmsg("Price","admin")."</Th>
		<Th class=\"c4\">&nbsp;</Th>
	</TR>";

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		
		$pt_id = ""; 
		$pt_cena = ""; 
		$pt_poczatek = "";
		$pt_koniec = "";
		$checked = "";

		$sql = "SELECT pt_id, pt_cena, pt_ts_id, pt_poczatek, pt_koniec
				FROM towar_sklep, promocja_towaru,promocja
				WHERE ts_to_id = $to_id AND pt_ts_id = ts_id 
				AND pt_pm_id = $pm_id LIMIT 1";

		parse_str(ado_query2url($sql));

		$hours1 = "";
		$hours2 = "";

		for ($_i=0; $_i < 24; $_i++)
		{
			if (strlen($_i) == 1)
				$h = "0".$_i.":00";
			else
				$h = $_i.":00";
			$sel1 = "";
			$sel2 = "";

			$g = "";
			if (strlen($pt_poczatek))
			{
				$dataod = date("d-m-Y",$pt_poczatek);
				$g = date("H",$pt_poczatek);
				if ($g == $_i)
					$sel1 = "selected";
			}

			$g = "";
			if (strlen($pt_koniec))
			{
				$datado = date("d-m-Y",$pt_koniec);
				$g = date("H",$pt_koniec);
				if ($g == $_i)
					$sel2 = "selected";
			}

			$hours1.= "<option value=\"$h\" $sel1>$h</option>";
			$hours2.= "<option value=\"$h\" $sel2>$h</option>";
		}

		if ($pt_id) $checked = "checked";
	
		if (strlen($pt_poczatek))
			$pt_poczatek = date("d-m-Y",$pt_poczatek);

		if (strlen($pt_koniec))
			$pt_koniec = date("d-m-Y",$pt_koniec);
		$tow.= "				
		<FORM METHOD=POST ACTION=\"$self\" name=\"frm_$pm_id\">		
		<INPUT TYPE=\"hidden\" name=\"action\" value=\"PromocjaModTow\">
		<INPUT TYPE=\"hidden\" NAME=\"form[to_id]\" value=\"$to_id\">
		<INPUT TYPE=\"hidden\" NAME=\"form[pt_id]\" value=\"$pt_id\">
		<INPUT TYPE=\"hidden\" NAME=\"form[pm_id]\" value=\"$pm_id\">
		<INPUT TYPE=\"hidden\" NAME=\"form[pm_rabat_domyslny]\" value=\"$pm_rabat_domyslny\">
		<INPUT TYPE=\"hidden\" NAME=\"form[pm_poczatek]\" value=\"$pm_poczatek\">
		<INPUT TYPE=\"hidden\" NAME=\"form[pm_koniec]\" value=\"$pm_koniec\">
		<TR>
			<TD class=\"c2\"><INPUT TYPE=\"checkbox\" $checked NAME=\"\" onClick=\"deleteTow('$pt_id',this.checked,document.frm_$pm_id)\"></TD>
			<TD class=\"c2\">$pm_symbol</TD>
			<TD class=\"c2\"><INPUT TYPE=\"text\" NAME=\"form[pt_poczatek]\" id=\"pt_pocz_$i\" value=\"$pt_poczatek\" style=\"width:70px\">&nbsp;&nbsp;<SELECT NAME=\"form[godzinap]\">$hours1</SELECT></TD>
			<TD class=\"c2\"><INPUT TYPE=\"text\" NAME=\"form[pt_koniec]\" id=\"pt_kon_$i\" value=\"$pt_koniec\" style=\"width:70px\">&nbsp;&nbsp;<SELECT NAME=\"form[godzinak]\">$hours2</SELECT></TD>
			<TD class=\"c2\"><INPUT TYPE=\"text\" NAME=\"form[rabat]\" value=\"$pt_cena\" style=\"width:50px\"></TD>
			<TD class=\"c4\"><img src=\"$SKLEP_IMAGES/save.gif\" style=\"cursor:hand\" onClick=\"submit()\"></TD>
		</TR>
		</FORM>

		<script type=\"text/javascript\">

				Calendar.setup({
					inputField     :    \"pt_pocz_$i\",  
					ifFormat       :    \"%d-%m-%Y\", 
					showsTime      :    false,        
					button         :    \"pt_pocz_$i\",  
					singleClick    :    true,         
					firstDay	   :    1,
					step           :    1             
				});
				Calendar.setup({
					inputField     :    \"pt_kon_$i\",    
					ifFormat       :    \"%d-%m-%Y\",  
					showsTime      :    false,         
					button         :    \"pt_kon_$i\",   
					singleClick    :    true,         
					firstDay	   :    1,
					step           :    1             
				});

		</script>
		
		";
	}

	$tow.= "</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" name=\"promDelForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"PromocjaUsunTow\">
	<INPUT TYPE=\"hidden\" NAME=\"form[pt_id]\" id=\"kill_id\" value=\"\">
	</FORM>
	";

	echo $tow;
?>
<script>
	function deleteTow(id, chck, obj)
	{
		if (chck == false)
		{
			document.promDelForm.kill_id.value = id;
			document.promDelForm.submit();
		}
		else
			obj.submit();
	}

</script>
