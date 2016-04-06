<?
	if (strlen($_GET['sid'])) $sid=$_GET['sid'];
	if (strlen($_GET['table'])) $table=$_GET['table'];
	if (strlen($_GET['pole'])) $pole=$_GET['pole'];
	if (strlen($_GET['wart'])) $wart=$_GET['wart'];
	if (strlen($_GET['dir'])) $dir=$_GET['dir'];
	if (strlen($_GET['clibtd'])) $clibtd=$_GET['clibtd'];


	$STD_WHERE="lang='$lang' AND server=$SERVER_ID";
	if (isset($ver)) $STD_WHERE.=" AND ver=$ver";
	if (strlen($pole)) $STD_WHERE.=" AND $pole=$wart";

	$VS=-100;



	if ($table=="td")
	{
		$SRCH_AND="AND level IN (SELECT level FROM webtd WHERE $STD_WHERE AND sid=$sid)";
	}
	
	$query="SELECT pri FROM web$table WHERE sid=".$sid." AND ".$STD_WHERE; 
	parse_str(ado_query2url($query));

	if ($dir=="up") $query="SELECT pri as chpri, sid as chsid FROM web$table WHERE pri<".$pri." AND $STD_WHERE $SRCH_AND ORDER BY pri DESC LIMIT 1 "; 
	if ($dir=="down") $query="SELECT pri as chpri, sid as chsid FROM web$table WHERE pri>".$pri." AND $STD_WHERE $SRCH_AND ORDER BY pri LIMIT 1"; 

	//echo nl2br($query);return;
	parse_str(ado_query2url($query));

	if (!$chpri)
	{
		if ($dir=="up")	$query="SELECT max(pri)+1 AS chpri FROM web$table WHERE $STD_WHERE "; 
		if ($dir=="down") $query="SELECT min(pri)-1 AS chpri FROM web$table WHERE $STD_WHERE ";
		$sql = pg_query($query);
		list($chpri) = pg_fetch_row($sql); 
		//parse_str(ado_query2url($query));
	}

	

	if (isset($chpri)) 
	{
		if ($chsid>0){
			$query="UPDATE web$table SET pri=".$chpri." WHERE sid=".$sid." AND $STD_WHERE;
			UPDATE web$table SET pri=".$pri." WHERE sid=".$chsid." AND $STD_WHERE;
			";
		}
		else
		{
			$query="UPDATE web$table SET pri=".$chpri." WHERE sid=".$sid." AND $STD_WHERE;";
		}
			
	}
	else
	{
		/*
		$chpri=1;
		$query="UPDATE web$table SET pri=$VS WHERE pri=$pri AND $STD_WHERE;
			UPDATE web$table SET pri=pri+1 WHERE pri<>$VS AND $STD_WHERE;
			UPDATE web$table SET pri=1 WHERE pri=$VS AND $STD_WHERE;
			";
	 	*/
	}
	




	//echo nl2br($query);return;
	
	
	if (!strlen($error)) 
		if ($adodb->Execute($query)) 
		{
			if ($table=='td') webver_td($wart,$chpri,$action);

			logquery($query) ;
		}


