<?
	include_once("$SKLEP_INCLUDE_PATH/admin/ws_fun.php");

	$za_ws="";
	if (!$LIST[za_id]) return;
	$sql="SELECT * FROM zamowienia WHERE za_id=".$LIST[za_id];
	$str2parse=ado_query2url($sql);
	parse_str($str2parse);

	if (!strlen($za_ws)) return;


	$zamowienie="";

	//$ws_debug=1;
	$ws_action="$SOAP_PATH/$action.h";
	include("$SKLEP_INCLUDE_PATH/admin/ws_action.php");

	$status_changed=0;
	if (!is_array($zamowienie)) return;
	$status_changed=1;

	$action_id=$LIST[za_id];
	if ($AUTH[id]<=0) $AUTH[id]=$SYSTEM[master];
	$FORM[accept_id]=$LIST[za_id];
	$FORM[acc_status]=1;
	$FORM[kom]=$zamowienie[za_uwagi_realizacji];

	
	$WM->update_table("zamowienia",array("za_id"=>$action_id),$zamowienie,array("za_wart_nt","za_wart_br"));


	if (is_array($zamowienie[zampoz])) foreach ($zamowienie[zampoz] AS $zp)
	{
		if (!$zp[zp_id] && strlen($zp[zp_to_indeks]))
		{
			$zp_id=0;
			$query="SELECT zp_id FROM zampoz 
				WHERE zp_za_id=$action_id 
				AND zp_to_indeks='$zp[zp_to_indeks]'";
			parse_str(ado_query2url($query));
			if (!$zp_id)
			{
				$zp[zp_cena_ws]+=0;
				$sql="INSERT INTO zampoz
					(zp_za_id,zp_to_indeks,zp_ts_id,
					 zp_ilosc,zp_cena)
					SELECT $action_id,to_indeks,ts_id,
					 0,$zp[zp_cena_ws]
					FROM towar,towar_sklep
					WHERE to_indeks='$zp[zp_to_indeks]'
					AND ts_to_id=to_id AND ts_sk_id=$SKLEP_ID;
					$query";
				parse_str(ado_query2url($sql));
			}

			$zp[zp_id]=$zp_id;
		}
		if ($zp[zp_id]) $WM->update_table("zampoz",array("zp_id"=>$zp[zp_id]),$zp,
							array("zp_cena_ws","zp_ilosc_ws"));

	}

	if (!strlen($zamowienie[za_status])) $action="ZamowienieUp";
	

?>
