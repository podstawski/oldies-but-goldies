
<?

	global $SZUKAJ_PUNKTU, $list;

	$LIST = $list;

	$szukaj=$SZUKAJ_PUNKTU;

	if (!$size) $size=25;

	parse_str($costxt);

	if (!is_array($szukaj) && !strlen($tylko_dla)) return;

	if (strlen($tylko_dla))
		$cond.= " AND ps_wojewodztwo = '$tylko_dla'";
	
	if (strlen($szukaj[nazwa]))
		$cond.= " AND ps_nazwa ~* '".addslashes(stripslashes($szukaj[nazwa]))."'";

	if (strlen($szukaj[miejsce]))
		$cond.= " AND ps_miasto ~* '".addslashes(stripslashes($szukaj[miejsce]))."'";

//	$fakrodb->debug=1;
	$sql = "SELECT * FROM punkty_sprzedazy WHERE ps_typ='S' $cond ORDER BY ps_nazwa";
	$res = $fakrodb->execute($sql);

	if (!$LIST[ile])
	{
		$query="SELECT count(ps_id) AS c FROM punkty_sprzedazy WHERE ps_typ='S' $cond ";
		$res2 = $fakrodb->execute($query);
		parse_str(ado_explodename($res2,0));
		$LIST[ile]=$c;
	}
	if ($KAMELEON_MODE)
		$self.="&SZUKAJ_PUNKTU[nazwa]=".$SZUKAJ_PUNKTU[nazwa]."&SZUKAJ_PUNKTU[miejsce]=".$SZUKAJ_PUNKTU[miejsce];
	else
		$self.="?SZUKAJ_PUNKTU[nazwa]=".$SZUKAJ_PUNKTU[nazwa]."&SZUKAJ_PUNKTU[miejsce]=".$SZUKAJ_PUNKTU[miejsce];

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
			$tylko_dla = str_replace("warmiёskie","warmiёsko-mazurskie",$tylko_dla);
			$tylko_dla = str_replace("wielkopolska","wielkopolskie",$tylko_dla);
			$addinfo = "Wojewѓdztwo: $tylko_dla.";
		}
		echo "Brak punktѓw sprzedaПy speГniajБcych podane kryteria. $addinfo";
		return;
	}

	$pic = "<img valign=\"absmiddle\" title=\"Punkt sprzedaПy posiada okna ekspozycyjne\" alt=\"Sklep posiada okno ekspozycyjne\" src=\"$UIMAGES/sb/check.gif\">";

	//$pic - Punkt sprzeda\xbfy posiada okna ekspozycyjne<br><br>

	echo "
	$navi<br><br>
	<TABLE class=\"tl\" cellspacing=\"0\" cellpadding=\"3\" width=\"100%\">
	<tbody>";
	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		parse_str(ado_explodename($res,$i));	
		echo "
		<TR class=\"".(($i && ($i%2))?"even":"odd")."\">
			<TD class=\"name\" valign=\"top\">".stripslashes($ps_nazwa)."</TD>
			<TD valign=\"top\">$ps_kod $ps_miasto<br>$ps_adres</TD>
			<TD valign=\"top\" style=\"font-weight:bold\">$ps_kontakt</TD>
		</TR>
		";

	}
	echo "</tbody></table>";
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
