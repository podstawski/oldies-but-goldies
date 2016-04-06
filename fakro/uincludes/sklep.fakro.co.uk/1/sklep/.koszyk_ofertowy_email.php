<?
/*
	echo "= $action =";
	$action = $_REQUEST["action"];
	if (strlen($action))
	{
		echo "===";
		include($SKLEP_INCLUDE_PATH."/action.h");
	}
*/
	if ($_REQUEST["do_action"])
		include($SKLEP_INCLUDE_PATH."/action/WysylkaDane.h");

	$w_cosiu = explode(";",$costxt);
	$maile = explode(",",$w_cosiu[0]);

	for ($i=0; $i < count($maile); $i++)	
	{
		$usun = "<img src=\"$UIMAGES/autoryzacja/i_nie.gif\" alt=\"Usuñ adres\" onClick=\"usunAdres($i)\">";
		$maile_tab.= "
		<FORM METHOD=POST ACTION=\"$self\">		
		<INPUT TYPE=\"hidden\" name=\"do_action\" value=\"1\">
		<TR>
			<TD>Email".($i+1)."</TD>
			<TD><INPUT TYPE=\"text\" NAME=\"EMAIL[$i]\" value=\"".trim($maile[$i])."\" onChange=\"submit()\" style=\"width:200px\"></TD>
			<TD>$usun</TD>
		</TR>
		</FORM>";
	}
	$zapisz = "<img src=\"$UIMAGES/sb/i_zamow.gif\" alt=\"Dodaj adres\" onClick=\"submit()\">";
	$maile_tab.= "
	<FORM METHOD=POST ACTION=\"$self\">		
	<INPUT TYPE=\"hidden\" name=\"do_action\" value=\"1\">
	<TR>
		<TD>Nowy email</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"addAdres\" value=\"\" style=\"width:200px\"></TD>
		<TD>$zapisz</TD>
	</TR></form>";

	for ($i=1; $i < count($w_cosiu); $i++)	
	{
		$usun = "<img src=\"$UIMAGES/autoryzacja/i_nie.gif\" alt=\"Usuñ region\" onClick=\"usunRegion($i)\">";
		$regiony_tab.= "
		<FORM METHOD=POST ACTION=\"$self\">
		<INPUT TYPE=\"hidden\" name=\"do_action\" value=\"1\">
		<TR>
			<TD>Region".($i)."</TD>
			<TD><INPUT TYPE=\"text\" NAME=\"REGION[".$i."]\" value=\"".trim($w_cosiu[$i])."\" onChange=\"submit()\" style=\"width:200px\"></TD>
			<TD>$usun</TD>
		</TR>
		</form>
		";
	}
	$zapisz = "<img src=\"$UIMAGES/sb/i_zamow.gif\" alt=\"Dodaj region\" onClick=\"submit()\">";	

	$regiony_tab.= "
	<FORM METHOD=POST ACTION=\"$self\">		
	<INPUT TYPE=\"hidden\" name=\"do_action\" value=\"1\">
	<TR>
		<TD>Nowy region</TD>
		<TD><INPUT TYPE=\"text\" NAME=\"addRegion\" value=\"\" style=\"width:200px\"></TD>
		<TD>$zapisz</TD>
	</TR>
	</form>
	";
	
	echo "
	<TABLE>
		<TR>
			<Th clospan=\"2\">Adresy Email</Th>
		</TR>
		$maile_tab
		<TR>
			<Th clospan=\"2\">Regiony</Th>
		</TR>
		$regiony_tab
	</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" name=\"updateWysylka\">
	<INPUT TYPE=\"hidden\" name=\"do_action\" value=\"1\">
	<INPUT TYPE=\"hidden\" name=\"killAdres\">
	<INPUT TYPE=\"hidden\" name=\"killRegion\">
	</FORM>
	";
?>
<script>
	function usunAdres(id)
	{
		if (!confirm('Na pewno usun±æ ten adres ?')) return;
		document.updateWysylka.killAdres.value=id;
		document.updateWysylka.submit();
	}

	function usunRegion(id)
	{
		if (!confirm('Na pewno usun±æ ten region ?')) return;
		document.updateWysylka.killRegion.value=id;
		document.updateWysylka.submit();
	}
</script>
