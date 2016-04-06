<?
	$t=explode(':',substr($tree,1).$page);

	$back='';
	$html='';
	$inside='';

	push($adodb);
	$adodb=$kameleon_adodb;

	$mid=0;

	for ($i=count($t)-1;$i>=0;$i--)
	{
		$WP=kameleon_page($t[$i]);
		$WP=$WP[0];

		if ($WP->menu_id && !$mid) $mid=$WP->menu_id;
		if (strlen($WP->background) && !strlen($back) ) $back=$WP->background;

		continue;
		//stare
		if (strlen($WP->background) && !strlen($back)) $back="background-image: url($UIMAGES/".$WP->background.")";
		if (strlen($WP->fakro_header) && !strlen($html)) $html=$WP->fakro_header;
		if (strlen($back) && strlen($html)) break;
	}

	//echo "$back -> $mid ";

	parse_str($costxt);

	if (strlen($back))
	{
		if (strstr($back,'swf') && $mid)
		{
			$wtd=null;

			$m=kameleon_menus($mid);
			$a=getimagesize ("$KAMELEON_UIMAGES/".$m[0]->img);
			if ($a[0]) $wtd->width=$a[0];
			if ($a[1]) $wtd->size=$a[1];
			$wtd->sid=$sid;
			$wtd->bgimg=$back;
			$wtd->menu_id=$mid;
			$inside=kameleon_td2swf_obj($wtd);
			
		}
	}


	$adodb=pop();


	if ($page)
	{
		echo '<table cellspacing=0 cellpadding=0><tr>';
		echo "<td class=\"left\">$inside</td>";
		echo "<td class=\"right\">";


		if (strlen($ctx_inc))
		{
			if ($KAMELEON_MODE) include("$INCLUDE_PATH/$ctx_inc");
			else echo '<'.'? parse_str("next='.urlencode($next).'&more='.urlencode($more).'&self='.urlencode($self).'");
					include("$INCLUDE_PATH/'.$ctx_inc.'")?'.'>';
		}
		$html=ereg_replace("uimages/[0-9]+/[0-9]+",$UIMAGES,$html);	
		echo $html;
		echo "</td></tr></table>";
	}
	else echo $inside;
	
?>
