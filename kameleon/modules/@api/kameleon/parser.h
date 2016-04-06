<?
if ($PARSER_H_INCLUDED) return;
else
	$PARSER_H_INCLUDED=1;
//*********** parametry
//
//	$parser_start="%SECTION_PARSER_BEGIN%";
//	$parser_end="%SECTION_PARSER_END%";
//	$parser_template="$SZABLON_PATH/themes/$template" lub "$SZABLON_PATH/$template";
//	$parser_tokens= tablica tokenów
//*********************

// zwraca string " width="w" height="h" "
// jesli brak fliku zwraca pusty string
function imageSize($img_src)
{
	global $KAMELEON_UIMAGES,$UIMAGES,$KAMELEON_MODE;

	$webmenu_tree_src = "$UIMAGES/$img_src";
	if (!$KAMELEON_MODE)
		$webmenu_size_src = "$KAMELEON_UIMAGES/$img_src";
	else
		$webmenu_size_src = $webmenu_tree_src;

	if (file_exists("$webmenu_size_src"))
		$webmenu_size = @getimagesize("$webmenu_size_src");
	else
		$webmenu_size[3]="";
	return $webmenu_size[3];
}


if ($CONST_PARSER_TOKENS && file_exists("$SZABLON_PATH/$CONST_TOKENS"))
{
	include_once("$SZABLON_PATH/$CONST_TOKENS");
}

