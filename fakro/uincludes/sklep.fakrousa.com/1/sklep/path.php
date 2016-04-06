<?
	global $WEBPAGE,$WEBTD;
	if (!is_object($WEBPAGE)) return;
	if ($WEBTD->page_id==$page) return;

	$adodb=$kameleon_adodb;

	$path="";
	foreach (explode(":",$WEBPAGE->tree) AS $p)
	{
		if (!$p) continue;
		if ($p==$WEBTD->page_id)
		{
			$juz=1;
			continue;
		}
		if (!$juz) continue;
		$query="SELECT title FROM webpage WHERE id=$p
				AND ver=$ver AND lang='$lang' AND server=$SERVER_ID";
		parse_str(ado_query2url($query));
		if (strlen($path)) $path.=" -> ";
		$path.="<a href=\"".kameleon_href("","",$p)."\">$title</a>";
	}
	if (strlen($path)) $path.=" -> ";
	$path.=$WEBPAGE->title;


	echo $path;
?>
