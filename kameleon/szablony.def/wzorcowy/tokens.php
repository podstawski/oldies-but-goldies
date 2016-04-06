<?
function mapa($_root) 
{
	global $SERVER_ID, $lang, $ver, $adodb,$IMAGES;
	$mapa ="<ul class=\"parent\">";
	$sql = "SELECT * FROM webpage WHERE prev=".$_root." AND (nositemap!=1 OR nositemap IS NULL)	AND server=".$SERVER_ID."	AND lang='".$lang."' AND ver=".$ver."	ORDER BY  title;";
	$res = $adodb->execute($sql);
	for ($i=0;$i<$res->RecordCount();$i++) {
		parse_str(ado_explodename($res, $i));	
		if (strlen($title_short)) $_title=$title_short;
		else $_title=$title;
		$_link = "";
		if (!$hidden) $_link=kameleon_href('','',$id);
		$sqlc = "SELECT count(*) AS child_count FROM webpage WHERE prev=".$id." AND (nositemap!=1 OR nositemap IS NULL)	AND server=".$SERVER_ID." AND lang='".$lang."' AND ver=".$ver;
		$resc = $adodb->execute($sqlc);
		parse_str(ado_explodename($resc, 0));	
		$rozwijak = "<span class=\"suw ".($child_count ? "par plus" : "no")."\"></span>";
		$mapa.= "<li>".$rozwijak.(strlen($_link)>0 ? "<a href=\"".$_link."\">" : "").str_replace("&","&amp;",$_title).(strlen($_link)>0 ? "</a>" : "").($child_count ? mapa($id) : "")."</li>";
	}	
	return $mapa."</ul>";	
}

function genTreeStructure($dir,$mode=0700)
{
	if (!is_dir(dirname($dir))) genTreeStructure(dirname($dir),$mode);
	if (is_dir(dirname($dir))) 
	{
		mkdir($dir,$mode);
		chmod($dir,$mode);
	}	
}

function genMiniatura($img,$dir,$dst,$wh,$ht)
{
  global $UIMAGES, $IMAGES;
	$filename = $UIMAGES."/".$img;
	if (file_exists($filename) && strlen($img)>0)
	{
    $pinfo = pathinfo($filename);    
    
    $ext = strtolower($pinfo['extension']);
    if (!is_dir($UIMAGES."/".$dir))  genTreeStructure($UIMAGES."/".$dir);
    $newfilename = $UIMAGES."/".$dir.$dst;
    switch($ext) 
    {
    	case 'jpg':
    	case 'jpeg':
    		$source = imagecreatefromjpeg($filename);
    		break;
    		
    	case 'png':
    	  $source = imagecreatefrompng($filename);
    		break;
    		
    	case 'gif':
    	  $source = imagecreatefromgif($filename);
    		break;
    }
    
    list($width, $height) = getimagesize($filename);
    $thumb = imagecreatetruecolor($wh, $ht);
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $wh, $ht, $width, $height);
    imagejpeg($thumb, $newfilename, 80);	
    return $dir.$dst;
  }
	return "";
}


