<?

$maxcol=3;
if ($WEBTD->size) $maxcol = $WEBTD->size;


if ($WEBTD->menu_id)
{
	$menu=kameleon_menus($WEBTD->menu_id);
	
	$ret_menu = "<table width=\"100%\"><col width=".(100/$maxcol)."%><tr>";
	for ($i=0; $i<count($menu); $i++)
	{
		$ret_menu.="<td>";
		$ret_menu.= "<a href=\"".kameleon_href('','pogoda='.$menu[$i]->page_target,$WEBTD->page_id)."\">".$menu[$i]->alt."</a>";
		$ret_menu.="</td>";
		
		if (!(($i+1)%$maxcol)) $ret_menu.="</tr><tr>";
				
	}
	$ret_menu.= "</tr></table>";
}

echo $ret_menu;

?>
