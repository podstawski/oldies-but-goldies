<?
	global $printlist;
	global $location_back;
?>

<span class="printlist">

<span id="_printlist">
<input class="button" type="button" value="<?echo sysmsg('Print')?>" 
	onClick="document.getElementById('_printlist').style.display='none'; window.print(); document.getElementById('_printlist').style.display='';">
<input class="button" type="button" value="<?echo sysmsg('Back')?>" onClick="location.href='<?echo $location_back?>'">
</span>


<?
	if (!is_array($printlist)) return;


	foreach (array_keys($printlist) AS $za_id)
	{
		echo $WM->zamowienie($za_id);
		echo $WM->produkty_zamowienia($za_id);
		echo '<hr size="1">';
	}
?>

</span>