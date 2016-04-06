<?
	$KATEGORIA = $CIACHO[admin_ka_id];

	if (!strlen($KATEGORIA) || !$KATEGORIA) return;

	$sql = "SELECT * FROM kategorie WHERE ka_id = $KATEGORIA";
	parse_str(ado_query2url($sql));

	$sql = "SELECT sk_id, sk_nazwa FROM sklep ORDER BY sk_nazwa";
	$res = $adodb->execute($sql);
	$opcje = "";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		$opcje.= "<option value=\"$sk_id\">$sk_nazwa</option>\n";
	}

	$table = "
	<FORM METHOD=POST ACTION=\"$self\" name=\"nowaCena\" onSubmit=\"return false\">	
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"CenaKategoriaZapisz\">
	<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
	<table class=\"list_table\" cellspacing=0 cellpadding=0 border=0 width=100%>
	<thead>
	<TR>
		<Th>".sysmsg('Price','kat')."</Th>
		<Th>".sysmsg('Shop','import')."</th>
		<Th>&nbsp;</Th>
	</TR>
	</thead>
	<tbody>
		<TR>
			<Td><INPUT TYPE=\"text\" NAME=\"form[cena]\"></Td>
			<Td align=\"left\"><SELECT NAME=\"form[sklep]\">$opcje</SELECT></td>
			<Td><img src=\"$SKLEP_IMAGES/save.gif\" style=\"cursor:hand\" onClick=\"zapiszCene()\"></Td>
		</TR>
	</tbody>
	</FORM>
	<FORM METHOD=POST ACTION=\"$self\" name=\"nowyVat\" onSubmit=\"return false\">	
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"VatKategoriaZapisz\">
	<INPUT TYPE=\"hidden\" name=\"KATEGORIA\" value=\"$KATEGORIA\">
	<thead>
	<TR>
		<Th colspan=2><? sysmsg('VAT','kat')?></Th>
		<Th>&nbsp;</Th>
	</TR>
	</thead>
	<tbody>
		<TR>
			<Td colspan=2><INPUT TYPE=\"text\" NAME=\"form[vat]\"></Td>
			<Td><img src=\"$SKLEP_IMAGES/save.gif\" style=\"cursor:hand\" onClick=\"zapiszVat()\"></Td>
		</TR>
	</tbody>
	</table>
	</form>";

	echo $table;

?>
<script>
	function zapiszCene()
	{
		if (confirm("<? sysmsg('Sure you want to overwrite price for all items in category','kat')?> ?"))
			document.nowaCena.submit();

	}

	function zapiszVat()
	{
		if (confirm("<? sysmsg('Sure you want to overwrite tax for all items in category','kat')?> ?"))
			document.nowyVat.submit();

	}

</script>
