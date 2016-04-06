<?
	$t=explode(':',substr($tree,1).$page);

	$back='';
	$html='';

	push($adodb);
	$adodb=$kameleon_adodb;

	for ($i=count($t)-1;$i>=0;$i--)
	{
		$WP=kameleon_page($t[$i]);
		$WP=$WP[0];
		if (strlen($WP->background) && !strlen($back)) 
		{
			$backsrc=$WP->background;
			$back="background-image: url($UIMAGES/$backsrc)";
		}
		if (strlen($WP->fakro_header) && !strlen($html)) $html=$WP->fakro_header;

		if (strlen($back) && strlen($html)) break;
	}

	parse_str($costxt);

	$adodb=pop();

	$html=ereg_replace("uimages/[0-9]+/[0-9]+",$UIMAGES,$html);	
	$ext=strtolower(substr($backsrc,-3));
	if ($ext!='swf')
	{
		echo "<div style=\"$back\">";
		if (strlen($ctx_inc))
		{
			if ($KAMELEON_MODE) include("$INCLUDE_PATH/$ctx_inc");
			else echo '<? include("$INCLUDE_PATH/'.$ctx_inc.'")?>';
		}

		echo "$html</div>";
	}
	else
	{
		$a=getimagesize ("$KAMELEON_UIMAGES/$backsrc");
		if ($a[0]) $wtd->width=$a[0];
		if ($a[1]) $wtd->size=$a[1];

		$wtd->sid=$sid;
		$wtd->bgimg=$backsrc;

		$inside=kameleon_td2swf_obj($wtd);
		//$inside=htmlspecialchars($inside);

		echo '<table cellspacing=0 cellpadding=0><tr>';	
		echo "<td class=\"left\">$inside</td>";
		echo "<td class=\"right\">$html</td></tr></table>";
	}
?>
