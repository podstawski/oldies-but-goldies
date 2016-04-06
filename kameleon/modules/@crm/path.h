<?
	global $WEBPAGE;

	$first=1;

	foreach (explode(":",$WEBPAGE->tree . $page) AS $v)
	{
		if (!strlen($v)) continue;
		$query="SELECT title FROM webpage WHERE ver=$ver AND lang='$lang' AND server=$SERVER_ID
				AND id=$v
				LIMIT 1";
			
		parse_str(ado_Query2url($query));

		if (!$first)
		{
			echo "&nbsp;->&nbsp;";
		}
		$first=0;

		echo "<a href=\"".kameleon_href("","",$v)."\">";
		echo "$title";
		echo "</a>";
	
	}



	
?>

