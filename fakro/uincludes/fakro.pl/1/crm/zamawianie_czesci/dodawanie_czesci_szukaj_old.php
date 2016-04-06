<div align="center">
<table>
<form action="<?=$_action;?>" method="post" onsubmit="return walidacjaTabliczki();">
<input type="hidden" name="mode" value="results">
<tr>
	<td>numer identyfikacyjny produktu:</td>
	<td>
	<input type="text" name="nazwa_nr" id="nazwa_nr" value="" style="width:73px">
	<input type="text" name="nazwa_nr2" id="nazwa_nr2" value="" size="4" maxlength="4">
	<input type="text" name="nazwa_nr3" id="nazwa_nr3" value="" size="2" maxlength="2">
	<input type="text" name="nazwa_nr4" id="nazwa_nr4" value="" size="4" maxlength="4">
	</td>
</tr>
<tr>
	<td colspan="2" align="right"><input class="button" type="submit" value="szukaj"></td>
</tr>
</form>
<? if(is_array($_SESSION['bp_form_data']) && (count($_SESSION['bp_form_data']) > 0)) { ?>
<form action="<?=$_action;?>" method="post">
<input type="hidden" name="mode" value="details">
<input type="hidden" name="user_action" value="summary">
<tr>
	<td colspan="2" align="right"><input class="button" type="submit" value="Przejdź do podsumowania"></td>
</tr>
</form>
<? } ?>
</table>
</div>

<script type="text/javascript">
document.getElementById('nazwa_nr').focus();
</script>