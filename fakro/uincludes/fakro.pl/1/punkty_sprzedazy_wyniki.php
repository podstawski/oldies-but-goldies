<?

	global $SZUKAJ_PUNKTU, $list;

	$LIST = $list;

	$szukaj=$SZUKAJ_PUNKTU;

	if (!$size) $size=25;

	parse_str($costxt);
	
	
	function zamien($str) {
	    $old = array('e', 'o', 'a', 's', 'l', 'z', 'c', 'n', 'E', 'O', 'A', 'S', 'L', 'Z', 'C', 'N');
	    $new = array('(e|ę)', '(o|ó)', '(a|ą)', '(s|ś)', '(l|ł)', '(z|ż|ź)', '(c|ć)', '(n|ń)', '(E|Ę)', '(O|Ó)', '(A|Ą)', '(S|Ś)', '(L|Ł)', '(Z|Ż|Ź)', '(C|Ć)', '(N|Ń)');
	    return str_replace($old, $new, $str);
	}
	
	if (!is_array($szukaj) && !strlen($tylko_dla)) return;

	if (strlen($tylko_dla))
	    $cond.= " AND ps_wojewodztwo = '$tylko_dla'";
	
	if (strlen($szukaj['nazwa'])) {
	    $szukaj['nazwa'] = zamien($szukaj['nazwa']);
	    
	    $cond.= " AND (ps_nazwa ~* '".addslashes(stripslashes(mb_strtoupper($szukaj['nazwa'], "UTF-8")))."'";
	    $cond.= " OR ps_nazwa ~* '".addslashes(stripslashes($szukaj['nazwa']))."' )";
	}

	if (strlen($szukaj[miejsce])) {
	    $szukaj['miejsce'] = zamien($szukaj['miejsce']);
	
	    $cond.= " AND (ps_miasto ~* '".addslashes(stripslashes(mb_strtoupper($szukaj['miejsce'], "UTF-8")))."'";
	    $cond.= " OR ps_miasto ~* '".addslashes(stripslashes($szukaj['miejsce']))."' )";
	}

	$sort = " ORDER BY ps_miasto, ps_nazwa";
	
	if($list['sort']==1 && $list['type']==1) $sort = " ORDER BY ps_nazwa ASC";
	if($list['sort']==1 && $list['type']==2) $sort = " ORDER BY ps_nazwa DESC";
	
	if($list['sort']==2 && $list['type']==1) $sort = " ORDER BY ps_miasto ASC";
	if($list['sort']==2 && $list['type']==2) $sort = " ORDER BY ps_miasto DESC";
	
	if($list['sort'] && $list['type']) $self_sort = "&list[sort]=".$list['sort']."&list[type]=".$list['type'];
	
