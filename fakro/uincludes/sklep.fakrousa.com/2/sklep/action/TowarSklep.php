<?
	$LIST[id]=$LIST[to_id];
	$action_id=$LIST[to_id];
	$ts_id=0;

	$query="SELECT ts_id,ts_aktywny FROM towar_sklep 
		WHERE ts_to_id=$LIST[to_id] AND ts_sk_id=$LIST[sk_id]";

	parse_str(ado_query2url($query));

	if ($ts_id)
	{
		$tsa=$ts_aktywny?0:1;
		$query="UPDATE towar_sklep SET ts_aktywny=$tsa WHERE ts_id=$ts_id";
	}
	else
	{
		$query="INSERT INTO towar_sklep (ts_to_id,ts_sk_id,ts_pri,ts_pri2)
			VALUES ($LIST[to_id],$LIST[sk_id],ts_pri_seq(),ts_pri2_seq())";
	}	

	$projdb->Execute($query);

?>
