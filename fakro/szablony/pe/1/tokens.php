<?
function tokens($t)
{
	global $WEBPAGE, $KAMELEON_MODE, $UIMAGES, $WEBTD, $WEBLINK, $IMAGES, $KAMELEON_UIMAGES, $INCLUDE_PATH;
	global $page, $ver, $lang,$editmode;
	global $LINK_TYPY,$tokens;
	global $SZABLON_PATH;
	global $DEFAULT_PATH_KAMELEON_UINCLUDES;
	global $SERVER_NAME;

	$pages_list = explode(":",$WEBPAGE->tree);
	$pages_list[] = $WEBPAGE->id;
	
	switch ($t)	{
	
		case "KAMELEON_META_ADD":
		case "WEBPAGE_DATE":
		case "WEBPAGE_META_DESCRIPTION":
		case "WEBPAGE_META_KEYWORDS":
			$tk = $t;
			if ($t=="WEBPAGE_META_DESCRIPTION") $tk = "metadesc";
			if ($t=="WEBPAGE_META_KEYWORDS") $tk = "metakey";
			$ret = str_replace(">","/>",$tokens[$tk]);
			return $ret;
		break;	
		
		case "GOOGLE_VERIFY": 
			return " ";
      
      $ret = " ";
			if (!$page)
				$ret = "<meta name=\"verify-v1\" content=\"\" />";
			return $ret;
		break;	
		
		case "PAGE_BODY_TABLE": 
			$page_valign_begin = " ";
			if ($page)
			{
				$_height = 275 + 130;
				$_height = 275 + 90;
				$page_valign_begin = "
					<script type=\"text/javascript\">
						var maintableObj = getObject('mt');
						maintableObj.setAttribute('height',document.documentElement.clientHeight - ".$_height.");
					</script>";	
			}		
			return $page_valign_begin; 
		break;  
		
		case "WEBPAGE_GOOGLE_ANALITICS":
			return " ";
      
      $ret = "<script src=\"http://www.google-analytics.com/urchin.js\" type=\"text/javascript\">";
			$ret.= "
			</script>
			<script type=\"text/javascript\">
			_uacct = \"\";
			urchinTracker();
			</script>";
			if ($KAMELEON_MODE) $ret = " ";
			return $ret; 
		break; 
		
		case "WEBPAGE_BODYCLASS": 
			$ret = " class=\"page".($WEBPAGE->type+0)."\"";
			return $ret; 
		break; 
		
		case "WEBPAGE_TEXTSTYLE_CSS": 
			$_ret  = "<link href=\"".$IMAGES."/textstyle.css\" rel=\"stylesheet\" type=\"text/css\" />\n"; 
			$_ret .= "<link href=\"".$IMAGES."/szablon.css\" rel=\"stylesheet\" type=\"text/css\" />"; 
			return $_ret; 
		break;
		
		case "KAMELEON_META_ADD": 
			$ret = " ";
			if ($page) {
				$ret.= "<meta name=\"WebKameleonId\" content=\"".$page.":".$ver.":".$lang."\"/>\n";
			}	
			return $ret; 
		break; 
		
		case "WEBPAGE_STYLE": 
			$ret = "style=\"";
			$ret.= "margin:0px;";
      $ret.= "background: #ebeef1;";
			$ret.= "\"";
			return $ret; 
		break;	

		case "WEBPAGE_DATE": 
			$_ret = "<meta name=\"date\" content=\"";
			$_ret .= date("r");
			$_ret .= "\">";
			return $_ret; 
		break; 
		
		case "WEBKAMLEON_FOOTER": 
			return 	"
				<div class=\"st\">
				<A href=\"http://www.gammanet.pl/\" target=_blank>created by gammanet</A> | <A href=\"javascript:sc()\">site credits</A></div>"; 
			return 	"
				<a href=\"http://webkameleon.com\" target=\"_blank\">
				<img src=\"$IMAGES/web_stopka_1.gif\" width=115 height=13 hspace=0 vspace=0 border=0 alt=\"CMS: webkamleon\"></a>"; 
		break;
		
		case "FLASH_MENU": 
			parse_str($WEBTD->costxt);
			return $flash_menuname;
		break; 
		
		case "WEBPAGE_HEADER_BANNER": 
			$img_src = $IMAGES."/baner.png";
			if (is_array($pages_list)) {
				for ($i=count($pages_list); $i >= 0 ; $i--) {	
					if (strlen($pages_list[$i])){							
						$PREV_WEBPAGE=kameleon_page($pages_list[$i]);
						$_head_img = $PREV_WEBPAGE[0]->background;
						if (!strlen($_head_img)) continue;
						$img_src = $UIMAGES."/".$_head_img;
						
						$isbanner_flash=0;
						if (substr($_head_img,strpos($_head_img,"."))==".swf")	$isbanner_flash=1;
						break;
					}	
				}
			}
			if ($isbanner_flash)
			{
				$ufo_majorversion = 6;
				$ufo_build = 40;
				$flash_height = 287;
				if ($WEBPAGE->type) $flash_height = 140;
				
				$files = "";
				if (!$page)
					$files = "so.addVariable(\"files\", \"".$UIMAGES."/baner/home/reklama1;/".$UIMAGES."/baner/home/1;/".$UIMAGES."/baner/home/2;./".$UIMAGES."/baner/home/3;./".$UIMAGES."/baner/home/4;./".$UIMAGES."/baner/home/5\");";
				/*
					$files = "so.addVariable(\"files\", \"".$UIMAGES."/baner/home/1;/".$UIMAGES."/baner/home/2;./".$UIMAGES."/baner/home/3;./".$UIMAGES."/baner/home/4;./".$UIMAGES."/baner/home/5\");";

				if ($page==500)
					$files = "so.addVariable(\"files\", \"".$UIMAGES."/baner/home/reklama1;/".$UIMAGES."/baner/home/1;/".$UIMAGES."/baner/home/2;./".$UIMAGES."/baner/home/3;./".$UIMAGES."/baner/home/4;./".$UIMAGES."/baner/home/5\");";
				*/				
				$_ret = "
				<div id=\"kam_swf_".$WEBTD->sid."\">FLASH</div>
				<script type=\"text/javascript\">
					// <![CDATA[
					if (SWFObject == 0) alert(\"This page requires swfobject.js file.\");
					else {
						var so = new SWFObject(\"".$img_src."\", \"dga\", \"900\", \"".$flash_height."\", \"7\", \"\");
						// use semicolon to split path to files (without extensions)
						$files		
						so.addParam(\"wmode\", \"transparent\");
						so.addParam(\"showmenu\", \"false\");
						so.addParam(\"allowscriptaccess\", \"sameDomain\");
						so.addParam(\"scale\", \"noscale\");
						so.write(\"kam_swf_".$WEBTD->sid."\");
					}
					// ]]>
				</script>
				";
				
			}
			else
			{
				$_ret  = "<img src=\"".$img_src."\" alt=\"\" width=\"900\" height=\"140\"/>"; 
			}
			//$_ret  = $isbanner_flash;
			return $_ret; 
		break;
		
		case "FLASH_PLAIN": 
			$ufo_majorversion = 6;
			$ufo_build = 40;

			parse_str($WEBTD->costxt);

			$flash_bgcolor="";
			$flash_vars="";
			
			$flash_src = $UIMAGES."/".$WEBTD->bgimg;
			$flash_src_kam = $KAMELEON_UIMAGES."/".$WEBTD->bgimg;
			
			if (file_exists($flash_src_kam)) {
				$flash_size_arr = getimagesize($flash_src_kam);
				if (is_array($flash_size_arr)) {
					if (!strlen($flash_width))	$flash_width = $flash_size_arr[0];
					if (!strlen($flash_height))	$flash_height = $flash_size_arr[1];
				}	
			}
			
			$ufo_addparams = "";
			$ufo_addparams.= ",wmode:\"transparent\"";
			$ufo_addparams.= ",allowscriptaccess:\"sameDomain\"";
			if ($WEBTD->bgcolor)
				$ufo_addparams.= ",bgcolor:\"".$WEBTD->bgcolor."\"";
			if (strlen($flash_loop)) 
			{
				$ufo_addparams.= ",loop:\"";
				if ($flash_loop) $ufo_addparams.= "\"true\"";
				else $ufo_addparams.= "\"false\"";
				$ufo_addparams.= "\"";
			}
			
			if (strlen($flash_clicktag)) {
				$flash_src.="?clickTag=".$flash_clicktag;
			}	
			elseif (strlen($WEBTD->next)) {
				$flash_src.="?clickTag=".kameleon_href('','',$WEBTD->next);
			}	
			
			$flash_plain = "
				<div id=\"kam_swf_".$WEBTD->sid."\">FLASH</div>
				<script type=\"text/javascript\">
			    var FO={movie:\"".$flash_src."\",width:\"".$flash_width."\",height:\"".$flash_height."\",majorversion:\"".$ufo_majorversion."\",build:\"".$ufo_build."\"".$ufo_addparams."};
			    UFO.create(FO, \"kam_swf_".$WEBTD->sid."\");
			    </script>
				";
			return $flash_plain;
		break; 
		
		case "FLASH_MENU_POS": 
			global $menu_pos, $menu_id, $fmenu_id;
			
			if (!strlen($fmenu_id)) $fmenu_id=$menu_id; 
			if ($fmenu_id != $menu_id)	$menu_pos = 0;
			$fmenu_id=$menu_id;
			$menu_pos++;
			
			$flash_menu_link="";
			if (strlen($WEBLINK->page_target)) {
				$flash_menu_link = kameleon_href("",$WEBLINK->variables,$WEBLINK->page_target);
			}
			elseif ($WEBLINK->href) {
				$flash_menu_link = $WEBLINK->href;
				if ($WEBLINK->variables) $flash_menu_link.="?".$WEBLINK->variables;
			} 
			
			$flash_menu = " ";
			$flash_menu.= "case $menu_pos: window.location.href='".$flash_menu_link."';break;"; 
			return $flash_menu;
		break; 
		
		case "WEBTD_STYLE":
			$_ret = " ";
			if (strlen($WEBTD->costxt)) parse_str($WEBTD->costxt);
			
			if (strlen($WEBTD->bgcolor) || strlen($WEBTD->width) || strlen($WEBTD->align) || strlen($WEBTD->valign) || strlen($WEBTD->costxt) || strlen($tdstyle)) {
				$_ret = " style=\"";

				if (strlen($WEBTD->bgcolor)) $_ret.= "background-color:#".$WEBTD->bgcolor.";";
				if (strlen($WEBTD->width)) $_ret.= "width:".$WEBTD->width."px;";
				if (strlen($WEBTD->align)) $_ret.= "text-align:".$WEBTD->align.";";
				if (strlen($WEBTD->valign)) $_ret.= "vertical-align:".$WEBTD->valign.";";
				if (strlen($tdstyle)) $_ret.= $tdstyle;
					
				
				$_ret.= "\"";	
			}
			return $_ret;	
		break;

	
		case "WEBTD_CLASS": 	
			$ret = " class=\"";
			switch ($WEBTD->type) {
				case 1:  $ret.= "std2"; break;
				case 3:  $ret.= "flash"; break;
				case 5:  $ret.= "news"; break;
				case 6:  $ret.= "press"; break;
				case 7:  $ret.= "spec"; break;
				default: $ret.= "std1";
			}	
			$ret.= "\"";
			if ($WEBTD->class) $ret=" class=\"".$WEBTD->class."\"";
			return $ret;
		break; 
		
		case "WEBLINK_GALERY":
			$_gal = " ";
			if ($WEBLINK->img) {
				$_src = $KAMELEON_UIMAGES."/".$WEBLINK->img;
				if (file_exists($_src)) {
					$_size = getimagesize($_src);
				}
				$_class="";
				if ($WEBLINK->class) $_class = " class=\"".$WEBLINK->class."\"";

				$_popup="";
				if ($WEBLINK->imga) {
					$_src = $KAMELEON_UIMAGES."/".$WEBLINK->imga;
					if (file_exists($_src)) {
						$_size_pp = getimagesize($_src);
					}

					$_popup=" onclick=popup_img('$UIMAGES/$WEBLINK->imga',$_size_pp[0],$_size_pp[1]) style=\"cursor: hand;\"";
				}
				$_abegin="";$_aend="";
				if ($WEBLINK->page_target || $WEBLINK->href) {
					
					if ($WEBLINK->page_target)
						$link_galery = kameleon_href('','',$WEBLINK->page_target);
					else	
						$link_galery = $WEBLINK->href;
					
					$link_target="";
					if ($WEBLINK->target) $link_target = " target=\"".$WEBLINK->target."\"";	
					$_abegin="<a href=\"".$link_galery."\" $link_target>";	
					$_aend = "</a>";
					$_popup = "";
				}
				$_gal = $_abegin."<img src=\"$UIMAGES/$WEBLINK->img\" alt=\"".$WEBLINK->alt."\" ".$_size[3].$_class.$_popup."/>".$_aend;
			}
			

			return $_gal;
		break;		
		
		case "WEBLINK_GALERY_ENDROW":	
			global $_link_count_row, $_menu_id;
			$_ret = " ";
			$_link_maxcol = 4;
			if ($WEBTD->size) $_link_maxcol = $WEBTD->size;
			if ($_menu_id != $WEBLINK->menu_id) {
				$_menu_id = $WEBLINK->menu_id;
				$_link_count_row = 0;
			}
			
			$_link_count_row++;
			if ($_link_count_row==$_link_maxcol) {
				$_link_count_row = 0;
				$_ret = "</tr><tr>";
			}
			return $_ret;
		break;
		
		case "WEBLINK_MENU_SUB":
			global $menu_id,$menu,$_linkcount;
			
			if (!in_array($WEBLINK->page_target,$pages_list)) return " ";
			$submenu = $WEBLINK->submenu_id;
			
			if ($submenu) {

				$old_menu_id=$menu_id;
				$old_menu=$menu;
				$old__linkcount=$_linkcount;

				$menu_id=$submenu;
				$menu_id+=0;
				$menu=kameleon_menus($menu_id);
				$_linkcount=count($menu);			
				
				if (!is_array($menu)) continue;
				//echo "<div id=\"lift_$menu_id\" style=\"visibility: hidden; display: none; z-index: 10;\" class=\"lift_div\">";
				if ($_linkcount)
				{
					$pom=$WEBLINK;
					$WEBLINK=$menu[0];
					$template=$LINK_TYPY[($WEBLINK->type+0)][2];
					$link_template="$SZABLON_PATH/$template";
     				if (!file_exists($link_template)) 
						$link_template="$SZABLON_PATH/themes/$template";
					$link_start="%SECTION_LINK_BEGIN%";
					$link_end="%SECTION_LINK_END%";
					$token['td_menu']=$menu_id;
					parser($link_start,$link_end,$link_template,$token);
					$WEBLINK=$pom;
				}
				$menu_id=$old_menu_id;
				$menu=$old_menu;
				$_linkcount=$old__linkcount;
				//echo "</div>";
			}
			return " ";			
		break; 
		case "WEBLINK_TITLE":
			$_ret = " ";
			if ($WEBLINK->alt_title) $_ret = $WEBLINK->alt_title;

			if ($WEBLINK->type==10)
			{
				$_spacepos = strpos($_ret,' ');
				$_ret = "<h4><strong>".substr($_ret,0,$_spacepos)."</strong>".substr($_ret,$_spacepos)."</h4>";
			}	

			return $_ret;	
		break;
		case "WEBTD_MORE":
			$ctx_morename = "więcej&nbsp;&raquo;";
			if ($WEBTD->costxt)  parse_str($WEBTD->costxt);
			if ($nomore) return " ";
			
			if (strlen($ctx_morehref) && ($WEBTD->more==1)) {
				$ret = "<a";
				$ret.= " href=\"".$ctx_morehref."\"";
				if (strlen($ctx_moretarget))$ret.= " target=\"".$ctx_moretarget."\"";
				$ret.= ">";
				$ret.= $ctx_morename;
				$ret.= "</a>";
				return $ret;
			}
		break;
		
		case "WEBLINK_SELECT_TEXT":
			$ret = "wybierz";
			parse_str($WEBTD->costxt);
			if ($select_text) $ret = $select_text;
			return $ret;	
		break;
				
		default:
		return "";
	}
}
?>
