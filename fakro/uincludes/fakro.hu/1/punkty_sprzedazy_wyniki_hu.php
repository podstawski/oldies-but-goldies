<?
	global $SZUKAJ_PUNKTU, $list;
	//var_dump($list);
	//exit;
	
	//ini_set('display_errors', 1);
	//ini_set('error_reporting', E_ALL);
  
	$LIST = $list;
	$szukaj = $SZUKAJ_PUNKTU;

	if(!$size) $size=25;

	parse_str($costxt);
	
	$cond = '';
	if(!is_array($szukaj) && !strlen($tylko_dla)) return;
		if(strlen($tylko_dla))
		$cond.= " AND punkty_sprzedazy_hu.id_woj = '$tylko_dla'";
	if(strlen($szukaj['nazwa']))
		$cond.= " AND punkty_sprzedazy_hu.nazwa ~* '".addslashes(stripslashes($szukaj['nazwa']))."'";
	if(strlen($szukaj['miejsce']))
		$cond.= " AND punkty_sprzedazy_hu.miasto ~* '".addslashes(stripslashes($szukaj['miejsce']))."'";

	//$fakrodb->debug=1;
	$sql = "SELECT * FROM punkty_sprzedazy_hu WHERE type = 'S' $cond ORDER BY nazwa";
	$res = $fakrodb->execute($sql);

	if(!$LIST['ile']) {
		$query = "SELECT count(id) AS c FROM punkty_sprzedazy_hu WHERE type = 'S' $cond";
		$res2 = $fakrodb->execute($query);
		parse_str(ado_explodename($res2,0));
		$LIST[ile] = $c;
		}
	if($KAMELEON_MODE) {
		$self.="&SZUKAJ_PUNKTU[nazwa]=".$SZUKAJ_PUNKTU[nazwa]."&SZUKAJ_PUNKTU[miejsce]=".$SZUKAJ_PUNKTU[miejsce];
		}else{
		$self.="?SZUKAJ_PUNKTU[nazwa]=".$SZUKAJ_PUNKTU[nazwa]."&SZUKAJ_PUNKTU[miejsce]=".$SZUKAJ_PUNKTU[miejsce];
		}

	$navi = $size?navi($self,$LIST,$size):"";

	if(strlen($navi)) {
		$res = $fakrodb->SelectLimit($sql,$size,$LIST['start']+0);
		}else{
		$res = $fakrodb->Execute($sql);
		}

	#$fakrodb->debug=0;

	if(!$res->RecordCount()) {
		echo "Brak punktów sprzedaży spełniających podane kryteria.";
		return;
		}

	echo "$navi<br><br>
	<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
	<tbody>";

	for($i=0; $i < $res->RecordCount(); $i++) {
		parse_str(ado_explodename($res,$i));
		echo "
		<TR class=\"".(($i && ($i%2))?"even":"odd")."\">
			<TD class=\"name\" valign=\"top\">
			".(($www)?"<a href=\"http://".$www."\" target=\"_blank\"><img src=\"".$UIMAGES."/COMMON/ikony/i_www.gif\" align=\"right\" border=\"0\"></a>":"")."
			".(($mail)?"<a href=\"mailto:".$mail."\" target=\"_blank\"><img src=\"".$UIMAGES."/COMMON/ikony/i_mail.gif\" align=\"right\" border=\"0\"></a>":"")."".stripslashes($nazwa)."</TD>
			<TD valign=\"top\">$kod $miasto<br>$adres</TD>
			<TD valign=\"top\" style=\"font-weight:bold\">$telefon1<br>$telefon2</TD>
		</TR>
		";
		}

	echo "</tbody></table>";
	echo "
		<table class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
		<form method=\"".($KAMELEON_MODE?"POST":"GET")."\" action=\"$self\">
		<tr>
			<td></td>
			<td></td>
		</tr>
		</form>
		</table>";
?>
