<?
	include_once("$SKLEP_INCLUDE_PATH/admin/ws_fun.php");

	$sql="SELECT * FROM towar WHERE to_id=".$LIST[to_id];
	$str2parse=ado_query2url($sql);
	$towar=urlEncodedStr2arr($str2parse);
	
	$action_id=$LIST[to_id];

	$sql="SELECT * FROM towar_sklep WHERE ts_sk_id=$SKLEP_ID AND ts_to_id=".$LIST[to_id];
	$str2parse=ado_query2url($sql);
	$towar[towar_sklep]=urlEncodedStr2arr($str2parse);


	$ws=$WM->ws_action_pre("$SOAP_PATH/$action.h",$param_str,&$operation,$output);
	if ($ws)
	{
		eval($param_str);
		$towar="";
		$error=$WM->ws_action($ws,$operation,$params,$output,$res_str);
		if (!strlen($error)) eval($res_str);
	}

	if (strlen($error)) return;

	$ts_id=0;
	$sql="SELECT ts_id FROM towar_sklep WHERE ts_sk_id=$SKLEP_ID AND ts_to_id=".$LIST[to_id];
	parse_str(ado_query2url($sql));	

	if (!is_array($towar))
	{
		if ($ts_id)
		{
			$sql="UPDATE towar_sklep SET ts_aktywny=0 WHERE ts_id=$ts_id";
			$projdb->execute($sql);
		}

		return;
	}

	$towar[towar_sklep][ts_id]=$ts_id;
	$towar[towar_sklep][ts_aktywny]=1;
	$towar[to_ws_update]=$NOW;
	$WM->update_towar($LIST[to_id],$towar);
?>
