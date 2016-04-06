<?
function sklep_tokens($t)
{
	global $WEBPAGE, $KAMELEON_MODE, $UIMAGES, $WEBTD, $WEBLINK, $IMAGES, $KAMELEON_UIMAGES;
	global $page, $ver, $lang,$editmode;
	global $LINK_TYPY,$tokens;
	global $SZABLON_PATH,$SERVER_ID;

	$KARTOTEKA_LEVEL=7;
	$SKLEP_TYPES=array(2,4,5,50);
	
	$pages_list = explode(":",$WEBPAGE->tree);
	$pages_list[] = $page;
	switch ($t)	
	{
	
		case "INCLUDE_AUTH":
			if (!in_array($WEBPAGE->type,$SKLEP_TYPES)) return ' '; 
			$sql = "SELECT next AS loginpage FROM webpage WHERE id = 0 
					AND server = $SERVER_ID AND lang = '$lang' AND ver = $ver LIMIT 1";
			parse_str(ado_query2url($sql));
			if (!strlen($loginpage)) $loginpage = 22;
			$LOGIN_PHP=kameleon_href('','err=1',$loginpage);
			return "<? \$LOGIN_PHP='$LOGIN_PHP';include(\"\$SKLEP_INCLUDE_PATH/autoryzacja/auth.h\"); ?>";

		case "SECTION_MODULE_AUTH_BEGIN":
			if (!in_array($WEBPAGE->type,$SKLEP_TYPES)) return ' '; 
			return "<? if (\$AUTH[id] < 0 || !strlen(\$AUTH[id])) { ?>";

		case "SECTION_MODULE_RIGHT_BEGIN":
			if (!in_array($WEBPAGE->type,$SKLEP_TYPES)) return ' '; 
			$p_more = $WEBTD->more;
			$p_next = $WEBTD->next;

			if (strlen($p_more) && strlen($p_next))
				$ret = "<? if (haveRight(\"p_".$p_more."\",\$AUTH[c_id],\"\") || haveRight(\"p_".$p_next."\",\$AUTH[c_id],\"\")) {?>";
			elseif (strlen($p_next))
				$ret = "<? if (haveRight(\"p_".$p_next."\",\$AUTH[c_id],\"\")) {?>";
			elseif (strlen($p_more))
				$ret = "<? if (haveRight(\"p_".$p_more."\",\$AUTH[c_id],\"\")) {?>";
			else
				$ret = "<? if (true) {?>";

			return $ret;

		case "SECTION_PAGE_RIGHT_BEGIN":
			if (!in_array($WEBPAGE->type,$SKLEP_TYPES)) return ' '; 
			$pid="p_".$WEBPAGE->id;
			return "<? if (haveRight(\"$pid\",\$AUTH[c_id],\"\")) {?>";

		case "SECTION_LINK_RIGHT_BEGIN":
			if (!in_array($WEBPAGE->type,$SKLEP_TYPES)) return ' '; 
			return "<? if (haveRight(\"\",\$AUTH[c_id],\"".$WEBLINK->page_target."\")) {?>";

		case "SECTION_RIGHT_END":
			if (!in_array($WEBPAGE->type,$SKLEP_TYPES)) return ' '; 
			return "<? } ?>";

		case "SECTION_PAGE_RIGHT_END":
			if (!in_array($WEBPAGE->type,$SKLEP_TYPES)) return ' ';
			global $HTTP_HOST;
			$sql = "SELECT next AS loginpage FROM webpage WHERE id = 0
					AND server = $SERVER_ID AND lang = '$lang' AND ver = $ver LIMIT 1";
			parse_str(ado_query2url($sql));
			if (!strlen($loginpage)) $loginpage = 22;
			return $KAMELEON_MODE?" ":("<? } else echo \"<script>location.href='".kameleon_href('','err=1',$loginpage)."'</script>\";?>");


		case "WEBBODY_LEVEL_MANAGEMENT":
			$td=kameleon_td($WEBPAGE->id,$WEBPAGE->ver,$WEBPAGE->lang,$KARTOTEKA_LEVEL);
			print_r($WEBTD);
			for ($i=0;$i<count($td) && is_array($td);$i++)
			{
				if ($td[$i]->hidden) continue;
				$sid=$td[$i]->sid;
				$onclick="popupBookmarkClick($sid)";
				$wynik.="<td onClick=\"$onclick\" id=\"mbid_$sid\">".$td[$i]->title."</td>";
			}
			if (!strlen($wynik)) $wynik = " ";
			return $wynik;

		case "WEBODY_BOOKMARK_SELECT":
			if ($KAMELEON_MODE) return "popupWindows[0]";
			$_ret="<?";
			$_ret.=" echo (\$CIACHO[\"kart_\$page\"])?\$CIACHO[\"kart_\$page\"]:\"popupWindows[0]\" ?>";
			return $_ret;
		case "SID":
                        return $WEBTD->sid;

		default:
		return "";
	}
}
?>
