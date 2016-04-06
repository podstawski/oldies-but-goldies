<?
	$article_id = $FORM[article_id];
	$clear_cart = $FORM[clear_cart];

	if (!strlen($article_id) && !strlen($clear_cart)) return;
	if ($AUTH[id] <= 0 || !strlen($AUTH[id])) return;

	if ($clear_cart)
	{
		$sql = "DELETE FROM koszyk WHERE ko_su_id = ".$AUTH[id];
		$adodb->execute($sql);
		return;
	}
	$action_id=$article_id;

	//SUKAMY UKRYTYCH TOWARгW W PROMOCJACH POWIЅZANYCH Z DODAWANYM
	$adodb->debug=0;
	$sql = "SELECT ts_to_id,ko_ilosc AS old_ilosc FROM koszyk LEFT JOIN towar_sklep ON  ko_ts_id = ts_id
			WHERE ko_id = $article_id 
			AND ko_su_id = ".$AUTH[id];

	parse_str(ado_query2url($sql));
	$powiazane = $WM->towary_powiazane($ts_to_id);
	if (strlen($powiazane))
	{
		$powiazane = explode(",",$powiazane);
		for ($i=0; $i < count($powiazane); $i++)
		{
			$sql = "SELECT ko_id,ko_ilosc FROM towar_sklep LEFT JOIN koszyk ON ko_ts_id = ts_id
					WHERE ts_to_id = ".$powiazane[$i]."
					AND ko_su_id = ".$AUTH[id];
			parse_str(ado_query2url($sql));

			if (!strlen($ko_id)) continue;

			$new_ilosc=$ko_ilosc-$old_ilosc;

			$sql = "UPDATE koszyk SET ko_ilosc=$new_ilosc WHERE ko_id = $ko_id; DELETE FROM koszyk WHERE ko_ilosc<=0";
			$adodb->execute($sql);
				
		}
	}
	$adodb->debug=0;
	//ENDOF SZUKAMY UKRYTYCH TOWARгW W PROMOCJACH POWIЅZANYCH Z DODAWANYM

	$sql = "DELETE FROM koszyk WHERE ko_su_id = ".$AUTH[id]." AND ko_id = $article_id";
	$adodb->execute($sql);

?>
