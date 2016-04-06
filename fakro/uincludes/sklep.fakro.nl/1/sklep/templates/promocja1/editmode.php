<?

	if ($next_id!=$WEBTD->next)
	{
		$pos=strpos($costxt,"&next_id=");
		if ($pos) $costxt=substr($costxt,0,$pos);

		$costxt.="&next_id=".$WEBTD->next;
		$sql = "UPDATE webtd SET costxt='$costxt' WHERE sid = $WEBTD->sid";
		$kameleon_adodb->execute($sql);
	}


	if ( "$FORM[sid]" == "$WEBTD->sid")
	{
		$pos=strpos($costxt,"&prom=");
		if ($pos) $costxt=substr($costxt,0,$pos);

		$prom=$FORM[prom];
		$costxt.="&prom=$prom";
		$sql = "UPDATE webtd SET costxt='$costxt' WHERE sid = $WEBTD->sid";
		$kameleon_adodb->execute($sql);
	}	

	global $DEFSORT;
	
	if (strlen($DEFSORT) && "$FORM[sid]" == "$WEBTD->sid")
	{
		$costxt.="&def_sort=".$DEFSORT;
		$sql = "UPDATE webtd SET costxt='$costxt' WHERE sid = $WEBTD->sid";
		$kameleon_adodb->execute($sql);
		$def_sort=$DEFSORT;
	}




	
	$options="";

	$query="SELECT * FROM promocja WHERE pm_koniec IS NULL OR pm_koniec>$NOW";
	$result = pg_exec($query);
	for ($i=0;$i<pg_numrows($result);$i++)
	{
		parse_str(pg_ExplodeName($result,$i));

		$sel=($pm_id==$prom)?"selected":"";

		$options.="<option $sel value=$pm_id>$pm_symbol";
	}


	ob_start();
	?>

		<select name="DEFSORT" class="formselect" >
		<option value="">Sortuj wg</option>
		<option value="ts_cena" <?if ($def_sort=='ts_cena') echo 'selected';?>>Cena</option>
		<option value="to_nazwa" <?if ($def_sort=='to_nazwa') echo 'selected';?>>Nazwa</option>
		<option value="to_indeks" <?if ($def_sort=='to_indeks') echo 'selected';?>>Indeks</option>
		<option value="ts_pri,to_nazwa" <?if ($def_sort=='ts_pri,to_nazwa') echo 'selected';?>>Ustal</option>
		<option value="ts_pri2,to_nazwa" <?if ($def_sort=='ts_pri2,to_nazwa') echo 'selected';?>>Ustal 2</option>
		</select>
	<?
	$sort=ob_get_contents();
	ob_end_clean();
	

	echo "
	<FORM METHOD=POST ACTION=\"$self\">
	<INPUT TYPE=\"hidden\" name=\"form[sid]\" value=\"$WEBTD->sid\">
	$sort
	<SELECT name=\"form[prom]\">
	<option value=\"\">Wybierz promocjê
	$options
	</SELECT>

	<INPUT TYPE=\"submit\" value=\"Zapisz\"></FORM>";



?>
