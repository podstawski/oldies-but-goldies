<?
	include_once("$SKLEP_INCLUDE_PATH/admin/ws_fun.php");

	$sql="SELECT * FROM system_user WHERE su_id=".$LIST[su_id];

	$str2parse=ado_query2url($sql);
	$system_user=urlEncodedStr2arr($str2parse);
	
	$action_id=$LIST[su_id];

	//$ws_debug=1;

	$ws_action_clear=array("system_user");
	$ws_action="$SOAP_PATH/$action.h";
	include("$SKLEP_INCLUDE_PATH/admin/ws_action.php");

	if (strlen($error)) return;

	if (!is_array($system_user)) return;
	$system_user[su_ws_update]=$NOW;
	$floats = array("su_saldo","su_ws_update");
	$index = array("su_id"=>$action_id);
	$noup = array("su_parent");

	//if ($ws_debug) return;

	$WM->update_table("system_user",$index,$system_user,$floats,$noup);

	if (is_array($system_user[adresy]))
	{
		$adresy = $system_user[adresy];
		$sql = "DELETE FROM adresy WHERE ad_su_id = ".$LIST[su_id];
		$projdb->execute($sql);
		for ($i = 0; $i < count($adresy); $i++)
		{
			$adr = $adresy[$i];
			if (strlen($adr[ad_ws]))
			{
				$sql = "INSERT INTO adresy (ad_ws,ad_adres,ad_su_id) 
						VALUES ('".$adr[ad_ws]."','".$adr[ad_adres]."',$LIST[su_id])";
				$projdb->execute($sql);
			}
		}
	}
?>
