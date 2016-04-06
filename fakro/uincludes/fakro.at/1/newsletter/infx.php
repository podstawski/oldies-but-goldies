<?
	include_once("$INCLUDE_PATH/newsletter2/infx_show.h");
	

	$INFX=explode("|",trim($WEBTD->costxt));

	if (count($INFX)<2) return;
	$width=round(100/(count($INFX)-2));
	$typ=$cos+0;
	$class="nl_lm_".$typ;
	
	$_lm_table = "<table cellspacing=\"0\" cellpadding=\"0\" align=\"center\" class=\"$class\"><tr>";
	for ($i=2;$i<count($INFX);$i++)	{
		$_lm_table.= "<td class=\"cl\" valign=\"top\" align=\"center\" width=\"".$width."%\">";
		$_lm_table.= "<div class=\"pl\" style=\"width: 100%; background-color: ".$INFX[0].";\">";
		$_lm_table.= rysuj_tabelke($WEBTD->cos,$INFX[0],$INFX[$i]);
		$_lm_table.= "</div>";
		$_lm_table.= "</td>\n";
	}
	$_lm_table.= "</tr></table>";
	
	echo $_lm_table;
		
	if ($WEBTD->more && strlen($INFX[1]) )
	{
		$href=kameleon_href("","",$WEBTD->more);
		echo "<div class=\"nl_more_$typ\"><a href=\"$href\">$INFX[1]\n";
		echo "<img src=\"$UIMAGES/newsletter/more_$typ.gif\" border=0 align=absMiddle></a></div>";
	}

?>
