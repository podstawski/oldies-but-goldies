<input type="button" class="addbut" value="<?echo sysmsg("Add category","system")?>"
	onClick="dodajKat()">
<?
	$PARENT_KAT = $FORM[parent_id];
	if (!$PARENT_KAT) $PARENT_KAT = "";
	echo "
	<FORM METHOD=POST ACTION=\"$self\" name=\"catForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"KategoriaDodaj\">
	<INPUT TYPE=\"hidden\" name=\"form[new_indx]\" id=\"new_indx\" value=\"\">
	<INPUT TYPE=\"hidden\" name=\"form[new_parent]\" id=\"new_parent\" value=\"$PARENT_KAT\">
	<INPUT TYPE=\"hidden\" name=\"form[parent_id]\" value=\"$PARENT_KAT\">
	</FORM>
	";
?>
<script>
	function dodajKat()
	{
		indx = prompt('<? echo sysmsg("Categoty name","system") ?>',"");
		if (indx != "" && indx != null)
		{
			document.catForm.new_indx.value = indx;
			document.catForm.submit();
		}
	}
</script>
