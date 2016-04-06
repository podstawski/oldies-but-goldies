<?
	$sql="SELECT zp_za_id FROM zampoz WHERE zp_id=$FORM[zp_id]";
	parse_str(ado_query2url($sql));

	$wart=toFloat($FORM[wart]);
	$query="UPDATE zampoz SET $FORM[zp]=$wart WHERE zp_id=$FORM[zp_id];
			SELECT sum(zp_cena*zp_ilosc) AS za_wart_nt, sum(zp_cena*zp_ilosc*(1+to_vat/100)) AS za_wart_br
			FROM zampoz,towar_sklep,towar
			WHERE zp_za_id=$zp_za_id AND zp_ts_id=ts_id AND ts_to_id=to_id;";
	parse_str(ado_query2url($query));

	$query.="UPDATE zamowienia SET za_wart_nt=$za_wart_nt, za_wart_br=$za_wart_br
			WHERE za_id=$zp_za_id";
	parse_str(ado_query2url($query));
?>
