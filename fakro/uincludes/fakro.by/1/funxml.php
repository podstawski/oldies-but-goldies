<?
class XMLTD
{
	var $title;	
	var $plain;
	var $img;
	var $menu_id;
	var $more;
	var $more_txt;
	var $next;
	var $next_txt;
	
	function td2xml() {
		$rettdxml = "";
		if ($this->title) $rettdxml.= "<title>".$this->title."</title>";
		if ($this->plain) $rettdxml.= "<plain>".$this->plain."</plain>";
		if ($this->img) $rettdxml.= "<img>".$this->img."</img>";
		
		if ($this->more) { 
			$rettdxml.= "<more>";
			if ($this->more) $rettdxml.= "<alt>".$this->more_txt."</alt>";
			$_more = explode("&",$this->more);
			$rettdxml.= "<url>".$_more[0]."</url>";
			$rettdxml.= "</more>";
		}
		
		if ($this->next) { 
			$rettdxml.= "<next>";
			if ($this->more) $rettdxml.= "<alt>".$this->next_txt."</alt>";
			$_next = explode("&",$this->next);
			$rettdxml.= "<url>".$next[0]."</url>";
			$rettdxml.= "</next>";
		}
		
		if ($this->menu_id) $rettdxml.= $this->menu2xml($this->menu_id);
		return $rettdxml;
	}
	
	function menu2xml($mid) {
		$MENUARR = kameleon_menus($mid);
		$levelname="element";
		$ret = "<menu>";
		
		for	($m=0;is_array($MENUARR)&&$m<count($MENUARR);$m++) {
			$MN = $MENUARR[$m];
			if ($MN->hidden==1) continue;
			$ret.= "<".$levelname.">";
			$ret.= $this->menulink2xml($MN);
			$ret.= "</".$levelname.">";	
		}

		$ret.= "</menu>";
		return $ret;
	}
	
	function menulink2xml($MENUOBJ) {
		global $UIMAGES;
		$ret = "";
		$ret.= "<alt>".$MENUOBJ->alt."</alt>";
		
		if ($MENUOBJ->page_target) 
			$url = kameleon_href('','',$MENUOBJ->page_target);
		if ($MENUOBJ->href) 
			$url = $MENUOBJ->href;
		$_url = explode("&",$url);
		$ret.= "<url>".$_url[0]."</url>";
				
		if ($MENUOBJ->img)	$ret.= "<img>".$UIMAGES."/".$MENUOBJ->img."</img>";
		if ($MENUOBJ->imga) $ret.= "<imga>".$UIMAGES."/".$MENUOBJ->imga."</imga>";
		
		if ($MENUOBJ->submenu_id) $ret.= $this->menu2xml($MENUOBJ->submenu_id);
		return $ret;
	}
}
?>