function parser($parser_start,$parser_end,$parser_template,$parser_tokens,$parser_starter=0)
{
	global $page,$ver,$lang,$SZABLON_PATH,$IMAGES,$UIMAGES,$INCLUDE_PATH,$API_SERVER,$UFILES;

	global $PAGE_TYPY,$TD_POZIOMY,$TD_TYPY,$LINK_TYPY,$CONST_MORE_TOKENS;	
	
	global $SCRIPT_NAME,$WEBLINK,$WEBTD,$WEBPAGE,$KAMELEON_MODE;
	global $EREG_REPLACE_KAMELEON_UIMAGES,$KAMELEON_UIMAGES,$KAMELEON_UFILES,$this_editmode;
	global $page_parts_index,$page_id,$TD_POZIOMY_HF,$KAMELEON_EXT,$SERVER;
	global $menu_id,$menu, $_linkcount,$menu_separator;
	global $AUTH_EMAIL,$AUTH_NAME;
	
	global $api_email,$api_osoba,$api_action,$api_post;
	global $HTTP_GET_VARS,$HTTP_POST_VARS;
	global $CONST_REMOTE_INCLUDES_ARE_HERE;

	global $CONST_TOKENS;

	static $section_separator;



	if (strlen($parser_template) && file_exists($parser_template) )
	{
		$parser_content=read_file($parser_template);

		$parser_pos_counter=$parser_starter;
		$parser_content=substr($parser_content,$parser_starter);

		$parser_b=strpos($parser_content,$parser_start);

		if ($parser_b===null) 
		{
			if ($this_editmode) echo label("No start section");
			return;
		}
		$parser_b+=strlen($parser_start);
		$parser_e=strpos($parser_content,$parser_end);
		if (!$parser_e) 
		{
			if ($this_editmode) echo label("No end section");
			return;
		}
		$parser_content=substr($parser_content,$parser_b,$parser_e-$parser_b);

		$parser_pos_counter+=$parser_b;

		$parser_startpos=0;
		//tu wykrywamy wszystkie nasze znaczniki
		while (1) 
		{	$lp++;
			$parser_content=substr($parser_content,$parser_startpos);
			$parser_pos_counter+=$parser_startpos;

			$parser_proc1=strpos($parser_content,"%");
			$parser_proc2=strpos(substr($parser_content,$parser_proc1+1),"%");
			if (!strlen($parser_proc1) || !strlen($parser_proc2) )
			{
				echo $parser_content;
				break;
			}
			$parser_token=substr($parser_content,$parser_proc1+1,$parser_proc2);
			$parser_startpos=$parser_proc1+$parser_proc2+2;
			echo substr($parser_content,0,$parser_proc1);

			$result_token="";

			if (function_exists("tokens") )
			{
				$result_token=tokens($parser_token);
			}
			if (is_array($CONST_MORE_TOKENS) && !strlen($result_token) )
			{
				reset($CONST_MORE_TOKENS);
				while(list($fun,$file)=each($CONST_MORE_TOKENS))
				{
					if (file_exists("$SZABLON_PATH/$file")) include_once("$SZABLON_PATH/$file");
					if (function_exists($fun)) $result_token=$fun($parser_token);
					if (strlen($result_token)) break; 
				}
			}

			if ( !strlen($result_token) )$result_token=$parser_tokens[$parser_token];

			if (strlen($result_token) || isset($parser_tokens[$parser_token]) ) 
				echo $result_token;
			else
			switch ($parser_token)
			{
				case "WEBPAGE_CHARSET" :
					echo $parser_tokens['CHARSET'];break;
				case "WEBPAGE_TITLE" :
					echo $parser_tokens['title'];break;
				case "WEBPAGE_META_LANG" :
					echo $parser_tokens['meta_lang'];break;
				case "WEBPAGE_GENERATOR" :
					echo "WebKameleon ".$parser_tokens['KAMELEON_VERSION'];break;
				case "WEBPAGE_GENERATOR_VER" :
					echo $parser_tokens['KAMELEON_VERSION'];break;	
				case "WEBPAGE_DOCBASE" :
					echo $parser_tokens['docbase'];break;
				case "WEBPAGE_META_DESCRIPTION" :
					echo $parser_tokens['metadesc'];break;
				case "WEBPAGE_META_KEYWORDS" :
					echo $parser_tokens['metakey'];break;
				case "WEBPAGE_TEXTSTYLE_CSS" :
					echo $parser_tokens['TEXTSTYLE_CSS'];break;
				case "WEBPAGE_KAMELEON_CSS" :
					echo $parser_tokens['KAMELEON_CSS'];break;
				case "WEBPAGE_BACKGROUND" :
					echo $parser_tokens['background'];break;
				case "WEBPAGE_BODYCLASS" :
					echo $parser_tokens['bodyclass'];break;
				case "WEBPAGE_BGCOLOR" :
					echo $parser_tokens['bgcolor'];break;
				case "WEBPAGE_TEXTCOLOR" :
					echo $parser_tokens['textcolor'];break;

//sekcja stalych ustawionych w pagebegin.h
				case "WEBPAGE_MENULEFTWIDTH" :
					echo " width=\"".$parser_tokens['C_PAGE_MENULEFTWIDTH']."\"";break;
				case "WEBPAGE_MENURIGHTWIDTH" :
					echo " width=\"".$parser_tokens['C_PAGE_MENURIGHTWIDTH']."\"";break;
				case "WEBPAGE_WIDTH" :
					echo " width=\"".$parser_tokens['C_PAGE_WIDTH']."\"";break;
				case "WEBPAGE_ALIGN" :
					echo " align=\"".$parser_tokens['C_PAGE_ALIGN']."\"";break;
				case "WEBBODY_IMG_PATH" :
					echo $parser_tokens['IMAGES'];break;
				case "WEBBODY_UIMG_PATH" :
					echo $parser_tokens['UIMAGES'];break;
// header i footer
				case "WEBPAGE_HEADER" :
				case "WEBPAGE_FOOTER" :
					$_ver=$version;
					$display_menu="";
					
					$webtd=kameleon_td($page_id,$ver,$lang,0);	
					if (!is_array($webtd)) break;
					if (strlen($parser_tokens['C_PAGE_WIDTH'])) $p_width=" width=\"".$parser_tokens['C_PAGE_WIDTH']."\"";
					$p_width="";
					echo "<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\"$p_width>\n<tr>";
					$level=0;
					for ($h_i=0;$h_i<count($webtd);$h_i++)
					{
						$WEBTD=$webtd[$h_i];
				
						if ($level!=$WEBTD->level)
						{
				            if ($level) echo "</tr>\n<tr>\n";
						}
				        
						$level=$WEBTD->level;
						$tdcount=$h_i;
						$align="";$valign="";$class="";$bgcolor="";$colrowspan="";
						$width=" width=\"100%\"";
						if (strlen($WEBTD->align)) $align=" align=\"".$WEBTD->align."\"";
						if (strlen($WEBTD->valign)) $valign=" valign=\"".$WEBTD->valign."\"";
						if (strlen($WEBTD->class)) $class=" class=\"".$WEBTD->class."\"";
						if (strlen($WEBTD->width)) $width=" width=\"".$WEBTD->width."\"";
				        if (strlen($WEBTD->bgcolor)) $bgcolor=" bgcolor=\"#".$WEBTD->bgcolor."\"";
				        if (strlen($WEBTD->costxt) && !strstr($WEBTD->costxt,'$')) $colrowspan=" $WEBTD->costxt";
				
						echo "<td$class$align$valign$width$bgcolor$colrowspan>";
						include ("include/td.h");
						echo "</td>\n";
					}
					echo "</tr>\n</table>\n";
					break;
//webtd
				case "WEBTD_TITLE":
					echo $parser_tokens['td_title'];break;
				case "WEBTD_TITLE_BEGIN":
					$parser_content=substr($parser_content,$parser_startpos);
					$parser_loop_start=$parser_startpos;
					$parser_loop_end=strpos($parser_content,"%WEBTD_TITLE_END%");
					$parser_loop_content=substr($parser_content,0,$parser_loop_end);
					$parser_startpos=$parser_loop_end+strlen("%WEBTD_TITLE_END%");
					if (strlen($parser_tokens['td_title']) || strlen($parser_tokens['td_img']))
						parser("%WEBTD_TITLE_BEGIN%","%WEBTD_TITLE_END%",$parser_template,$parser_tokens);					
					break;
				case "WEBTD_TITLE_IMG":
					echo $parser_tokens['td_img'];break;	
				case "WEBTD_PLAIN":
					echo $parser_tokens['td_plain'];break;
				case "WEBTD_MORE_HREF":
					echo $parser_tokens['td_more_href'];break;
				case "WEBTD_MORE":
					echo $parser_tokens['td_more'];break;
				case "WEBTD_MORE_BEGIN":
					$parser_content=substr($parser_content,$parser_startpos);
					$parser_loop_start=$parser_startpos;
					$parser_loop_end=strpos($parser_content,"%WEBTD_MORE_END%");
					$parser_loop_content=substr($parser_content,0,$parser_loop_end);
					$parser_startpos=$parser_loop_end+strlen("%WEBTD_MORE_END%");
					if (strlen($parser_tokens['td_more']))					
						parser("%WEBTD_MORE_BEGIN%","%WEBTD_MORE_END%",$parser_template,$parser_tokens);					
					break;
				case "WEBTD_PRI":
					echo $parser_tokens['td_pri'];break;
				case "WEBTD_WIDTH":
					echo $parser_tokens['td_width'];break;
				case "WEBTD_CLASS":
					echo $parser_tokens['td_class'];break;
				case "WEBTD_BGIMG":
					echo $parser_tokens['td_bgimg'];break;
				case "WEBTD_ALIGN":
					echo $parser_tokens['td_align'];break;
				case "WEBTD_VALIGN":
					echo $parser_tokens['td_valign'];break;
				case "WEBTD_BGCOLOR":
					echo $parser_tokens['td_bgcolor'];break;
				case "WEBTD_IMG_PATH":
					echo $IMAGES;
					break;
				case "WEBTD_UIMG_PATH":
					echo $UIMAGES;
					break;
				case "WEBTD_VALID_FROM":
					echo $parser_tokens['td_valid_from']; break;
				case "WEBTD_VALID_TO":
					echo $parser_tokens['td_valid_to']; break;

				case "WEBTD_API":
					if (strlen($parser_tokens['td_api']))
					{
						$api=$parser_tokens['td_api'];
						include("include/api.h");
					}
					break;
				case "WEBTD_INCLUDE":
	  				if ($parser_tokens['td_include']) 
	   				{
						$html=$parser_tokens['td_include'];
						include("include/remote_html.h");
	  				}
					break;
				case "WEBTD_MENU":
					if ($parser_tokens['td_menu'])
					{
						$menu_id=$parser_tokens['td_menu'];
						$menu_id+=0;
						$menu=kameleon_menus($menu_id);
						$_linkcount=count($menu);			
						if (!is_array($menu)) continue;
						if ($_linkcount)
						{
							$WEBLINK=$menu[0];
							$template=$LINK_TYPY[($WEBLINK->type+0)][2];
							$link_template="$SZABLON_PATH/$template";
       						if (!file_exists($link_template)) 
								$link_template="$SZABLON_PATH/themes/$template";
							$link_start="%SECTION_LINK_BEGIN%";
							$link_end="%SECTION_LINK_END%";
							parser($link_start,$link_end,$link_template,$parser_tokens);
						}
					}
					break;
				case "WEBLINK_CLASS":
					echo $parser_tokens['menu_class'];break;

				case "WEBLINK_SEPARATOR_BEGIN":
					$parser_content=substr($parser_content,$parser_startpos);
					$parser_loop_start=$parser_startpos;
					$parser_loop_end=strpos($parser_content,"%WEBLINK_SEPARATOR_END%");
					$parser_loop_content=substr($parser_content,0,$parser_loop_end);
					$menu_separator=$parser_loop_content;
					$parser_startpos=$parser_loop_end+strlen("%WEBLINK_SEPARATOR_END%");
					break;


				case "SECTION_SEPARATOR_BEGIN":
					$parser_content=substr($parser_content,$parser_startpos);
					$parser_loop_start=$parser_startpos;
					$parser_loop_end=strpos($parser_content,"%SECTION_SEPARATOR_END%");
					$parser_loop_content=substr($parser_content,0,$parser_loop_end);
					$section_separator=$parser_loop_content;
					$parser_startpos=$parser_loop_end+strlen("%SECTION_SEPARATOR_END%");
					break;


				case "LOOP_BEGIN" :
					$parser_pos_counter+=strpos($parser_content,"%LOOP_BEGIN%");
					$parser_content=substr($parser_content,$parser_startpos);
					$parser_loop_start=$parser_startpos;
					$parser_loop_end=strpos($parser_content,"%LOOP_END%");
					$parser_loop_content=substr($parser_content,0,$parser_loop_end);
					$parser_startpos=$parser_loop_end+strlen("%LOOP_END%");

					if (!function_exists($parser_tokens["function_loop_begin"]) ) break;
					if (!function_exists($parser_tokens["function_loop_item"]) ) break;
					if (!function_exists($parser_tokens["function_loop_end"]) ) break;

					$_cmd=$parser_tokens["function_loop_begin"]."(\$_parser_iteration);";
					eval($_cmd);
					
					$_parser_loop_item=$parser_tokens["function_loop_item"]."(\$_parser_iteration);";
					$_parser_loop_query_end=$parser_tokens["function_loop_end"]."(\$_parser_iteration);";

					while (1)
					{
						eval ("\$_koniec=$_parser_loop_query_end");
						if ($_koniec) break;
						eval ("\$new_tokens=$_parser_loop_item");

						while ( is_array($new_tokens) &&  list( $p_key, $p_val ) = each($new_tokens) )
							$parser_tokens[$p_key]=trim($p_val);
						parser("%LOOP_BEGIN%","%LOOP_END%",$parser_template,$parser_tokens,$parser_pos_counter);
					}
					break;


				case "WEBLINK_LOOP_BEGIN" :
					$parser_content=substr($parser_content,$parser_startpos);
					$parser_loop_start=$parser_startpos;
					$parser_loop_end=strpos($parser_content,"%WEBLINK_LOOP_END%");
					$parser_loop_content=substr($parser_content,0,$parser_loop_end);
					$parser_startpos=$parser_loop_end+strlen("%WEBLINK_LOOP_END%");
					
					// tu zaczynamy pêtle
					//tu szukamy czy w danym menu jest link do podswietlenie
					
					if (strlen($WEBLINK->class)) 
						$menu_class = " class=\"$WEBLINK->class\"";
					else 
						$menu_class = "";
					
					$link_active=0;
					for ($_link=0;$_link<$_linkcount;$_link++)
					{
						$WEBLINK=$menu[$_link];
						if (strlen($WEBLINK->class))
							if ($WEBLINK->page_target==$WEBPAGE->id)
								$link_active = 1;
					}

					for ($_link=0;$_link<$_linkcount;$_link++)
					{
						$WEBLINK=$menu[$_link];

						$menu_pri=$WEBTD->pri.$WEBLINK->pri;
						$parser_tokens['menu_pri']=$menu_pri;

						$__clases=explode(' ',$WEBLINK->class);
						$primary_class=$__clases[0];
						
						//tu wykrywamy podswietlone menu				
						if (strlen($WEBLINK->class))
						{
							if ($WEBLINK->page_target==$WEBPAGE->id)
							{
								
								$menu_class = " class=\"".ereg_replace("(${primary_class})([^_]*)","\\1_active\\2",$WEBLINK->class)."\"";
							}
							else
							{
								$menu_class = " class=\"$WEBLINK->class\"";
							}
						}

						//tu podswietlamy wybrane menu wg tree z pominieciem menu juz podswietlonego
						$webpage_tree=explode(":",$WEBPAGE->tree);
						if (is_array($webpage_tree) && !$link_active)
						{
							//tu byla zmiana
							for ($i_tree=(count($webpage_tree));$i_tree>1;$i_tree--)
							{
								if (($WEBLINK->page_target==$webpage_tree[$i_tree]) && (strlen($webpage_tree[$i_tree])))
								{
									$menu_class = " class=\"".ereg_replace("(${primary_class})([^_]*)","\\1_active\\2",$WEBLINK->class)."\"";
									break;
								}
								else
								{
									$menu_class = " class=\"$WEBLINK->class\"";
								}
							}
						}
						
						$parser_tokens['menu_class']=$menu_class;

						$menu_alt=$WEBLINK->alt;
						$parser_tokens['menu_alt']=$menu_alt;						
						if (strlen($WEBLINK->target))
							$href_target="target=\"$WEBLINK->target\"";
						else
							$href_target="";
						$parser_tokens['href_target']=$href_target;

						
						if (strlen($WEBLINK->imga))
						{
							$wh=imageSize($WEBLINK->imga);
							$menu_mouseover=" onmouseout=\"this.src='$UIMAGES/$WEBLINK->img'\" onmouseover=\"this.src='$UIMAGES/$WEBLINK->imga'\"";
							$menu_imga="<img $wh border=\"0\" alt=\"$menu_alt\" src=\"$UIMAGES/$WEBLINK->imga\">";
							$menu_imga_src="$WEBLINK->imga";

						}
						else
						{
							$menu_mouseover="";
							$menu_imga="";
							$menu_imga_src="";
						}
					
						if (strlen($WEBLINK->img))
						{
							$wh=imageSize($WEBLINK->img);
							$menu_img="<img $wh border=\"0\" alt=\"$menu_alt\" src=\"$UIMAGES/$WEBLINK->img\"$menu_mouseover>";
							$menu_img_src="$WEBLINK->img";
						}
						else
						{
							$menu_img="";
							$menu_img_src="";
						}
						$parser_tokens['menu_img']="$menu_img";
						$parser_tokens['menu_img_src']=$menu_img_src;
						$parser_tokens['menu_imga']="$menu_imga";
						$parser_tokens['menu_imga_src']=$menu_imga_src;
					
						$menu_color	= ($WEBLINK->fgcolor)?" color: $WEBLINK->fgcolor;":"";	
						$parser_tokens['menu_color']=$menu_color;

						if (strlen($menu_color))
							$menu_style = " style=\"$menu_color\"";
						else
							$menu_style = "";
						$parser_tokens['menu_style']=$menu_style;
						$menu_href=kameleon_href($WEBLINK->href, $WEBLINK->variables, $WEBLINK->page_target);

						if (strlen($WEBLINK->ufile_target)) $menu_href=$UFILES.'/'.$WEBLINK->ufile_target;

						if (!strlen($WEBLINK->page_target) && !strlen($WEBLINK->href) && !strlen($WEBLINK->ufile_target) ) $menu_href="";
						$parser_tokens['menu_href']=$menu_href;

						// to jest do selecta
						$tit=strlen($WEBLINK->alt_title)?' title="'.addslashes($WEBLINK->alt_title).'"':'';
						$menu_selected	= ($WEBLINK->page_target==$WEBPAGE->id)?" selected":"";
						$parser_tokens['menu_selected']=$menu_selected;
						if (strlen($menu_href))
						{
							$menu_a		= "<a href=\"$menu_href\"$menu_class$menu_style$menu_target$href_target$tit>";
							$menu_noa	= "</a>";
						}
						else
						{
							$menu_a		= "";
							$menu_noa	= "";
						}
						$menu_inside= strlen($menu_img)?$menu_img:$menu_alt;
						$parser_tokens['menu_a']=$menu_a;
						$parser_tokens['menu_noa']=$menu_noa;
						$parser_tokens['menu_inside']=$menu_inside;
						// Tu jest taki magic ale potrzebny
						if ($_link) echo $menu_separator;
						$parser_tokens['menu_link_id']=$_link;
						//zmienne sa juz wystawione wiec mozna odpalic parsera jeszcze raz
						parser("%WEBLINK_LOOP_BEGIN%","%WEBLINK_LOOP_END%",$parser_template,$parser_tokens);
					}//for
					$menu_separator="";
					break;

				case "WEBLINK_MENU":
					echo $parser_tokens['menu_a'];
					echo $parser_tokens['menu_inside'];
					echo $parser_tokens['menu_noa'];
					break;
				case "WEBLINK_PRI":
					echo $parser_tokens['menu_pri'];break;
				case "WEBLINK_CLASS":
					echo $parser_tokens['menu_class'];break;
				case "WEBLINK_IMG":
					echo $parser_tokens['menu_img'];break;
				case "WEBLINK_IMG_SRC":
					echo $parser_tokens['menu_img_src'];break;
				case "WEBLINK_IMGA":
					echo $parser_tokens['menu_imga'];break;
				case "WEBLINK_IMGA_SRC":
					echo $parser_tokens['menu_imga_src'];break;
				case "WEBLINK_ALT":
					echo $parser_tokens['menu_alt'];break;
				case "WEBLINK_COLOR":
					echo $parser_tokens['menu_style'];break;
				case "WEBLINK_HREF":
					echo $parser_tokens['menu_href'];break;
				case "WEBLINK_INSIDE":
					echo $parser_tokens['menu_inside'];break;
				case "WEBLINK_MENU_SELECTED":
					echo $parser_tokens['menu_selected'];break;
				case "WEBLINK_ID":
					echo $parser_tokens['menu_link_id'];break;
				case "WEBLINK_IMG_PATH":
					echo $IMAGES;break;
				case "WEBLINK_UIMG_PATH":
					echo $UIMAGES;break;
				case "WEBLINK_POSITION_SELECTED":
					echo $WEBPAGE->tree;break;

				case "WEBTD_SID":
					echo $WEBTD->sid; break;
				case "WEBPAGE_SID":
					echo $WEBPAGE->sid; break;
				case "WEBLINK_SID":
					echo $WEBLINK->sid; break;

				default :
					if (substr($parser_token,0,7)=="GLOBAL(" )
					{
						$_wynik=kameleon_global($parser_token);
						if (substr($_wynik,0,7)!="GLOBAL(" ) echo $_wynik;	
					}
					elseif (ereg("WEBBODY_LEVEL([0-9]+)",$parser_token,$levels))
					{
						$parser_alias=0;
						if (ereg("WEBBODY_LEVEL([0-9]+)_INHERITED_(UP|DOWN|UP_NOTYPE|DOWN_NOTYPE)",$parser_token,$levels))
						{
							$level=$levels[1];
							$updown=$levels[2];
							
							$tree="";
							$inheritance=0; //czy ma rozrozniac typ stron przy dziedziczeniu td z ojca na syna czy nie

							switch ($updown)
							{
								case "UP":
									$tree=$WEBPAGE->tree;
									$inheritance=1;
									break;
								case "DOWN":
									$tmp_tree=explode(":",$WEBPAGE->tree);
									for ($tmp_i=count($tmp_tree);$tmp_i>0;$tmp_i--)
										if (strlen($tmp_tree[$tmp_i]))
											$tree.=$tmp_tree[$tmp_i].":";
									$tree=":".$tree;
									$inheritance=1;
									break;
								case "UP_NOTYPE":
									$tree=$WEBPAGE->tree;
									$inheritance=0;
									break;
								case "DOWN_NOTYPE":
									$tmp_tree=explode(":",$WEBPAGE->tree);
									for ($tmp_i=count($tmp_tree);$tmp_i>0;$tmp_i--)
										if (strlen($tmp_tree[$tmp_i]))
											$tree.=$tmp_tree[$tmp_i].":";
									$tree=":".$tree;
									$inheritance=0;
									break;
								default :
									$tree=$WEBPAGE->tree;
									$inheritance=1;
							}
						
							$webpage_tree=explode(":",$tree);
//							echo "tree=$tree<br>";
							for ($i_tree=1;$i_tree<(count($webpage_tree)-1) && is_array($webpage_tree);$i_tree++)
							{
								$parent_page=$webpage_tree[$i_tree];
//								echo "parent=$parent_page, inherit=$inheritance<br>";								
								if ($inheritance)
								{
									unset($PARENT_WEBPAGE);
									$PARENT_WEBPAGE=kameleon_page($parent_page);
									$cr = kameleon_td_count($parent_page,$ver,$lang,$level);
									if ($cr && ($WEBPAGE->type==$PARENT_WEBPAGE[0]->type))
									{
										$webtd=kameleon_td($parent_page,$ver,$lang,$level);
										if (!is_array($webtd) ) return;
										for ($td_w_szpalcie=0;$td_w_szpalcie<count($webtd);$td_w_szpalcie++)
										{
											$tdcount=$td_w_szpalcie;
											$WEBTD=$webtd[$td_w_szpalcie];
											include ("include/td.h");
										}
									}

								}
								else
								{
									$cr = kameleon_td_count($parent_page,$ver,$lang,$level);
									if ($cr)
									{
										$webtd=kameleon_td($parent_page,$ver,$lang,$level);
										if (!is_array($webtd) ) return;
										for ($td_w_szpalcie=0;$td_w_szpalcie<count($webtd);$td_w_szpalcie++)
										{
											$tdcount=$td_w_szpalcie;
											$WEBTD=$webtd[$td_w_szpalcie];
											include ("include/td.h");
										}
									}
								}
							}
						}
						else
						{
							if (ereg("WEBBODY_LEVEL([0-9]+)_ALIAS([0-9]+)",$parser_token,$levels))
							{
								$level=$levels[1];
								$copy_page=$levels[2];
								$parser_alias=1;
								$cr=kameleon_td_count($page,$ver,$lang,$level);
								$cr+=kameleon_td_count($copy_page,$ver,$lang,$level);
								if ($cr)
								{
									if ($copy_page!=$page)
										include("include/szpalta.h");
								}        
							}
							else
							{
								$level=$levels[1];
								$cr=kameleon_td_count($page,$ver,$lang,$level);
								if ($cr)
								{
									include("include/szpalta.h");
								}        
							}
						}
					}
					elseif (ereg("WEB(HEADER|FOOTER)_LEVEL([0-9]+)",$parser_token,$levels))
					{
						$level=$levels[2];
						$hf=$levels[1][0];

						$cr=kameleon_td_count($page_id,$ver,$lang,$level);
						if ($cr)
						{
							$webtd=kameleon_td($page_id,$ver,$lang,$level);
							if (is_array($webtd) )
							 for ($td_w_szpalcie=0;$td_w_szpalcie<count($webtd);$td_w_szpalcie++)
							 {
								$tdcount=$td_w_szpalcie;
								$WEBTD=$webtd[$td_w_szpalcie];
								if ($td_w_szpalcie) echo $section_separator;
								include ("include/td.h");
							 }

						}  						

					}
					else
					{
						echo "%";
						$parser_startpos=$parser_proc1+1;
					}
				
			}//switch
		} //while
	}
	elseif	($this_editmode) echo label("Missing")." $template";
}

?>