<?
function tokens($t)
{
	global $WEBPAGE, $KAMELEON_MODE, $UIMAGES, $WEBTD, $WEBLINK, $IMAGES, $KAMELEON_UIMAGES;
	global $page, $ver, $lang,$editmode;
	global $LINK_TYPY,$tokens;
	global $SZABLON_PATH,$SERVER_ID;
//	print_r($WEBPAGE);
//	print_r($WEBTD);

	
	$pages_list = explode(":",$WEBPAGE->tree);
	$pages_list[] = $page;
	switch ($t)	{
	
		case "WEBPAGE_BODYCLASS":
			if (!strlen($WEBPAGE->class)) 
			{
				$ret = " class=\"";
				$ret.= "si".$SERVER_ID;
				$ret.= " pt";
				$ret.= $WEBPAGE->type+0;
				$ret.= "\"";
				return $ret;
			}
		break;

		case "PAGE_BODY_TABLE": 
			global $C_PAGE_ALIGN,$C_PAGE_WIDTH;
			if ($KAMELEON_MODE) 
				$page_valign_begin = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" class=\"bt\" width=\"$C_PAGE_WIDTH\" align=\"$C_PAGE_ALIGN\">";
			else 
				$page_valign_begin = "
				<script language=\"JavaScript\" type=\"text/javascript\">
				<!--
				document.write('<table height='+(document.body.clientHeight - 140 - 78)+' class=bt border=0 cellpadding=0 cellspacing=0 width=$C_PAGE_WIDTH>');
				// -->
				</script>";
			return $page_valign_begin; 
		break; 
		/*
		case "WEBPAGE_TEXTSTYLE_CSS": 
			global $CONST_MORE_CSS;
			$_ret = "<link href=\"".$IMAGES."/textstyle.css\" rel=\"stylesheet\" type=\"text/css\">"; 
			$_ret .= "\n\t\t<link href=\"".$IMAGES."/szablon.css\" rel=\"stylesheet\" type=\"text/css\">"; 

			
			while( is_array($CONST_MORE_CSS) && list($type,$file)=each($CONST_MORE_CSS))
				if ($type==$WEBPAGE->type) $_ret .= "\n\t\t<link href=\"".$IMAGES."/$file\" rel=\"stylesheet\" type=\"text/css\">"; 

			return $_ret; 
		break;
		*/
		case "KAMELEON_META_NEXT": 
			$ret = " ";
			if (strlen($WEBPAGE->next)) {
				if ($KAMELEON_MODE) {
					$ret ="<script language=\"JavaScript\" type=\"text/javascript\">";
					
					if (!$editmode) 
						$ret.="location.href='".kameleon_href('','',$WEBPAGE->next)."';\n";
					
					$ret.="</script>\n";
				}	
				else
				{
				$ret.="<? header(\"Location: ".kameleon_href('','',$WEBPAGE->next)."\") ?>";
				}
			}
			return $ret; 
		break;
	
		case "KAMELEON_META_ADD": 
			$ret = " ";
			if ($page) {
				$ret.= "<meta name=\"WebKameleonId\" content=\"".$page.":".$ver.":".$lang."\">\n";
			}	
			return $ret; 
		break; 
		
		
		case "WEBPAGE_STYLE": 
			$ret = "style=\"";
			$ret.= "margin:0px;";
			if ($SERVER_ID==1 && !$WEBPAGE->type && !$editmode) 
			{
				$ret.= "background-image: url(".$IMAGES."/back_home.gif);";
				$ret.= "background-repeat: repeat-x;";
				$ret.= "background-position: left ";
				if ($KAMELEON_MODE)
					$ret.= 454;
				else
					$ret.= 413;	
				$ret.= "px;";
			}	
			$ret.= "\"";
			return $ret; 
		break;	
		
		case "WEBPAGE_JS":

			global $CONST_MORE_JS;

			$scr="";

			while( is_array($CONST_MORE_JS) && list($type,$file)=each($CONST_MORE_JS))
				if ($type==$WEBPAGE->type) 
					$scr .= "\n\t\t\t\t<script src=\"$IMAGES/$file\" type=\"text/javascript\"></script>"; 
			return "
				<script language=\"JavaScript\" type=\"text/javascript\">
				var JSTITLE = \"".addslashes(stripslashes($WEBPAGE->title))."\";
				</script>
				<script src=\"$IMAGES/js/scripts.js\" type=\"text/javascript\"></script>$scr";
		break; 
		
		case "WEBPAGE_DATE": 
			$_ret = "<META name=\"date\" content=\"";
			$_ret .= date("r");
			$_ret .= "\">";
			return $_ret; 
		break; 
		
		case "WEBKAMLEON_FOOTER": 
			return 	"
				<div class=\"st\">
				<A href=\"http://www.gammanet.pl/\" target=_blank>created by gammanet</A> | <A href=\"javascript:sc('$IMAGES/gammanet.gif')\">site credits</A></div>"; 
			return 	"
				<a href=\"http://webkameleon.com\" target=\"_blank\">
				<img src=\"$IMAGES/web_stopka_1.gif\" width=115 height=13 hspace=0 vspace=0 border=0 alt=\"CMS: webkamleon\"></a>"; 
		break;
		
		case "FLASH_MENU": 
			parse_str($WEBTD->costxt);
			return $flash_menuname;
		break; 
		
		case "FLASH_PLAIN": 
			parse_str($WEBTD->costxt);
			$flash_bgcolor="";
			$flash_src = $UIMAGES."/".$WEBTD->bgimg;
			$flash_src_kam = $KAMELEON_UIMAGES."/".$WEBTD->bgimg;
			
			if (strlen($flash_width)) $flash_width = "width=\"".$flash_width."\"";
			if (strlen($flash_height)) $flash_height = "height=\"".$flash_height."\"";
			if (file_exists($flash_src_kam)) {
				$flash_size_arr = getimagesize($flash_src_kam);
				if (is_array($flash_size_arr)) {
					if (!strlen($flash_width))	$flash_width = "width=\"".$flash_size_arr[0]."\"";
					if (!strlen($flash_height))	$flash_height = "height=\"".$flash_size_arr[1]."\"";
				}	
			}
			
			
			if (strlen($WEBTD->bgcolor)) {
				$flash_bgcolor = "<PARAM NAME=\"BGColor\" VALUE=\"$WEBTD->bgcolor\">";
				$embed_bgcolor = "bgcolor=\"".$WEBTD->bgcolor."\"";
			}
			if (strlen($flash_loop)) {
				if ($flash_loop)
					$flash_loop = "<PARAM NAME=\"LOOP\" VALUE=\"true\">";
				else 	
					$flash_loop = "<PARAM NAME=\"LOOP\" VALUE=\"false\">";
			}	
			
			
			
			if (strlen($flash_clicktag)) {
				$flash_src.="?clickTag=".$flash_clicktag;
				$flash_clicktag="<PARAM NAME=\"clickTag\" VALUE=\"".$flash_clicktag."\">";
				
			}	
			elseif (strlen($WEBTD->next)) {
				$flash_src.="?clickTag=".kameleon_href('','',$WEBTD->next);
				$flash_clicktag="<PARAM NAME=\"clickTag\" VALUE=\"".kameleon_href('','',$WEBTD->next)."\">";
			}	
			
			$flash_plain = $flash_next_f."
				<OBJECT codeBase=http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0 
					$flash_width $flash_height classid=clsid:D27CDB6E-AE6D-11cf-96B8-444553540000>
				<PARAM NAME=\"Movie\" VALUE=\"$flash_src\">
				<PARAM NAME=\"Src\" VALUE=\"$flash_src\">
				<PARAM NAME=\"wmode\" VALUE=\"transparent\">
				$flash_clicktag
				$flash_bgcolor
				$flash_loop
				<EMBED src=\"$flash_src\" quality=high $flash_width $flash_height $embed_bgcolor
					TYPE=\"application/x-shockwave-flash\" 
					PLUGINSPAGE=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\">
				</EMBED>
				</OBJECT>";
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
			
			if (strlen($WEBTD->bgcolor) || strlen($WEBTD->bgimg) || strlen($WEBTD->width) || strlen($WEBTD->align) || strlen($WEBTD->valign) || strlen($WEBTD->costxt) || strlen($tdstyle)) {
				$_ret = " style=\"";
				
				if (strlen($WEBTD->bgimg)) $_ret.= "background-image: url(".$UIMAGES."/".$WEBTD->bgimg.");";
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
				case 3:  $ret.= "prod"; break;
				case 6:  $ret.= "sklep"; break;
				default: $ret.= "std";
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
				$_gal = $_abegin."<img src=\"$UIMAGES/$WEBLINK->img\" border=0 hspace=0 vspace=0 alt=\"".$WEBLINK->alt."\" ".$_size[3].$_class.$_popup.">".$_aend;
			}
			

			return $_gal;
		break;
		
		case "WEBLINK_GALERY2":
			$text_alt_1 = label("Kliknij aby zobaczyæ wszystkie pozycje ");
			$text_alt_2 = label("Kliknij aby zobaczyæ powiêkszenie ");
			$_gal = " ";
			if ($WEBLINK->img) {
				$_src = $KAMELEON_UIMAGES."/".$WEBLINK->img;
				if (file_exists($_src)) {
					$_size = getimagesize($_src);
				}
				$_class="";
				if ($WEBLINK->class) $_class = " class=\"".$WEBLINK->class."\"";

				$_popup="";
				$_abegin="";$_aend="";
				$link_galery = kameleon_href($WEBLINK->href,$WEBLINK->variables,$WEBLINK->page_target);
				if ($WEBLINK->target) $link_target = " target=\"".$WEBLINK->target."\"";	
				$_abegin="<a href=\"".$link_galery."\" $link_target>";	
				$_aend = "</a>";


				$txt_js = "<a href=\'".$link_galery."\' id=\'inlink\' target=\'_tst\' onclick=window.close(); style=\'color:#666285\;text-decoration:none;font-weight:bold;\'>".$text_alt_1."</a>";
				//echo $txt_js;
				
				if ($WEBLINK->imga) {
					$_src = $KAMELEON_UIMAGES."/".$WEBLINK->imga;
					if (file_exists($_src)) {
						$_size_pp = getimagesize($_src);
					}

					$_popup="onClick=\"return galeriaOnclick(this,'$UIMAGES/$WEBLINK->imga','$txt_js');\"";
				}
				$_abegin="";$_aend="";
				if ($WEBLINK->page_target || $WEBLINK->href) {
					
					$link_galery = kameleon_href($WEBLINK->href,$WEBLINK->variables,$WEBLINK->page_target);

					
					$link_target="";
					if ($WEBLINK->target) $link_target = " target=\"".$WEBLINK->target."\"";	
					$_abegin="<a href=\"".$link_galery."\" $link_target>";	
					$_aend = "</a>";

				}
				
				$_gal = $_abegin."<img src=\"$UIMAGES/$WEBLINK->img\" border=0 hspace=0 vspace=0 alt=\"".$text_alt_2."\" title=\"".$text_alt_2."\" ".$_size[3].$_class.$_popup.">".$_aend;
			}
			$_gal .= "<a href=\"".$link_galery."\" $link_target title=\"".$text_alt_1.$WEBLINK->alt."\">".$WEBLINK->alt."</a>";

			return $_gal;
		break;		
		
		case "WEBLINK_GALERY_ENDROW":	
			global $_link_count_row, $_menu_id;
			$_ret = " ";
			
			if($WEBTD->costxt) $_link_maxcol = $WEBTD->costxt;
				else $_link_maxcol = 4;
			
			if ($WEBTD->size) $_link_maxcol = $WEBTD->size;
			if ($_menu_id != $WEBLINK->menu_id) {
				$_menu_id = $WEBLINK->menu_id;
				$_link_count_row[$WEBTD->sid] = 0;
			}
			
			$_link_count_row[$WEBTD->sid]++;
			if ($_link_count_row[$WEBTD->sid]==$_link_maxcol) {
				$_link_count_row[$WEBTD->sid] = 0;
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
				echo "<div class=\"lift\">";
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
				echo "</div>";
			}
			return " ";			
		break; 
		
		case "WEBTD_MORE":
			$ctx_morename = "wiêcej";
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


		case "WEBTD_TITLE_IMG":
			if ($WEBTD->type==3) return;
			return " ";
		
		case "WEBTD_TITLE":
			if ($WEBTD->type==3) return ;
			if (strlen($WEBTD->img)) return "<img src=\"$UIMAGES/".$WEBTD->img."\" alt=\"".$WEBTD->title."\" title=\"".$WEBTD->title."\">";
			return $WEBTD->title;



		default:
			global $CONST_MORE_TOKENS;
			if (is_array($CONST_MORE_TOKENS))
			{
				reset($CONST_MORE_TOKENS);
				while(list($fun,$file)=each($CONST_MORE_TOKENS))
				{
					$ret="";
					if (file_exists("$SZABLON_PATH/$file")) include_once("$SZABLON_PATH/$file");
					if (function_exists($fun)) $ret=$fun($t);
					if (strlen($ret)) return $ret;
				}
			}
		return "";
	}
}
?>
