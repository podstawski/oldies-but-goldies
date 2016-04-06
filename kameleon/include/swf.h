<?
include_once('include/utf8.h');


	function kameleon_td2xml(&$td)
	{
		global $lang,$SERVER_ID,$page,$ver,$UIMAGES,$KAMELEON_MODE;

		$wynik='<module>';

		$img=$td->img;
		if (strlen($img)) $img="$UIMAGES/$img";

		
	

		$wynik.='<title>'.htmlspecialchars($td->title).'</title>';
		$wynik.='<plain>'.htmlspecialchars($td->plain).'</plain>';
		$wynik.='<img>'.htmlspecialchars($img).'</img>';
		if ($KAMELEON_MODE) $wynik.='<kameleon_mode>1</kameleon_mode>';

		if (strlen($td->cos)) $wynik.='<num>'.htmlspecialchars($td->cos).'</num>';
		if (strlen($td->costxt)) $wynik.='<param>'.htmlspecialchars($td->costxt).'</param>';


		parse_str($td->costxt);
		if (!strlen($ctx_morename)) $ctx_morename=label('More'); 
		if (!strlen($ctx_nextname)) $ctx_nextname=label('Next'); 

		if (!strlen($ctx_morehref)) $ctx_morehref=kameleon_href('','',$td->more);
		if (!strlen($ctx_nexthref)) $ctx_nexthref=kameleon_href('','',$td->next);


		if ($td->more)
		{
			$wynik.='<more>';
			$wynik.='<alt>'.htmlspecialchars($ctx_morename).'</alt>';
			$wynik.='<url>'.htmlspecialchars($ctx_morehref).'</url>';
			$wynik.='</more>';
		}
		if ($td->next)
		{
			$wynik.='<next>';
			$wynik.='<alt>'.htmlspecialchars($ctx_nextname).'</alt>';
			$wynik.='<url>'.htmlspecialchars($ctx_nexthref).'</url>';
			$wynik.='</next>';
		}

		


		if (strlen($td->html))
		{
			global $WEBPAGE,$IMAGES,$UIMAGES,$editmode;

			$more=(strlen($td->more))?$td->more:$page;
			$next=(strlen($td->next))?$td->next:$page;

			$next=urlencode(kameleon_href("","",$next));
			$more=urlencode(kameleon_href("","",$more));

			$self=urlencode(kameleon_href("","",$page));
			$tit=urlencode($td->title);

			$_costxt=urlencode($td->costxt);

			$param="more=$more&cos=$td->cos&next=$next&size=$td->size&class=$td->class&costxt=$_costxt&title=$tit&pagetype=$WEBPAGE->type&self=$self&tree=$WEBPAGE->tree&sid=$td->sid";

			if ($WEBPAGE->next) 
			{
				$param.="&nextpage=".urlencode(kameleon_href("","",$WEBPAGE->next));
				
			}
			if ($WEBPAGE->prev) 
			{
				$param.="&prevpage=".urlencode(kameleon_href("","",$WEBPAGE->prev));
			}

			$param.="&page=$page&ver=$ver&lang=$lang&IMAGES=$IMAGES&UIMAGES=$UIMAGES";


			push($editmode);
			//$editmode=0;
			ob_start();
			kameleon_include($td->html,$param);
			$inc=trim(ob_get_contents());
			ob_end_clean ();
			//$editmode=pop();
		
			if (strlen($inc)) $wynik.='<inc>'.$inc.'</inc>';


		}

		if ($td->menu_id) $wynik.=kameleon_menu2xml($td->menu_id);

		$wynik.='</module>';

		

		$xml=lang2utf8($wynik);

		

		return $xml;
	}


	function kameleon_menulink2xml(&$MENUOBJ,$root='menu',$levelname='element') 
	{
		global $UIMAGES,$WEBPAGE,$KAMELEON_UIMAGES,$UFILES,$LINK_TYPY_DXML;
		$ret = "";
		if (strlen($MENUOBJ->alt)) $ret.= "<alt>".htmlspecialchars($MENUOBJ->alt)."</alt>";

		$variables=eregi_replace("(^|&)_[^=]+=[^&]*","\\1",$MENUOBJ->variables);
		

		if (strlen($MENUOBJ->page_target)) $url = kameleon_href('',$variables,$MENUOBJ->page_target);
		if ($MENUOBJ->href) $url = $MENUOBJ->href;	
		if ($MENUOBJ->ufile_target) $url = $UFILES.'/'.$MENUOBJ->ufile_target;

		$url=ereg_replace("&+","&",$url);
		if ($url[strlen($url)-1]=='&') $url=substr($url,0,strlen($url)-1);
		

		$_url = explode("&",$url);
		//$ret.= "<url>".$_url[0]."</url>";
		//linijke nizej bylo htmlspecialchars, ale encodowalo ampersanty
		if (strlen($url)) $ret.= "<url>".$url."</url>";
		if (strlen($MENUOBJ->target)) $ret.='<target>'.htmlspecialchars($MENUOBJ->target).'</target>';
				
		if (strlen($MENUOBJ->img))	
		{
			$imgattr='';
			if (file_exists($KAMELEON_UIMAGES."/".$MENUOBJ->img) && function_exists('getimagesize'))
			{

				$a=getimagesize ($KAMELEON_UIMAGES."/".$MENUOBJ->img);
				if ($a[0]) $imgattr.=' w="'.$a[0].'"';
				if ($a[1]) $imgattr.=' h="'.$a[1].'"';
			}
			$ret.= "<img$imgattr>".$UIMAGES."/".$MENUOBJ->img."</img>";
		}
		if (strlen($MENUOBJ->imga)) 
		{
			$imgattr='';
			if (file_exists($KAMELEON_UIMAGES."/".$MENUOBJ->imga) && function_exists('getimagesize'))
			{

				$a=getimagesize ($KAMELEON_UIMAGES."/".$MENUOBJ->imga);
				if ($a[0]) $imgattr.=' w="'.$a[0].'"';
				if ($a[1]) $imgattr.=' h="'.$a[1].'"';
			}


			$ret.= "<imga$imgattr>".$UIMAGES."/".$MENUOBJ->imga."</imga>";
		}

		if (strlen($MENUOBJ->alt_title)) $ret.='<title>'.htmlspecialchars($MENUOBJ->alt_title).'</title>';
		if (strlen($MENUOBJ->fgcolor)) $ret.='<color>'.htmlspecialchars('#').$MENUOBJ->fgcolor.'</color>';
		

		if (strlen($MENUOBJ->variables))
		{
			foreach (explode('&',$MENUOBJ->variables) AS $pair)
			{
				$p=explode('=',$pair);
				if (strlen($p[0])) $MENUOBJ->_tag_values.=' '.$p[0].'="'.$p[1].'"';
			}

		}
		if ( isMenuTargetInPageTree($MENUOBJ,$WEBPAGE) ) $ret.="<active>1</active>";

		$_LINK_TYPY_DXML=array();
		if (is_array($LINK_TYPY_DXML[$MENUOBJ->type+0])) $_LINK_TYPY_DXML=array_merge($_LINK_TYPY_DXML,$LINK_TYPY_DXML[$MENUOBJ->type+0]);
		if (is_array($LINK_TYPY_DXML['*'])) $_LINK_TYPY_DXML=array_merge($_LINK_TYPY_DXML,$LINK_TYPY_DXML['*']);		

		if (count($_LINK_TYPY_DXML))
		{
			foreach ($LINK_TYPY_DXML AS $k=>$sraka)
			{
				$ret.="<$k>".$MENUOBJ->$k."</$k>";
			}

		}
		
		if ($MENUOBJ->submenu_id) $ret.= kameleon_menu2xml($MENUOBJ->submenu_id,$root,$levelname);
		return $ret;
	}



	function kameleon_menu2xml($mid,$root='menu',$levelname='element') 
	{
		$MENUARR = kameleon_menus($mid);
		$ret = "<$root>";

		if (!strlen($levelname) ) $levelname=$MENUARR[0]->name;
		if (!strlen($levelname) ) $levelname="element";
		
		
		for	($m=0;is_array($MENUARR)&&$m<count($MENUARR);$m++) 
		{
			$MN = $MENUARR[$m];
			if ($MN->hidden==1) continue;
			$MN->_tag_values='';
			$inside=kameleon_menulink2xml($MN,$root,$levelname);
			$tag_values=$MN->_tag_values;
			$ret.= "<".$levelname.$tag_values.">";
			$ret.= $inside;
			$ret.= "</".$levelname.">";	
		}

		$ret.= "</$root>";
		return $ret;
	}


	function kameleon_td_xml2fake_html($xml,$sid)
	{
		return '<?php if (false) {?><sid'.$sid.'>'.$xml.'</sid'.$sid.'><?}?>'."\n";
	}

	function kameleon_mode_swf_link(&$WEBTD,$html='')
	{
		global $adodb,$kameleon;
		static $temp_page_opened;

		$temp_page=$adodb->getSesionDir().'/'.$adodb->session_file_prefix.$kameleon->user[username].'.swf_page.'.$kameleon->current_server->id;
		

		if (strlen($html))
		{
			$plik=fopen($temp_page,$temp_page_opened?'a':'w');
			fwrite($plik,$html);
			fclose($plik);
			$temp_page_opened=true;
		}

		$link='remote/swf.php'.urlencode('?').'base'.urlencode('=').$temp_page;
		$link.=urlencode('&').'_time'.urlencode('=').time();	
		$link.=urlencode('&').'sid'.urlencode('=').$WEBTD->sid;

		return $link;

	}


	function kameleon_td2swf_obj(&$WEBTD)
	{
		global $KAMELEON_MODE,$kameleon,$UIMAGES,$WEBPAGE;
		global $adodb;
		global $DEFAULT_PATH_PAGES_PREFIX,$DEFAULT_PATH_PAGES,$SWF_OBJECT_PARAMS;
		global $CONST_SWF_JS,$CONST_SWF_UFO;
		global $lang,$ver;

		$xml=kameleon_td2xml($WEBTD);
		$html=kameleon_td_xml2fake_html($xml,$WEBTD->sid);

		$plain='';
		
		if (!$KAMELEON_MODE)
		{
			$plain.="\n$html";
			
			eval("\$swf=\"$DEFAULT_PATH_PAGES_PREFIX$DEFAULT_PATH_PAGES/swf.php\";");
			$link = strlen($WEBPAGE->file_name) ? kameleon_relative_dir($WEBPAGE->file_name,$swf) : 'swf.php' ;
			$link.=urlencode('?').'base'.urlencode('=').'<?php echo $_SERVER["SCRIPT_NAME"]?>';
			$link.=urlencode('&').'sid'.urlencode('=').$WEBTD->sid;
		}
		else
		{
			$link=kameleon_mode_swf_link($WEBTD,$html);
		}
		
		$_swf['flashvars']='';

		reset($SWF_OBJECT_PARAMS);
		while (list($name,$values)=each($SWF_OBJECT_PARAMS)) 
		{
			if (!strlen($values)) continue;
			$v=explode('|',$values);
			$_swf[$name]=$v[0];
		}		



		$align='';
		if (strlen($WEBTD->align)) $align=' align="'.$WEBTD->align.'"';

		parse_str($WEBTD->xml);

		$_swf['flashvars']="my_xml=$link&clicktag=".urlencode(kameleon_href('','',$WEBTD->next));

		
		$plain.="<object width=\"".$WEBTD->width."\" height=\"".$WEBTD->size."\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"$align>\n";
		$plain.="	<param name=\"Movie\" value=\"$UIMAGES/".$WEBTD->bgimg."\" />\n";
		$plain.="	<param name=\"Src\" value=\"$UIMAGES/".$WEBTD->bgimg."\" />\n";

		$ufo_fo="majorversion:\"6\", build:\"40\", movie:\"$UIMAGES/".$WEBTD->bgimg."\", width:\"".$WEBTD->width."\", height:\"".$WEBTD->size."\"";

		$embed="";
		reset($_swf);
		while (list($name,$value)=each($_swf)) 
		{
			$plain.="	<param name=\"$name\" value=\"$value\" />\n";
			$embed.=" $name=\"$value\"";
			$ufo_fo.=", $name:\"$value\"";
		}

		$plain.="	<embed type=\"application/x-shockwave-flash\" src=\"$UIMAGES/".$WEBTD->bgimg."\" width=\"".$WEBTD->width."\" height=\"".$WEBTD->size."\" $embed$align />\n";
		$plain.="</object>\n";



		if ($CONST_SWF_JS || $CONST_SWF_UFO) 
		{
			if (!$KAMELEON_MODE)
			{
				global $DEFAULT_PATH_PAGES;

				eval("\$js=\"$DEFAULT_PATH_PAGES_PREFIX$DEFAULT_PATH_PAGES/swf.js\";");
				eval("\$ufo=\"$DEFAULT_PATH_PAGES_PREFIX$DEFAULT_PATH_PAGES/ufo.js\";");
				$js = strlen($WEBPAGE->file_name) ? kameleon_relative_dir($WEBPAGE->file_name,$js) : 'swf.js' ;
				$ufo = strlen($WEBPAGE->file_name) ? kameleon_relative_dir($WEBPAGE->file_name,$ufo) : 'ufo.js' ;
			}
			else
			{
				$js="remote/swf.js";
				$ufo="remote/ufo.js";
			}

			static $ufo_included;

			$oid='ta_swf_'.$WEBTD->sid;
			$did='di_swf_'.$WEBTD->sid;
			$plain="<textarea style=\"display:none\" id=\"$oid\">$plain</textarea>";
			if ($CONST_SWF_UFO) 
			{
				$plain="<div style=\"margin:0;padding:0\" id=\"$did\"></div>";
				if (!$ufo_included) $plain.="\n<script type=\"text/javascript\" src=\"$ufo\"></script>\n";
				$plain.="\n<script type=\"text/javascript\">\nvar FO = ".'{'.$ufo_fo.'}'.";\nUFO.create(FO,'$did');\n</script>\n";

				$ufo_included = true;
			}
			else
			{
				$plain.="\n<script type=\"text/javascript\">\nvar sid=".$WEBTD->sid.";\n</script>\n";
				$plain.="\n<script type=\"text/javascript\" src=\"$js\"></script>\n";
			}

			

		}


		return $plain;
	}

