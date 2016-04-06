<?
	if (!strlen($FORM[id])) return;

	if (!strlen($FORM[to_indeks])) $error="Brak indeksu";

	$sql="SELECT count(*) AS c FROM towar WHERE to_indeks='$FORM[to_indeks]' AND to_id<>$FORM[id]";
	parse_str(ado_query2url($sql));

	if ($c) $error="Indeks nie moПe siъ powtarzaц";

	if (strlen($error)) return;

	$sql="SELECT count(*) AS c FROM towar_parametry WHERE tp_to_id =".$FORM[id];
	parse_str(ado_query2url($sql));

	$sql="";
	if (!$c) $sql="INSERT INTO towar_parametry (tp_to_id) VALUES ($FORM[id]);\n";
	
	$FORM[to_cena] = toFloat($FORM[to_cena]);
	$FORM[to_vat] = toFloat($FORM[to_vat]);

	$sql.="UPDATE towar SET
			to_indeks = '$FORM[to_indeks]',
			to_nazwa = '$FORM[to_nazwa]',
			to_jm = '$FORM[to_jm]',
			to_klucze='$FORM[to_klucze]',
			to_foto_m = '$FORM[to_foto_m]',
			to_foto_s = '$FORM[to_foto_s]',
			to_foto_d = '$FORM[to_foto_d]',
			to_jp = '$FORM[to_jp]',
			to_ean = '$FORM[to_ean]',
			to_cena = $FORM[to_cena],
			to_vat = $FORM[to_vat],
			to_opis_m_$lang = '".$FORM["to_opis_m_$lang"]."'
			WHERE to_id = ".$FORM[id].";";

	$adodb->execute($sql);

	$action_id=$FORM[id];
?>
