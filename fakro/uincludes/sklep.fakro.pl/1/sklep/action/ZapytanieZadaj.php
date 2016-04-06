<?
	$osoba="NULL";
	if ($AUTH[id]>0) $osoba=$AUTH[id];

	if (!$FORM[za_ts_id] && $FORM[to_id]) 
	{
		$query="SELECT ts_id FROM towar_sklep WHERE ts_sk_id=$SKLEP_ID AND ts_to_id=".$FORM[to_id];
		parse_str(ado_query2url($query));
		$FORM[za_ts_id]=$ts_id;
	}
	if (!$FORM[za_ts_id]) return;
	if (!strlen($FORM[za_pyt])) return;

	$action_id=0;

	$FORM[za_cena] = ereg_replace("[^0-9]","",$FORM[za_cena]);
	if (!strlen($FORM[za_cena]))
		$FORM[za_cena] = "NULL";

	$adodb->debug=0;
	$query="INSERT INTO zapytania (za_ts_id,za_pyt_su_id,za_odp_su_id,
									za_email,za_telefon,za_pyt,za_pyt_data,za_cena)
			VALUES ($FORM[za_ts_id],$osoba,$SYSTEM[master],
					'$FORM[za_email]','$FORM[za_telefon]','$FORM[za_pyt]',$NOW,$FORM[za_cena]);
			SELECT max(za_id) AS action_id FROM zapytania";

	parse_str(ado_query2url($query));
	$adodb->debug=0;
	if ($action_id) $WM->sysinfo=sysmsg("Your query was sent to the proper person","crm");

?>
