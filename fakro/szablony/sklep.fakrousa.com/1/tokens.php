<?
function tokens($t)
{
	global $WEBPAGE, $KAMELEON_MODE, $UIMAGES, $WEBTD, $WEBLINK, $IMAGES, $KAMELEON_UIMAGES;
	global $page, $ver, $lang,$editmode;
	global $LINK_TYPY,$tokens;
	global $SZABLON_PATH,$SERVER_ID;
//	print_r($WEBPAGE);
//	print_r($WEBTD);
//	print_r($WEBLINK);

	
	$pages_list = explode(":",$WEBPAGE->tree);
	$pages_list[] = $page;
	switch ($t)	{
		case "WEBPAGE_META_DESCRIPTION":
			$meta_content="";
			for ($i=count($pages_list); $i >= 0 ; $i--)
			{
				if (strlen($pages_list[$i]))
				{
					$PREV_WEBPAGE=kameleon_page($pages_list[$i]);
					if (strlen($PREV_WEBPAGE[0]->description))
						$meta_content.=". ".$PREV_WEBPAGE[0]->description;
				}	
			}
			$meta_content=substr($meta_content,2);
			$meta = "
	<meta http-equiv=\"description\" content=\"".$meta_content."\">
	<meta name=\"description\" content=\"".$meta_content."\">
			";
			return $meta;
		break;
		case "WEBPAGE_META_KEYWORDS":
			$meta_content="";
			for ($i=count($pages_list); $i >= 0 ; $i--)
			{
				if (strlen($pages_list[$i]))
				{
					$PREV_WEBPAGE=kameleon_page($pages_list[$i]);
					if (strlen($PREV_WEBPAGE[0]->keywords))
						$meta_content.=", ".$PREV_WEBPAGE[0]->keywords;
				}	
			}
			$meta_content=substr($meta_content,2);
			$meta = "
	<meta http-equiv=\"keywords\" content=\"".$meta_content."\">
	<meta name=\"keywords\" content=\"".$meta_content."\">
			";
			return $meta;
		break;

	
		case "WEBPAGE_BODYCLASS":
//			if (!strlen($WEBPAGE->class)) 
			{
				global $SERVER_NAME;
				$si=1;
				if (strstr($SERVER_NAME,'fakro.pl')) $si=1;
				if (strstr($SERVER_NAME,'fakro.com')) $si=3;
				if (strstr($SERVER_NAME,'sklep')) $si=4;

				$ret = " class=\"";
				$ret.= "si".$si;
				$ret.= " pt";
				$ret.= $WEBPAGE->type+0;
				if (strlen($WEBPAGE->class)) $ret.= " ".$WEBPAGE->class;
				$ret.= "\"";
				return $ret;
			}
		break;

		case "PAGE_BODY_TABLE":
			global $C_PAGE_ALIGN,$C_PAGE_WIDTH;
			if ($KAMELEON_MODE) 
				$page_valign_begin = "<table border=0 cellpadding=0 cellspacing=0 id=\"main\">";
			else 
				$page_valign_begin = "
				<script language=\"JavaScript\" type=\"text/javascript\">
				<!--
				document.write('<table height='+(document.body.clientHeight - 243 - 104)+' border=0 cellpadding=0 cellspacing=0 id=\"main\">');
				// -->
				</script>";
			return $page_valign_begin; 
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
		
	
		case "WEBKAMLEON_FOOTER": 
			return 	"
				<div class=\"st\">
				<A href=\"http://www.gammanet.pl/\" target=_blank>created by gammanet</A> | <A href=\"javascript:sc('$IMAGES/gammanet.gif')\">site credits</A></div>"; 
			return 	"
				<a href=\"http://webkameleon.com\" target=\"_blank\">
				<img src=\"$IMAGES/web_stopka_1.gif\" width=115 height=13 hspace=0 vspace=0 border=0 alt=\"CMS: webkamleon\"></a>"; 
		break;
		
		
		case "WEBTD_STYLE":
			$_ret = " ";
			if (strlen($WEBTD->costxt)) parse_str($WEBTD->costxt);
			
			if (strlen($WEBTD->bgcolor) || strlen($WEBTD->bgimg) || strlen($WEBTD->width) || strlen($WEBTD->align) || strlen($WEBTD->valign) || strlen($WEBTD->costxt) || strlen($tdstyle)) {
				$_ret = " style=\"";
				
				if (strlen($WEBTD->bgimg)) $_ret.= "background-image: url(".$UIMAGES."/".$WEBTD->bgimg.");";
				if (strlen($WEBTD->bgcolor)) $_ret.= "background-color:#".$WEBTD->bgcolor.";";
				if (strlen($WEBTD->width) && !$WEBTD->swfstyle) $_ret.= "width:".$WEBTD->width."px;";
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

					$_popup=" onclick=kameleon_popup_img('$UIMAGES/$WEBLINK->imga',$_size_pp[0],$_size_pp[1]) style=\"cursor: hand;\"";
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
				$_gal = $_abegin."<img src=\"$UIMAGES/$WEBLINK->img\" border=0 hspace=0 vspace=0 alt=\"".strip_tags($WEBLINK->alt)."\" ".$_size[3].$_class.$_popup.">".$_aend."<br/>";
				if (strlen($WEBLINK->alt))	$_gal.= $_abegin.$WEBLINK->alt.$_aend;
			}
			

			return $_gal;
		break;
		
		case "WEBLINK_GALERY2":
			$text_alt_1 = label("Choose size");
			$text_alt_2 = label("Show zoom");
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


				$txt_js = "<a href=\'".$link_galery."\' id=\'inlink2\'   style=\'color:#666285\;text-decoration:none;font-weight:bold;\' onClick=\'window.close();\'>".$text_alt_1."</a>";
				//echo $txt_js;
				
				if ($WEBLINK->imga) {
					$_src = $KAMELEON_UIMAGES."/".$WEBLINK->imga;
					if (file_exists($_src)) {
						$_size_pp = getimagesize($_src);
					}

					$_popup=" onClick=\"return galeriaOnclick(this,'$UIMAGES/$WEBLINK->imga','$txt_js','$link_galery');\"";
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

/**/
			parse_str($WEBLINK->variables);	
			if (strlen($probka))
			{
				$text_alt_3 = label("zamów próbkê");
				$src=$WEBLINK->img;
				$kod=$WEBLINK->alt." ".$WEBLINK->alt_title."|".$UIMAGES."/".$src;
				$ret = "<p align=\"right\"><input class=\"sampleOrderButton\" type=\"button\" onclick=\"KOSZJS.putItem('$kod')\" value=\"$text_alt_3\"></input></p>";	
				$_gal.= "<img src=\"".$IMAGES."/i_specimen2.gif\" alt=\"$text_alt_3\" align=\"absmiddle\" class=\"specimen\" onclick=\"KOSZJS.putItem('$kod')\" style=\"cursor: hand;\">";	
			}

			$_gal .= "<a href=\"".$link_galery."\" $link_target title=\"".$text_alt_1.' '.$WEBLINK->alt."\">".$WEBLINK->alt."</a>";

			return $_gal;
		break;		
		
		case "WEBLINK_GALERY_ENDROW":	
			global $_link_count_row, $_menu_id;
			$_ret = " ";
			$_link_maxcol = 4;
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
				
				if (!is_array($menu)) return " ";
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
			$ctx_morename = label("more");
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

		case "KROKI":
			if ($WEBTD->type != 7 ) return ' ';
			if (!$WEBTD->size) return ' ';
			if (!$WEBTD->cos) return ' ';

			$ret='<span class="kroki">'.label("steep").': ';
			for ($i=1;$i<=$WEBTD->size;$i++)
			{
				$a=($i==$WEBTD->cos) ? '_active':'';
				$ret.="<span class=\"krok$a\">$i</span>";
			}
			$ret.='</span>';
			return $ret;
			
		case "PRODUKT_FILM":
			 $ret = " ";
			 if (strlen($WEBTD->bgimg))
			 {
				$ret = "<br>";
				$src = $UIMAGES."/".$WEBTD->bgimg;
			
				//$ret.= "<img src=\"".$UIMAGES."/sb/i_mov.gif\" class=\"mov\" onclick=\"popup_mov('".$src."')\">";
				$ret.= "<h6 class=\"mov\"><a href=\"javascript:popup_mov('".$src."')\">".label("see movie")."</a></h6>";
			 }
				/*
				$_src = $UIMAGES."/".$WEBTD->bgimg;
				$ret = "<EMBED src=\"".$_src."\" width=144 height=250 autoplay=no></EMBED>";

				$ret = "<object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" width=\"480\" height=\"274\" 
				          codebase=\"http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0\">
				          <param name=\"autoplay\" value=\"true\" />
				          <param name=\"controller\" value=\"true\" />
				          <param name=\"cache\" value=\"false\" />
				          <param name=\"pluginspage\" value=\"http://www.apple.com/quicktime/download/indext.html\" />
				          <param name=\"target\" value=\"myself\" />
				          <param name=\"type\" value=\"video/quicktime\" />
				          <param name=\"src\" value=\"".$_src."\" />
				          <embed 
				           width=\"144\" 
				           height=\"224\" 
				           src=\"".$_src."\" 
				           autoplay=\"true\" 
				           controller=\"true\" 
				           border=\"0\" 
				           cache=\"false\" 
				           pluginspage=\"http://www.apple.com/quicktime/download/indext.html\" 
				           target=\"myself\" />
				        </object>";
				*/
			return $ret;
		break;

		case "SID_GOOGLE_ANALYTICS":
			if($KAMELEON_MODE == 0) $re = '<?php @include($INCLUDE_PATH."/google_analytics.php"); ?>';
			$re .= " ";
			return $re;
		break;

		default:
			return "";
	}
}
?>