//	$fakrodb->debug=1;
	$sql = "SELECT * FROM punkty_sprzedazy WHERE ps_typ='S' $cond $sort";
	$res = $fakrodb->execute($sql);

	if (!$LIST[ile])
	{
		$query="SELECT count(ps_id) AS c FROM punkty_sprzedazy WHERE ps_typ='S' $cond ";
		$res2 = $fakrodb->execute($query);
		parse_str(ado_explodename($res2,0));
		$LIST[ile]=$c;
	}
	if ($KAMELEON_MODE)
		$self.="&SZUKAJ_PUNKTU[nazwa]=".$SZUKAJ_PUNKTU['nazwa']."&SZUKAJ_PUNKTU[miejsce]=".$SZUKAJ_PUNKTU['miejsce'].$self_sort;
	else
		$self.="?SZUKAJ_PUNKTU[nazwa]=".$SZUKAJ_PUNKTU['nazwa']."&SZUKAJ_PUNKTU[miejsce]=".$SZUKAJ_PUNKTU['miejsce'].$self_sort;

	$navi=$size?navi($self,$LIST,$size):"";
		
	if (strlen($navi))
		$res = $fakrodb->SelectLimit($sql,$size,$LIST[start]+0);
	else
		$res = $fakrodb->Execute($sql);	

	$fakrodb->debug=0;

	if (!$res->RecordCount())
	{
		if (strlen($tylko_dla))
		{
			$tylko_dla = str_replace("kujawskopomorskie","kujawsko-pomorskie",$tylko_dla);
			$tylko_dla = str_replace("zachodniopomorskie","zachodnio-pomorskie",$tylko_dla);
			$tylko_dla = str_replace("warmińskie","warmińsko-mazurskie",$tylko_dla);
			$tylko_dla = str_replace("wielkopolska","wielkopolskie",$tylko_dla);
			$addinfo = "Województwo: $tylko_dla.";
		}
		echo "Brak punktów sprzedaży spełniających podane kryteria. $addinfo";
		return;
	}

	$pic = "<img valign=\"absmiddle\" title=\"Punkt sprzedaży posiada okna ekspozycyjne\" alt=\"Sklep posiada okno ekspozycyjne\" src=\"$UIMAGES/sb/check.gif\">";

	//$pic - Punkt sprzeda\xbfy posiada okna ekspozycyjne<br><br>

	echo "
	$navi<br><br>
	<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
	<tbody>";
	
	$sort_firma = '<a href="./'.$self.'&list[sort]=1&list[type]=1"><img src="'.$IMAGES.'/sort_null.gif" width="9" height="15" border="0"> Firma</a>';
	if($list['sort']==1 && $list['type']==1) $sort_firma = '<a href="./'.$self.'&list[sort]=1&list[type]=2"><img src="'.$IMAGES.'/sort_asc.gif" width="9" height="15" border="0"> Firma</a>';
	if($list['sort']==1 && $list['type']==2) $sort_firma = '<a href="./'.$self.'&list[sort]=1&list[type]=1"><img src="'.$IMAGES.'/sort_desc.gif" width="9" height="15" border="0"> Firma</a>';
	
	$sort_adres = '<a href="./'.$self.'&list[sort]=2&list[type]=1"><img src="'.$IMAGES.'/sort_null.gif" width="9" height="15" border="0"> Adres</a>';
	if($list['sort']==2 && $list['type']==1) $sort_adres = '<a href="./'.$self.'&list[sort]=2&list[type]=2"><img src="'.$IMAGES.'/sort_asc.gif" width="9" height="15" border="0"> Adres</a>';
	if($list['sort']==2 && $list['type']==2) $sort_adres = '<a href="./'.$self.'&list[sort]=2&list[type]=1"><img src="'.$IMAGES.'/sort_desc.gif" width="9" height="15" border="0"> Adres</a>';
	
	echo "
	<TR>
		<TD class=\"name\" valign=\"top\">".$sort_firma."</TD>
		<TD class=\"name\" valign=\"top\">".$sort_adres."</TD>
		<TD class=\"name\" valign=\"top\">Kontakt</TD>
	</TR>
	";
	
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));
		
		if($ps_ma_okno) $ico_okno = '<img src="'.$IMAGES.'/ico_gdzie_kupic_okno.gif" width="15" height="15" title="Punkt sprzedaży posiada okna ekspozycyjne" alt="Punkt sprzedaży posiada okna ekspozycyjne" border="0">';
		if($ps_ma_schody) $ico_schody = '<img src="'.$IMAGES.'/ico_gdzie_kupic_schody.gif" width="15" height="15" title="Punkt sprzedaży posiada schody ekspozycyjne" alt="Punkt sprzedaży posiada schody ekspozycyjne" border="0">';
		if($ps_ma_okno || $ps_ma_schody) $div_info_ekspozycja = '<div class="ico_punkty_sprzedazy">'.$ico_okno.' '.$ico_schody.'</div>';
		
		if($ps_ma_www) $firma = '<a href="http://'.$ps_ma_www.'" target="_blank">'.stripslashes($ps_nazwa).'</a>';
			else $firma = stripslashes($ps_nazwa);
		
		echo "
		<TR class=\"".(($i && ($i%2))?"even":"odd")."\">
			<TD class=\"name\" valign=\"top\">".$div_info_ekspozycja."".$firma."</TD>
			<TD valign=\"top\">$ps_kod $ps_miasto<br>$ps_adres</TD>
			<TD valign=\"top\" style=\"font-weight:bold\">$ps_kontakt</TD>
		</TR>
		";
	unset($ps_ma_okno); unset($ico_okno);
	unset($ps_ma_schody); unset($ico_schody);
	unset($div_info_ekspozycja);
	}
	echo "</tbody></table><br>";
	
	echo '<img src="'.$IMAGES.'/ico_gdzie_kupic_okno.gif" width="15" height="15" title="Punkt sprzedaży posiada okna ekspozycyjne" alt="Punkt sprzedaży posiada okna ekspozycyjne" border="0"> Punkt sprzedaży posiada okna ekspozycyjne';
	echo '<br>';
	echo '<img src="'.$IMAGES.'/ico_gdzie_kupic_schody.gif" width="15" height="15" title="Punkt sprzedaży posiada schody ekspozycyjne" alt="Punkt sprzedaży posiada schody ekspozycyjne" border="0"> Punkt sprzedaży posiada schody ekspozycyjne';
	
	//<TD>".($ps_ma_okno?$pic:"&nbsp;")."</TD>
	
	echo "
	<table class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
	<form method=\"".($KAMELEON_MODE?"POST":"GET")."\" action=\"$self\">
	<tr>
		<td></td>
		<td></td>
	</tr>
	</form>
	</table>
	";
?>
