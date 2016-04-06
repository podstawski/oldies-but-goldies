<?

	global $SZUKAJ_PUNKTU, $list;

	$addinfo='';


	$LIST = $list;

	$szukaj=$SZUKAJ_PUNKTU;

	if (!$size) $size=25;

	parse_str($costxt);

	if (!is_array($szukaj) && !strlen($tylko_dla)) return;

	if (strlen($tylko_dla))
		$cond.= " AND ps_wojewodztwo = '$tylko_dla'";

	$n=addslashes(stripslashes($szukaj[nazwa]));
	
	if (strlen($szukaj[nazwa]))
		$cond.= " AND (ps_nazwa ~* '$n' OR d_imie  ~* '$n' OR d_nazwisko ~* '$n')";

	if (strlen($szukaj[miejsce]))
		$cond.= " AND ps_miasto ~* '".addslashes(stripslashes($szukaj[miejsce]))."'";

//	$fakrodb->debug=1;
	$sql = "SELECT * FROM punkty_sprzedazy WHERE ps_typ='D' $cond ORDER BY ps_nazwa";
	$res = $fakrodb->execute($sql);

	if (!$LIST[ile])
	{
		$query="SELECT count(ps_id) AS c FROM punkty_sprzedazy WHERE ps_typ='D' $cond ";
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
			$tylko_dla = str_replace("warmiñskie","warmiñsko-mazurskie",$tylko_dla);
			$tylko_dla = str_replace("wielkopolska","wielkopolskie",$tylko_dla);
			$addinfo = "Województwo: $tylko_dla.";
		}
		
		$addinfo = "Brak dekarzy spe³niaj±cych podane kryteria. $addinfo";
		return;
	}

	

	$wyniki[0][navi]=$navi;
	$wyniki[0][addinfo]=$addinfo;

	for ($i=0; $i < $res->RecordCount(); $i++)
	{
		$url=ado_explodename($res,$i);
		parse_str($url);	
		$row=array();
		foreach(explode('&',$url) AS $para)
		{
			$p=explode('=',$para);
			$row[$p[0]]=urldecode($p[1]);
		}
		$row[parity]=($i%2)?"odd":"even";
		$row[lpp]=$i+1;
		$row[lp]=$i+1+$LIST[start];
		$wyniki[0][dekarze][$i]=$row;


	}

?>
