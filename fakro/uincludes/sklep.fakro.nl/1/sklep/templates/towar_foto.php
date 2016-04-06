<?
	if (C_PROJ_NAZWA_SYSMSG) $to_nazwa=sysmsg($to_nazwa,'towar');
	
	$IMG=file_exists("$UIMAGES/nofoto_$lang.gif")?$UIMAGES:$SKLEP_IMAGES;

	if (!strlen($to_foto_d)) $to_foto_d="$IMG/nofoto_$lang.gif";
	else
	{
		$fd=explode(":",$to_foto_d);
		if (count($fd)>1)
			$to_foto_d=$$fd[0]."/".$fd[1];
		else
			$to_foto_d=$UIMAGES."/".$fd[0];

		if (!file_exists($to_foto_d )) $pr_foto_d="$IMG/nofoto_$lang.gif";
	}

	if (!strlen($to_foto_s)) $to_foto_s="$IMG/nofoto_$lang.gif";
	else
	{
		$fm=explode(":",$to_foto_s);
		if (count($fm)>1)
			$to_foto_s=$$fm[0]."/".$fm[1];
		else
			$to_foto_s=$UIMAGES."/".$fm[0];

		if (!file_exists($to_foto_s)) $pr_foto_s="$IMG/nofoto_$lang.gif";
	}


	if (!strlen($to_foto_m)) $to_foto_m="$IMG/nofoto_$lang.gif";
	else
	{
		$fm=explode(":",$to_foto_m);
		if (count($fm)>1)
			$to_foto_m=$$fm[0]."/".$fm[1];
		else
			$to_foto_m=$UIMAGES."/".$fm[0];

		if (!file_exists($to_foto_m )) $pr_foto_m="$IMG/nofoto_$lang.gif";
	}

	if (strlen($to_att))
	{
		$fm=explode(":",$to_att);
		if (count($fm)>1)
			$to_att=$$fm[0]."/".$fm[1];
		else
			$to_att=$UFILES."/".$fm[0];
	}

?>
