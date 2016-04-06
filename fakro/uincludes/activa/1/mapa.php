<style type="text/css">
	.mapa	{}
	.mapa .parent	{font-size: 12px; }
	.mapa .parent	{margin-left: 20px;}
	.mapa .parent a	{font-size: 12px;}
	.mapa .parent .plus	{ background-image: url(<?echo $IMAGES?>/mapa_plus.gif)}
	.mapa .parent .minus	{ background-image: url(<?echo $IMAGES?>/mapa_minus.gif)}
	.mapa .parent .no 	{ background-image: url(<?echo $IMAGES?>/mapa_no.gif)}
	.mapa .parent .plus, .parent .minus, .parent .no {width: 9px; height: 9px; margin-right: 10px; display: inline;}
</style>

<script language="JavaScript" type="text/javascript">
function mapa_sh(objid,objimg) {
	styleObject = getStyleObject(objid);
	if (styleObject.visibility == 'hidden') {
		styleObject.visibility = 'visible';
		styleObject.display = 'inline';
		objimg.className='minus';
	}
	else {
		styleObject.visibility = 'hidden';
		styleObject.display = 'none';
		objimg.className='plus';
	}
	return;
}
</script>
<?
global $WEBPAGE;

//push($adodb);
//$adodb=$kameleon_adodb;
//$adodb->debug=1;
if(!$WEBTD->staticinclude) {
	$sql = "UPDATE webtd SET staticinclude=1 WHERE sid=".$WEBTD->sid;
	pg_exec($sql);
	}

function mapa($_root) {
	global $SERVER_ID, $lang, $ver, $adodb,$IMAGES,$db;
	
	$sql = "SELECT * FROM webpage 
			WHERE prev=".$_root." 
			AND (nositemap=0 OR nositemap IS NULL)
			AND server=".$SERVER_ID." 
			AND lang='".$lang."' 
			AND ver=".$ver." 
			ORDER BY title;";
	$res = pg_exec($db,$sql);
	
	for($i = 0;$i < pg_numrows($res); $i++) {
		parse_str(pg_explodename($res, $i));
		if(strlen($title_short)) $_title = $title_short;
			else $_title = $title;
		
		$_href_b = ""; $_href_e = "";
		if(!$hidden || !$nositemap) {
			$_href_b="<a href=\"".kameleon_href('','',$id)."\">";
			$_href_e="</a>";
			}
		
		$sqlc = "SELECT count(*) AS child_count FROM webpage WHERE prev=".$id." 
				AND (nositemap=0 OR nositemap IS NULL)
				AND server=".$SERVER_ID." 
				AND lang='".$lang."' 
				AND ver=".$ver;
		$resc = pg_exec($db,$sqlc);
		parse_str(pg_explodename($resc, 0));
		
		if($child_count) $_link = " class=\"plus\" onclick=\"mapa_sh('ml".$id."',this)\"";
			else $_link = " class=\"no\"";
		
		$mapa .= "<div class=\"parent\">";
		$mapa .= "<img src=\"".$IMAGES."/sp.gif\"".$_link.">";
		$mapa .= $_href_b.$_title.$_href_e;
		
		if ($child_count) $mapa .= "<div id=\"ml".$id."\" class=\"child\" style=\"visibility: hidden; display: none;\">".mapa($id)."</div>";
		$mapa .= "</div>";
		}
	return "<div class=\"mapa\">".$mapa."</div>";
	}

if(strlen($costxt)) parse_str($costxt);
if(!strlen($map_root)) $map_root = 0;
if(!strlen($map_level)) $map_level = 3;

echo mapa($map_root);

//print_r($WEBPAGE);
//$adodb=pop();
?>
