<?
	parse_str($WM->table_row2url("producent",array("pr_id"=>$to_pr_id),true));

	
	if (!strlen($pr_logo_d)) $pr_logo_d="$SKLEP_IMAGES/spacer.gif";
	else
	{
		$fm=explode(":",$pr_logo_d);
		if (count($fm)>1)
			$pr_logo_d=$$fm[0]."/".$fm[1];
		else
			$pr_logo_d=$UIMAGES."/".$fm[0];

		if (!file_exists($pr_logo_d )) $pr_logo_d="$SKLEP_IMAGES/spacer.gif";
	}


	if (!strlen($pr_logo_m)) $pr_logo_m="$SKLEP_IMAGES/spacer.gif";
	else
	{
		$fm=explode(":",$pr_logo_m);
		if (count($fm)>1)
			$pr_logo_m=$$fm[0]."/".$fm[1];
		else
			$pr_logo_m=$UIMAGES."/".$fm[0];	

		if (!file_exists($pr_logo_m )) $pr_logo_m="$SKLEP_IMAGES/spacer.gif";
	}


?>
