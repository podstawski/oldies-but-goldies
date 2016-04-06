<?
	global $hf_editmode;
	if ($WEBTD->page_id<0 && !$hf_editmode) 
	{
		include("$SKLEP_INCLUDE_PATH/template.php");
		return;
	}

	global $TEMPL;

	parse_str($costxt);


	if ("$TEMPL[sid]"=="$WEBTD->sid")
	{
		$filename=$TEMPL[filename];
		$cache=$TEMPL[cache];
		$cachev=$TEMPL[cache_var];
		$costxt="filename=$filename&cache=$cache&cachev=$cachev";
		$kameleon_adodb->Execute("UPDATE webtd SET costxt='$costxt' WHERE sid=$WEBTD->sid");
		$WEBTD->costxt=$costxt;
		echo "Zaktualizowano<br />";
	}



	$dh = opendir("$SKLEP_INCLUDE_PATH/templates"); 
	while (($file = readdir($dh)) !== false) 
	{
		if ($file[0]==".") continue;
		$ffile="$SKLEP_INCLUDE_PATH/templates/$file";
		if (!is_dir($ffile) && !is_link($ffile)) continue;
		if (substr($file,0,7)=="action_") continue;

		$templates[]=$file;
	}
	sort($templates);

	$options="";
	foreach ($templates AS $file)
	{
		$sel=($file==$filename)?"selected":"";
		$options.="<option $sel value=\"$file\">$file</option>";
	}


?>
<form method="post" action="<?echo $self ?>">
<input type="hidden" name="TEMPL[sid]" value="<?echo $WEBTD->sid?>">
<select name="TEMPL[filename]" style="width:130">
	<option value="">Wybierz szablon</option>

	<?echo $options?>
</select> <I>wybierz szablon</I><BR>
<input type="text" style="width:130" name="TEMPL[cache]" value="<?echo $cache+0?>">
<I>czas cache (0=bez cache)</I><BR>

<input type="text" style="width:130" name="TEMPL[cache_var]" value="<?echo $cachev?>">
<I>zmienne cache</I><BR>


<input type="submit" class="but" value="Zapisz">
</form>

<?
	if (file_exists("$SKLEP_INCLUDE_PATH/templates/$filename/editmode.php"))
	{
		echo "<hr size=1>";
		include("$SKLEP_INCLUDE_PATH/templates/$filename/editmode.php");
	}
?>
