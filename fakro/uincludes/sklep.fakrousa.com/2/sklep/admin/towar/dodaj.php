<input type="button" class="addbut" value="<?echo sysmsg("Add article","system")?>"
	onClick="dodajTowar()">
<?
	echo "
	<FORM METHOD=POST ACTION=\"$self\" name=\"articleForm\">
	<INPUT TYPE=\"hidden\" name=\"action\" value=\"TowarDodaj\">
	<INPUT TYPE=\"hidden\" name=\"form[new_indx]\" id=\"new_indx\" value=\"\">
	</FORM>
	";
?>
<script>
	function dodajTowar()
	{
		indx = prompt('<? echo sysmsg("Article index","system") ?>',"");
		if (indx != "" && indx != null)
		{
			document.articleForm.new_indx.value = indx;
			document.articleForm.submit();
		}
	}
</script>
