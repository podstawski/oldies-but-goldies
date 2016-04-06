<?
$sql = "SELECT * FROM webpage 
			WHERE prev=".$page." 
				AND (nositemap!=1 OR nositemap IS NULL)
				AND server=".$SERVER_ID." 
				AND lang='".$lang."' 
				AND ver=".$ver." 
			ORDER BY title;";
$res = $adodb->execute($sql);

$maxrow = round($res->RecordCount()/3);

$menu = "<table width=\"100%\"><tr><td valign=\"top\">";
$menu.="<ul>";
for ($i=1;$i<=$res->RecordCount();$i++) 
{
	parse_str(ado_explodename($res, ($i-1)));	

	if (strlen($title_short)) $_title=$title_short;
	else $_title=$title;

	$menu.="<li><a href=\"".kameleon_href('','',$id)."\">".$title."</a>";
	if (!($i%$maxrow) && $i)
		$menu.="</ul></td><td valign=\"top\"><ul>";
}
$menu.="</ul>";
$menu.= "</td></tr></table>";
echo $menu;
?>