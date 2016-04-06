<?
 if ($SERVICE!="news")
 {
  $api_action="";
	return;
 }

	$api_action="";

	$VS=-100;

	if ($api_dir=="up") $query="SELECT max(pri) AS chpri FROM webaktual WHERE pri<$api_pri "; 
	if ($api_dir=="down") $query="SELECT min(pri) AS chpri FROM webaktual WHERE pri>$api_pri "; 

   $result=$adodb->Execute($query);

	parse_str(ado_ExplodeName($result,0));

	//echo nl2br($query);return;

	if (!$chpri) $error=label("Operation not permited!");
	else
	{
		$query="UPDATE webaktual SET pri=$VS WHERE pri=$chpri ;
			UPDATE webaktual SET pri=$chpri WHERE pri=$api_pri;
			UPDATE webaktual SET pri=$api_pri WHERE pri=$VS ;
			";
	}
	//echo nl2br($query);return;
	if (!strlen($error)) $adodb->Execute($query);

?>