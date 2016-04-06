<?
	if ($i>=$res->RecordCount()) 
	{
		$template_loop=0;
		return;
	}
	parse_str(ado_explodename($res,$i));
	$i++;


	$_tr = " class=t1";
	if (($i)%2) $_tr = " class=t2";

	$tcount=$zp_ilosc;
	$cena = $zp_cena;
	$wart = $cena*$tcount;

	$wart_ws=$zp_cena_ws*$zp_ilosc_ws;

	$wymiary=$WM->towar_wymiary($to_id);
	if (!strlen($wymiary)) $wymiary="&nbsp;";

	$lp++;

	include("$SKLEP_INCLUDE_PATH/templates/towar_foto.php");
	if ($to_pr_id)
	{
		include("$SKLEP_INCLUDE_PATH/templates/towar_producent.php");
	}

	eval("\$to_opis_m=\$to_opis_m_$lang;");
	eval("\$to_opis_d=\$to_opis_d_$lang;");

	$total_quant+= $zp_ilosc;
	$total_value+= ($cena*$zp_ilosc);
	$total_value_ws+= $wart_ws;

	$cena_o=$cena;

	if ($zp_rabat) 
	{
		$cena_o=$cena/(1- $zp_rabat/100);
		$zp_rabat=round(($zp_rabat*100)/100,2)."%";
	}

	$total_value_o+= ($cena_o*$zp_ilosc);

	include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");

?>
