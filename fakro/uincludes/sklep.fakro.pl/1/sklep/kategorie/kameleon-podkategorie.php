<?
	global $WEBPAGE,$WEBTD;
	if (!is_object($WEBPAGE)) return;

	$adodb=$kameleon_adodb;

	$query="SELECT id,title
		FROM webpage WHERE prev=$page 
		AND ver=$ver AND lang='$lang' AND server=$SERVER_ID
		AND (hidden IS NULL OR hidden=0)
		AND (nositemap IS NULL OR nositemap=0)
		ORDER BY title_short,title";
	$result=$adodb->Execute($query);

	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_explodeName($result,$i));
		$href=kameleon_href("","",$id);

		
		echo"<img src=\"$SKLEP_IMAGES/spacer.gif\" align=\"absMiddle\" height=20 width=2>
				<img src=\"$SKLEP_IMAGES/arr_r.gif\" align=\"absMiddle\">
				<a href=\"$href\" class=\"$class\">$title</a><br>";

	}
		$wynik.="\n</ul>";



?>
