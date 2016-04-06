<?
	$_grupa="";
	parse_str($WEBTD->costxt);
	$options="";

	global $GRUPY;
	if ("$GRUPY[sid]"=="$WEBTD->sid")
	{
		$ct=$WEBTD->costxt;
		$pos=strpos($ct,"&_grupa=");
		$_grupa=$GRUPY[grupa];

		if ($pos) $ct=substr($ct,0,$pos);
		$ct.="&_grupa=$_grupa";
		$kameleon_adodb->Execute("UPDATE webtd SET costxt='$ct' WHERE sid=$WEBTD->sid");
	}


	$query="SELECT gt_grupa AS g FROM grupy_towarow GROUP BY gt_grupa";
	$res=$projdb->Execute($query);

	for ($i=0;$i<$res->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		$sel=($g==$_grupa)?"selected":"";
		$options.="<option $sel value=\"$g\">$g</option>";
	}


?>
<form method="post" action="<?echo $self ?>">
<input type="hidden" name="GRUPY[sid]" value="<?echo $WEBTD->sid?>">
<select name="GRUPY[grupa]">
        <option value="">Wybierz</option>
        <?echo $options?>
</select>
<input type="submit" class="but" value="Zapisz">
</form>
