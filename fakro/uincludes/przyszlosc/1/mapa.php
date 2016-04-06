<style type="text/css">
	.mapa	{}
	.mapa .parent	{}
	.mapa .parent	{margin-left: 20px;}
	.mapa .parent a	{}
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

if (!$WEBTD->staticinclude)
{
	$sql = "UPDATE webtd SET staticinclude=1 WHERE sid=".$WEBTD->sid;
	$adodb->execute($sql);
}

function mapa($_root) {
	global $SERVER_ID, $lang, $ver, $adodb,$IMAGES;

	$sql = "SELECT * FROM webpage 
			WHERE prev=".$_root." 
				AND (nositemap!=1 OR nositemap IS NULL)
				AND server=".$SERVER_ID." 
				AND lang='".$lang."' 
				AND ver=".$ver." 
			ORDER BY title;";
	$res = $adodb->execute($sql);
	
	for ($i=0;$i<$res->RecordCount();$i++) {
		parse_str(ado_explodename($res, $i));	
		
		if (strlen($title_short)) $_title=$title_short;
		else $_title=$title;
	
		$_href_b="";$_href_e="";
		if (!$hidden) {
			$_href_b="<a href=\"".kameleon_href('','',$id)."\">";
			$_href_e="</a>";
		}
		
		$sqlc = "SELECT count(*) AS child_count FROM webpage WHERE prev=".$id." 
				AND (nositemap!=1 OR nositemap IS NULL)
				AND server=".$SERVER_ID." 
				AND lang='".$lang."' 
				AND ver=".$ver;
		$resc = $adodb->execute($sqlc);
		parse_str(ado_explodename($resc, 0));	
		
		if ($child_count) 
			$_link = " class=\"plus\" onclick=\"mapa_sh('ml".$id."',this)\"";
		else
			$_link = " class=\"no\"";
		
		$mapa.= "<div class=\"parent\">";
		$mapa.= "<img src=\"".$IMAGES."/sp.gif\"".$_link.">";
		$mapa.= $_href_b.$_title.$_href_e;
		
		if ($child_count) $mapa.= "<div id=\"ml".$id."\" class=\"child\" style=\"visibility: hidden; display: none;\">".mapa($id)."</div>";
		$mapa.= "</div>";
	}	
	
	return "<div class=\"mapa\">".$mapa."</div>";	
}

function mapa_xml($_root) {
	global $SERVER_ID, $lang, $ver, $adodb,$HTTP_SERVER;
	
	$sql = "SELECT * FROM webpage 
			WHERE prev=".$_root." 
				AND (nositemap!=1 OR nositemap IS NULL)
				AND server=".$SERVER_ID." 
				AND lang='".$lang."' 
				AND ver=".$ver.";";
			
	$res = $adodb->execute($sql);
	
	for ($i=0;$i<$res->RecordCount();$i++) 
	{
		parse_str(ado_explodename($res, $i));	
		$xml_map.= "<url>\n";
		$xml_map.= "<loc>".$HTTP_SERVER."/".kameleon_href('','',$id)."</loc>\n";
		if ($d_ftp) $xml_map.= "<lastmod>".$d_ftp."</lastmod>\n";
		$xml_map.= "</url>\n";
		
		$xml_map.= mapa_xml($id);
	}	
	return $xml_map;	
}


if (strlen($costxt)) parse_str($costxt);
if (!strlen($map_root)) $map_root=0;
if (!strlen($map_level)) $map_level=3;

echo mapa($map_root);


if (!$KAMELEON_MODE && $ver < 10)
{
	$xml_file_src = $KAMELEON_UFILES."/mapa.xml";
	
	$xml_map = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
	$xml_map.= "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n";
	$xml_map.= "<url>\n";
	$xml_map.= "<loc>".$HTTP_SERVER."</loc>\n";
	$xml_map.= "<lastmod>".date('Y-m-d')."</lastmod>\n";
	$xml_map.= "<changefreq>hourly</changefreq>\n";
	$xml_map.= "<priority>1.0</priority>\n";
	$xml_map.= "</url>\n";
	$xml_map.= mapa_xml($map_root);
	$xml_map.= "</urlset>\n";
	
	$xml_file = fopen($xml_file_src,"w");
	fwrite($xml_file,$xml_map);
	fclose($xml_file);

	$xml_file = fopen($xml_file_src,"r");
	
	$query="SELECT ftp_server, ftp_dir, ftp_pass, ftp_user FROM servers WHERE id=$SERVER_ID";
	parse_str(ado_query2url($query));

	$ftp_id = ftp_connect($ftp_server);
	if (@ftp_login($ftp_id, $ftp_user, $ftp_pass)) 
	{	
		if (strlen($ftp_dir)) ftp_chdir($ftp_id,$ftp_dir); 
		ftp_fput($ftp_id, "sitemap.xml", $xml_file, FTP_ASCII);
	}
	ftp_close($ftp_id);
	fclose($xml_file);

}

?>
