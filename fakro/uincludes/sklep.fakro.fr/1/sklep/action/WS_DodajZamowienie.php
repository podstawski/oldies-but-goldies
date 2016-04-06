<?
	include_once("$SKLEP_INCLUDE_PATH/admin/ws_fun.php");

	$za_ws="";
	if (!$LIST[za_id]) return;
	$sql="SELECT * FROM zamowienia WHERE za_id=".$LIST[za_id];
	$str2parse=ado_query2url($sql);
	parse_str($str2parse);

	if (strlen($za_ws)) return;

	$zamowienie=array_merge(urlEncodedStr2arr($str2parse),urlEncodedStr2arr($za_parametry));

	$zampoz=array();
	$query="SELECT zampoz.* FROM zampoz,towar_sklep WHERE zp_za_id=".$LIST[za_id]." AND zp_ts_id=ts_id
			ORDER BY ts_magazyn,zp_to_indeks";
	$res=$projdb->execute($query);
	for ($i=0;$i<$res->RecordCount();$i++)
	{
		$str2parse=ado_ExplodeName($res,$i);
		$zampoz[$i]=urlEncodedStr2arr($str2parse);
		parse_str($str2parse);
		$query="SELECT * FROM towar_sklep WHERE ts_id=$zp_ts_id";
		$str2parse=ado_query2url($query);
		parse_str($str2parse);
		$zampoz[$i][towar_sklep]=urlEncodedStr2arr($str2parse);
		$query="SELECT to_indeks,to_nazwa,to_jm,to_jp,to_ws,to_cena, to_vat 
				FROM towar WHERE to_id=$ts_to_id";
		$str2parse=ado_query2url($query);
		$zampoz[$i][towar]=urlEncodedStr2arr($str2parse);

	}
	$zamowienie[zampoz]=$zampoz;

	$sql="SELECT * FROM system_user WHERE su_id=".$za_su_id;
	$str2parse=ado_query2url($sql);
	$zamowienie[system_user]=urlEncodedStr2arr($str2parse);

	
	//$ws_debug=1;
	//if ($ws_debug) echo "<pre>"; print_r($zamowienie); echo "</pre>";


	$za_ws="";
	
	$ws_action="$SOAP_PATH/$action.h";
	include("$SKLEP_INCLUDE_PATH/admin/ws_action.php");

	if ($ws_debug) $za_ws="";

	if (strlen($za_ws))
	{
		if ($AUTH[id]<=0) $AUTH[id]=$SYSTEM[master];
		$FORM[accept_id]=$LIST[za_id];
		$FORM[acc_status]=0;
		$action_id=$LIST[za_id];
		$FORM[kom]=$za_uwagi_przyjecia;

		$query="UPDATE zamowienia SET za_ws_update=$NOW,za_ws='$za_ws' WHERE za_id=$action_id";
		//echo $query;
		$projdb->execute($query);
		$action="ZamowienieUp";
	}
?>
