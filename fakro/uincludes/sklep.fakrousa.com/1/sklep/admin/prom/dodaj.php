<input type="button" class="addbut" value="<?echo sysmsg("Add promotion","system")?>"
	onClick="dodajPromocje()">
<?
	echo "
	<FORM METHOD=POST ACTION=\"$self\" name=\"promotionForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"PromocjaDodaj\">
	<INPUT TYPE=\"hidden\" name=\"form[new_indx]\" id=\"new_indx\" value=\"\">
	</FORM>
	";
?>
<script>
	function dodajPromocje()
	{
		indx = prompt('<? echo sysmsg("Promotion name","system") ?>',"");
		if (indx != "" && indx != null)
		{
			document.promotionForm.new_indx.value = indx;
			document.promotionForm.submit();
		}
	}
</script>
