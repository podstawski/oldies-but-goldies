<p class="wyszukiwarka">
	<form method="GET" action="<?echo $self ?>">
		<input type="text" name="list[szukaj]" class="sys_input" value="<?echo $LIST[szukaj]?>">
		<input type="submit" value="Szukaj" class="addbut">
		<? if ($KAMELEON_MODE) {?>
		<input type="hidden" name="page" value="<?echo $page?>">
		<? } ?>
	</form>
</p>
<?
	$szukaj=trim(addslashes($LIST[szukaj]));
?>
