<?

$sql="SELECT $FORM[sort_f] AS p_1 FROM towar_sklep WHERE ts_id=$FORM[ts_1]";
parse_str(ado_query2url($sql));
$sql="SELECT $FORM[sort_f] AS p_2 FROM towar_sklep WHERE ts_id=$FORM[ts_2]";
parse_str(ado_query2url($sql));

$query="UPDATE towar_sklep SET $FORM[sort_f]=$p_2 WHERE ts_id=$FORM[ts_1];
		UPDATE towar_sklep SET $FORM[sort_f]=$p_1 WHERE ts_id=$FORM[ts_2];";

$adodb->execute($query);

?>
