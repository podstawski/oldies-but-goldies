<?
	if ($i>=$res->RecordCount())
	{
		$template_loop=0;
		return;
	}
	parse_str(ado_explodename($res,$i));
	$i++;

	$to_id=$TO_ID;
	$sql = "SELECT * FROM towar WHERE
			to_id IN ($gt_to_id1,$gt_to_id2) AND to_id<>$to_id";
	parse_str(ado_query2url($sql));

	echo "<br>$sql";
	
	$cena = $WM->system_cena($to_id);
	$sysmsg_cena = sysmsg("Price","article");

	$lp=$i;

	include("$SKLEP_INCLUDE_PATH/templates/towar_foto.php");
	if ($to_pr_id)
	{
		include("$SKLEP_INCLUDE_PATH/templates/towar_producent.php");
	}

	eval("\$to_opis_m=\$to_opis_m_$lang;");
	eval("\$to_opis_d=\$to_opis_d_$lang;");

	include("$SKLEP_INCLUDE_PATH/templates/towar_ceny.php");
?>
