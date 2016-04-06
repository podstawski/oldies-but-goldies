<?
	global $BANNER, $sid;
	$xml="";

	
	//print_r($_REQUEST);



	if (!strlen($BANNER[ab_id]))
	{
		$sql = "INSERT INTO api2_baner
				(ab_html, ab_place, ab_server, ab_href, ab_limit, ab_textid, ab_d_start, ab_d_end, ab_target)
				VALUES ($BANNER[sid],'$BANNER[place]',$SERVER_ID,'$BANNER[href]',$BANNER[limit],'$BANNER[id]',";

		if (!strlen($BANNER[data_od]))
		$sql.= "NULL,"; else $sql.= "'".FormatujDateSql($BANNER[data_od])."',";

		if (!strlen($BANNER[data_do]))
		$sql.= "NULL,"; else $sql.= "'".FormatujDateSql($BANNER[data_do])."',";


		$sql.="'$BANNER[target]')";
		

		$adodb->execute($sql);

	}
	else
	{
		$sql = "UPDATE api2_baner SET
				ab_place = '$BANNER[place]', 
				ab_href = '$BANNER[href]', 
				ab_textid = '$BANNER[id]', 
				ab_limit = $BANNER[limit], ";

		if (!strlen($BANNER[data_od]))
		$sql.= "ab_d_start = NULL,"; else $sql.= "ab_d_start = '".FormatujDateSql($BANNER[data_od])."',";

		if (!strlen($BANNER[data_do]))
		$sql.= "ab_d_end = NULL,"; else $sql.= "ab_d_end = '".FormatujDateSql($BANNER[data_do])."',";

		$sql.= "ab_target = '$BANNER[target]'
				WHERE ab_id = $BANNER[ab_id]";

		//$adodb->debug = 1;
		$adodb->execute($sql);
		//$adodb->debug = 0;

	}

	$costxt = "";


	if ($BANNER[popup])
	{
		$costxt = $BANNER[pop_top].":".$BANNER[pop_left].":".$BANNER[pop_width].":".$BANNER[pop_height];
		$costxt.= ":".$BANNER[banner_place].":".$BANNER[close_text].":".$BANNER[close_img].":".$BANNER[closeplace].":".$BANNER[close_pixels];
	}

?>
