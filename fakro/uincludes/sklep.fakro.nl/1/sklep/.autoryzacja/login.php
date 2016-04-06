<?
	global $af;

	if ("$af[sid]"=="$WEBTD->sid")
	{
		$cos=$af[tak]+0;
		$kameleon_adodb->execute("UPDATE webtd SET cos=$cos WHERE sid=$WEBTD->sid");
	}
?>


<form method=post action="<?echo $self?>">
<input type="hidden" name="af[sid]" value="<?echo $WEBTD->sid?>">
<input type="checkbox" <?if ($cos) echo "checked"?> name="af[tak]" value=1> autofokus
<br>
<input type="submit" value="zapisz" class="but">
</form>