function tokens($t)
{
	global $WEBPAGE, $KAMELEON_MODE, $UIMAGES, $WEBTD, $WEBLINK, $IMAGES, $KAMELEON_UIMAGES, $SERVER_ID;
	global $page, $ver, $lang, $editmode, $adodb;
	global $LINK_TYPY, $tokens;
	global $SZABLON_PATH;

	$pages_list = explode(":",$WEBPAGE->tree);
	$pages_list[] = $page;
	
	switch ($t)	{
		
		case "WEBPAGE_RSS": 
		    $ret = "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS\" href=\"http://kdgtest.ue.poznan.pl/o-projekcie/aktualnosci/rss/\" />";
			return $ret; 
		break;
		
		case "WEBPAGE_CSS_SRC":
		if ($KAMELEON_MODE)
		{
			$t=str_replace($SZABLON_PATH,"",dirname($_SERVER['SCRIPT_NAME']));
			if (strlen($t)>1) $t.="/";
		}
		else 
			$t=$DEFAULT_PATH_PAGES_PREFIX;
		return "
				<link href=\"".$IMAGES."/css.php?p=".base64_encode($IMAGES)."&t=".$t."\" rel=\"stylesheet\" type=\"text/css\" />
				<link rel=\"shortcut icon\" type=\"image/x-icon\" href=\"".$UIMAGES."/favicon.ico\" />
				<link href=\"".$IMAGES."/druk.css\" rel=\"stylesheet\" type=\"text/css\"  media=\"print\" />";
		break;
	
		case "WEBPAGE_STYLE": 
			$ret = "style=\"";
			$ret.= "margin:0px;";
			$ret.= "\"";
			return $ret; 
		break;	
		
		case "WEBPAGE_GOOGLE_ANALITICS":
			$ret = "<script type=\"text/javascript\"></script>";
			if ($KAMELEON_MODE) $ret = " ";
			return $ret; 
		break;
		
		case "BOX_TITLE":
			$naglowek = strlen($WEBTD->naglowek) ? $WEBTD->naglowek : "h2";
			if (strlen($WEBTD->title)) {
       			if ($WEBTD->next) return "<".$naglowek." class=\"title\"><a href=\"".kameleon_href("","",$WEBTD->next)."\">".$WEBTD->title."</a></".$naglowek.">";
				return "<".$naglowek." class=\"title\">".$WEBTD->title."</".$naglowek.">";
			}
			return " ";
		break;
		
		case "WEBTD_SEARCH":
			$c="";
			$c.="<form action=\"".kameleon_href("","",$WEBTD->next)."\" method=\"post\" class=\"se_search\">
			<label for=\"se_input\">".$WEBTD->title."</label><input id=\"se_input\" class=\"text\" name=\"api_query\" type=\"text\" value=\"\" /><input class=\"dot\" src=\"".$UIMAGES."/".$WEBTD->img."\" type=\"image\" /></form>";
			if ($WEBTD->menu_id){
				$menu = kameleon_menus($WEBTD->menu_id);
				$c.="<ul class=\"search_menu\">\n";
				for ($i=0;$i<sizeof($menu);$i++)
				{
					$c.="<li><a href=\"".kameleon_href($menu[$i]->href,$menu[$i]->variables,$menu[$i]->page_target)."\">".$menu[$i]->alt."</a></li>\n";
				}
				$c.="</ul>\n";
			}
			return $c;
		break;
		
		case "WEBTD_TOPMENU":
	  		if (!$WEBTD->menu_id) return " ";
			$menu = kameleon_menus($WEBTD->menu_id);
			$c="<ul class=\"top_menu\">\n";
			for ($i=0;$i<sizeof($menu);$i++)
			{
				$c.="<li ".(isMenuTargetInPageTree($menu[$i],$WEBPAGE) ? "class=\"active\"" : "")."><a href=\"".kameleon_href($menu[$i]->href,$menu[$i]->variables,$menu[$i]->page_target)."\">".$menu[$i]->alt."</a></li>\n";
			}
			$c.="</ul>\n";
			return $c;
		break;
		
		case "WEBTD_SUBMENU":
			if (!$WEBTD->menu_id) return " ";
			$menu = kameleon_menus($WEBTD->menu_id);
			$c="<ul>\n";
			for ($i=0;$i<sizeof($menu);$i++)
			{
				$c.="<li ".($WEBPAGE->id==$menu[$i]->page_target ? "class=\"active\"" : "")."><a href=\"".kameleon_href($menu[$i]->href,$menu[$i]->variables,$menu[$i]->page_target)."\">".$menu[$i]->alt."</a>";
				if ($menu[$i]->submenu_id && isMenuTargetInPageTree($menu[$i],$WEBPAGE))
				{
					$submenu = kameleon_menus($menu[$i]->submenu_id);
					$c.="<ul>";
					for ($j=0;$j<sizeof($submenu);$j++)
					{
						$c.="<li ".(isMenuTargetInPageTree($submenu[$j],$WEBPAGE) ? "class=\"active\"" : "")."><a href=\"".kameleon_href($submenu[$j]->href,$submenu[$j]->variables,$submenu[$j]->page_target)."\">".$submenu[$j]->alt."</a></li>\n";
					}
					$c.="</ul>\n";
				}
				$c.="</li>\n";
			}
			$c.="</ul>";
			return $c;
		break;
		
		case "WEBTD_MAPA":
			if (!$WEBTD->staticinclude)
			{
				$sql = "UPDATE webtd SET staticinclude=1 WHERE sid=".$WEBTD->sid;
				$adodb->execute($sql);
			}
			echo "<div id=\"supermapa\" class=\"mapa\">";
			echo mapa(0);
			echo "</div>";
			echo "<script type=\"text/javascript\">
					function createMapa(objid) {
 					$(\"#\"+objid+\" ul li ul\").hide();
  					$(\"#\"+objid+\" .par\").click(function() { 
	    			if ($(this).parent().find(\"ul\").css('display')=='block') {
      					$(this).parent().find(\"ul\").hide();
      					$(this).removeClass('minus');
      					$(this).addClass('plus');
      					$(this).parent().find(\".minus\").addClass('plus');
      					$(this).parent().find(\".minus\").removeClass('minus');
    				}
    				else {
      					$(this).parent().find(\"ul\").show(); 
      					$(this).removeClass('plus');
      					$(this).addClass('minus');
      					$(this).parent().find(\"ul ul\").hide();
    				} 
  					});
					}
					$(document).ready(function() {
  					createMapa(\"supermapa\");
					});
				</script>";

			return " ";
		break;
		
		case "WEBKAMLEON_FOOTER": 
			return 	"
				<div class=\"sitecredits\">
					projekt www <a href=\"http://www.gammanet.pl/\" target=\"_blank\">Gammanet</a>. cms <a href=\"http://www.webkameleon.com\" target=\"_blank\">WebKameleon</a>
				</div>
				<div class=\"copyright\">
					copyright 2011 <b>Gammanet Sp. z o.o.</b> Wszelkie prawa zastrzeżone.
				</div>"; 
		break;
		
		case "WEBTD_BREADCRUMB":
			$webpage_tree=explode(":",$WEBPAGE->tree);
			$webpage_tree[]=$WEBPAGE->id;
			for ($i_tree=0;$i_tree<(count($webpage_tree)) && is_array($webpage_tree);$i_tree++)
			{
				if (!strlen($webpage_tree[$i_tree])) continue;
				$parent_page=$webpage_tree[$i_tree];
				unset($PARENT_WEBPAGE);
				$PARENT_WEBPAGE=kameleon_page($parent_page);
				if ($PARENT_WEBPAGE[0]->nositemap) 
					continue;
				$title=$PARENT_WEBPAGE[0]->title;
				if (strlen($PARENT_WEBPAGE[0]->title_short))
					$title=$PARENT_WEBPAGE[0]->title_short;
				if (!$parent_page) {
					$title = "<img src=\"".$UIMAGES."/home.png\" alt=\"home\" />Strona główna";
				}
				$href=kameleon_href("","",$parent_page);
				if (!$PARENT_WEBPAGE[0]->hidden && strlen($title)>0) 
					$title="<a href=\"$href\">$title</a>";
				$path.=$title;
				if ($i_tree<(count($webpage_tree)-1) && strlen($title)>0)	$path.="<img src=\"".$UIMAGES."/dot.png\" alt=\"dot\" />";
			} 
			if (!$WEBPAGE->id) 
				$_path.="&nbsp;";
			else
				$_path.=$path;
			if ($WEBPAGE->id!=0) return "<div class=\"breadcrumb\">".$_path."</div>";
		return " ";
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
			$ret = " ";
			if ($WEBTD->class) $ret=" class=\"".$WEBTD->class."\""; 
			else $ret=" class=\"box\""; 
			return $ret;
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
		
		case "WEBTD_OPCJE_DODATKOWE": 	
			$ret = "
				<a class=\"drukuj\" href=\"javascript:window.print()\">Wydrukuj</a>
				<a class=\"polec\" href=\"".kameleon_href("","",$WEBTD->next)."\">Poleć znajomemu</a>
				<a class=\"dodaj\" href=\"javascript:dodajUlubione('','".$WEBPAGE->title."');\" title=\"Dodaj stronę główną do ulubionych\">Dodaj do ulubionych</a>";
			return $ret;
		break; 
		
		case "WEBTD_GALLERY": 	
			if (!$WEBTD->menu_id) {
				echo "<div class=\"error\">Brak podpiętego menu!</div>";
				return " ";
      		}
			$menu = kameleon_menus($WEBTD->menu_id);
     	 	if (sizeof($menu)==0) return " ";
      		// imga - miniatura z img
      		if ($KAMELEON_MODE)
		  	{
		    	//echo "<div class=\"error\">Nowe obrazki należy dodawać jako \"Obrazek w aktywnym linku\" <br />W przypadku podmiany zdjęcia usunąć zawartość w \"Obrazek w linku\"<br />Tytuł zdjęcia w polu \"Tekst alternatywny\", Opis zdjęcia w polu \"Tytuł\"</div>";
		    	include_once('include/webver.h');
		    	global $adodb;
        		for ($i=0;$i<sizeof($menu);$i++)
        		{          
          		if (strlen($menu[$i]->img)==0 && file_exists($UIMAGES."/".$menu[$i]->img))
          		{
            		$pinfo = pathinfo($UIMAGES."/".$menu[$i]->imga);
            		$filename = str_replace(".".$pinfo["extension"],"",$pinfo["basename"]);
					if (strlen($menu[$i]->img)==0 || !file_exists($UIMAGES."/galeria/mini/".$WEBPAGE->id."/".$filename.".jpg"))
					{
						$menu[$i]->img = genMiniatura($menu[$i]->imga,"galeria/mini/".$WEBPAGE->id."/",$filename.".jpg",170,127);
						$MAY_PROOF = 1;
						$adodb->debug=0;
						$sql = "SELECT * FROM weblink WHERE sid = ".$menu[$i]->sid." AND server = $SERVER_ID  AND lang = '$lang' AND ver = $ver";
						parse_str(ado_query2url($sql));
						$img= $menu[$i]->img;
						include "include/action/ZapiszLink.h";
					}
          		}
        	}
        	$menu = kameleon_menus($WEBTD->menu_id);
        	if (sizeof($menu)==0) return " ";
		}	
		if (sizeof($menu)>0)
		{
			$c="
				  <ul class=\"galeria\" id=\"realizacje_".$WEBTD->sid."\">";
				  $d=0; 
				  for ($i=0;$i<sizeof($menu);$i++)
				  {       
            if (strlen($menu[$i]->img))
					   $c.="<li><a href=\"".$UIMAGES."/".$menu[$i]->imga."\" title=\"".$menu[$i]->alt."\"><img src=\"".$UIMAGES."/".$menu[$i]->img."\" alt=\"".$menu[$i]->alt."\" /><div class=\"text\">".$menu[$i]->alt."</div></a></li>";
				  }
				  $c.="</ul>
				<script type=\"text/javascript\">
				$(function() {
					$('#realizacje_".$WEBTD->sid." a').lightBox({
					imageLoading: '".$IMAGES."/ligthbox/loading.gif', 
					imageBtnClose: '".$IMAGES."/ligthbox/close.gif',
					imageBtnPrev: '".$IMAGES."/ligthbox/prev.gif',
					imageBtnNext: '".$IMAGES."/ligthbox/next.gif',
					  imageBlank: '".$IMAGES."/ligthbox/blank.gif'
				  });
				});
				</script>
				";
			} 
      		else
        		$c=" "; 
     		return $c;
		break; 
		
		case "WEBTD_FILM":
			return " ";
		break;
		
		
		case "WEBTD_SLIDE_MENU":
		  if (!strlen($WEBTD->menu_id)) return " ";
		  $menu=kameleon_menus($WEBTD->menu_id);
		  if (sizeof($menu)>0)
		  {
			$newmenu = array();
			
			for ($w=0;$w<sizeof($menu);$w++)
			{
				$newmenu[]=$menu[$w];        
			}
			
			if (sizeof($newmenu)>0)
			{
			  $cos="
			  <script type=\"text/javascript\">
			  /* <![CDATA[ */
			  
			  ";
			  
			  $isfirst="";
			  $kolej=0;
			  for ($p=0;$p<sizeof($newmenu);$p++)
			  {
				if ($p==0)
				{
				  $isfirst="<img src=\"".$UIMAGES."/".$newmenu[$p]->img."\" title=\"0\" alt=\"\" id=\"slideshow_slide0\" />";
				  $kolej=1;
				}
				$cos.="
				n = slideshow_slides.length;
				slideshow_slides[n]=new Array(5);
				slideshow_slides[n][0]='";
				if (substr($newmenu[$p]->href,0,1)=="#") $cos.="javascript:".substr($newmenu[$p]->href,1)."(".$newmenu[$p]->variables.")";
				else $cos.=kameleon_href($newmenu[$p]->href,$newmenu[$p]->variables,$newmenu[$p]->page_target);
				$cos.="';
				slideshow_slides[n][1]='".$UIMAGES."/".$newmenu[$p]->img."';
				slideshow_slides[n][2]=false;
				slideshow_slides[n][3]='".$newmenu[$p]->alt_title."';
				slideshow_slides[n][4]='';
				";
			  } 
			  $cos.="
				$(document).ready(function()
				{  
				  $(window).load( 
					function () {slidediv_begin(".$kolej.");}
				  );  
				});
				
			  /* ]]> */
			  </script>
			
			  <div class=\"slideshow\" id=\"slideshow\">
				<div class=\"slides\">
				  <div class=\"click\"><a><img src=\"".$IMAGES."/slideshow/clicker.png\" alt=\"Szczegóły\" /></a></div>
				  <div class=\"slide_col\">".$isfirst."</div>
				</div>
				<div class=\"navi\">
				  <div class=\"arrow\"><a href=\"javascript:slideshow_change(-1)\"><img src=\"".$IMAGES."/slideshow/navi_left.png\" alt=\"Poprzednie\" /></a></div>
				  <div class=\"items\"></div>
				  <div class=\"arrow\"><a href=\"javascript:slideshow_change(0)\"><img src=\"".$IMAGES."/slideshow/navi_right.png\" alt=\"Następne\" /></a></div>
				  <div class=\"clean\"></div>
				</div>
			  </div>";
			  
			  return $cos;
			}
			return " ";
		}
		return " ";
		break;

				
		default:
		return "";
	}
}
?>
