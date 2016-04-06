<?
	$KATEGORIA = $CIACHO[admin_ka_id];

	$minimum = $FORM[minimum];
	$procent = $FORM[procent];
	$rabat = $FORM[rabat_id];

	if (strlen($minimum) && strlen($procent))
	{
		$minimum = ereg_replace(",","\.",$minimum);
		$minimum = ereg_replace("[^0-9\.-]","",$minimum);
		$procent = ereg_replace(",","\.",$procent);
		$procent = ereg_replace("[^0-9\.-]","",$procent);

		if (strlen($rabat))
			$sql = "UPDATE rabat_ilosciowy SET 
					ri_minmum = $minimum,
					ri_procent = $procent
					WHERE ri_id = $rabat";
		else
			$sql = "INSERT INTO rabat_ilosciowy (ri_sk_id,ri_ka_id,ri_minmum, ri_procent)
					VALUES ($SKLEP_ID, $KATEGORIA,$minimum,$procent)";
		$projdb->execute($sql);
		
	}


	$add = "
	<table class=\"list_table\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<tbody>
	<TR>
		<th>".sysmsg('No','kat')."</th>
		<th>".sysmsg('Minimal quantity','kat')."</th>
		<th>".sysmsg('Percent of price','kat')."</th>
		<th>&nbsp;</th>
	</TR>
	";
	
	$sql = "SELECT * FROM rabat_ilosciowy 
			WHERE ri_ka_id = $KATEGORIA
			AND ri_sk_id = $SKLEP_ID ORDER BY ri_minmum";

	$res = $adodb->execute($sql);
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$add.= "
		<TR>
			<FORM METHOD=POST ACTION=\"$self\">
			<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
			<INPUT TYPE=\"hidden\" name=\"form[rabat_id]\" value=\"$ri_id\">
			<Td>".($i+1)."</Td>
			<Td><INPUT TYPE=\"text\" NAME=\"form[minimum]\" value=\"$ri_minmum\"></Td>
			<Td><INPUT TYPE=\"text\" NAME=\"form[procent]\" value=\"$ri_procent\">%</Td>
			<Td><img src=\"$SKLEP_IMAGES/save.gif\" style=\"cursor:hand\" onClick=\"submit()\">
			<img src=\"$SKLEP_IMAGES/del.gif\" style=\"cursor:hand\" onClick=\"usunRabat('$ri_id')\">			
			</Td>
		</TR>
		</FORM>";
	}

	$add.= "
	<TR>
		<FORM METHOD=POST ACTION=\"$self\">
		<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
		<Td>".($i+1)."</Td>
		<Td><INPUT TYPE=\"text\" NAME=\"form[minimum]\"></Td>
		<Td><INPUT TYPE=\"text\" NAME=\"form[procent]\">%</Td>
		<Td><img src=\"$SKLEP_IMAGES/save.gif\" style=\"cursor:hand\" onClick=\"submit()\"></Td>
		</FORM>
	</TR>
	</tbody>
	</TABLE>
	<FORM METHOD=POST ACTION=\"$self\" name=\"killRabat\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KategoriaRabatUsun\">
	<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
	<INPUT TYPE=\"hidden\" id=\"killRabatId\" name=\"form[killRabatId]\">
	</FORM>";

	echo $add;

?>
<script>
	
	function usunRabat(id)
	{
		if (confirm('Na pewno usun±æ ten rabat ?'))
		{
			document.killRabat.killRabatId.value = id;
			document.killRabat.submit();
		}
	}
</script>
