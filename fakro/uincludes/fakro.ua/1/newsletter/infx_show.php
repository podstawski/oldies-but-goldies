<?
	function rysuj_tabelke($typ,$kolor,$param)
	{
		$_tp = explode(":",$param);
		
		$_cell= "";
		
		if (strlen($_tp[9])) {
			$_star = ereg_replace("Kategoria ","", $_tp[9]);
			if ($_tp[10]) $_star.= "+";
		}	
		
		$_cell.= "<div class=\"lmc_1\">".$_tp[0]."<br>".$_tp[1].$_star."</div>";
		//$_cell.= "<div class=\"lmc_2\">".tui_cena($_tp[2],"newsletter2")."</div>";
		$_cell.= "<div class=\"lmc_2\">".$_tp[2]."</div>";

		$wylot="";
		if (strlen(trim($_tp[6]))) $wylot="wylot: ";
		$_cell.= "<div class=\"lmc_3\">$wylot".$_tp[6]."<br>".$_tp[3]."</div>";
		$_cell.= "<div class=\"lmc_3\">".(strlen($_tp[4])?$_tp[4]:"<br>")."</div>";
		$_cell.= "<div class=\"lmc_3\"><b>".$_tp[8]."</b></div>";
		
		$_cell.= "<div class=\"lmc_4\"><a href=\"".kameleon_href('','',$_tp[7])."\" >Zobacz</a></div>";

		return $_cell;
	}

	global $INFX_TYPY;

	$INFX_TYPY[0]="TYP";
	$INFX_TYPY[1]="Bialy tytul - biale litery";
	$INFX_TYPY[2]="Bialy tytul - czarne litery";
	$INFX_TYPY[3]="Czarny tytul - biale litery";
	$INFX_TYPY[4]="Czarny tytul - czarne litery";

?>
