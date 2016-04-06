<input type="button" class="addbut" value="<?echo sysmsg("Add producer","system")?>"
	onClick="dodajProducenta()">
<?
	echo "
	<FORM METHOD=POST ACTION=\"$self\" name=\"prodForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"ProducentDodaj\">
	<INPUT TYPE=\"hidden\" name=\"form[new_indx]\" id=\"new_indx\" value=\"\">
	</FORM>
	";
?>
<script>
	function dodajProducenta()
	{
		indx = prompt('<? echo sysmsg("Producer name","system") ?>',"");
		if (indx != "" && indx != null)
		{
			document.prodForm.new_indx.value = indx;
			document.prodForm.submit();
		}
	}
</script>
