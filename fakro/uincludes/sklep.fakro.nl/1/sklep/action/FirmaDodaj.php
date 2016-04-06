<?
	$action = "";

	$FORM[su_nip]=ereg_replace("[^0-9]","",$FORM[su_nip]);
	
	$id=explode(":",$FORM[su_id]);

	$inserts="su_server,su_data_dodania";
	$values="$SERVER_ID,$NOW";
	
	include_once ("$SKLEP_INCLUDE_PATH/autoryzacja/firmy_fields.h");
	$suf=$id[3];
	$suf2=$id[4];

	$action_id=$id[0];

	for ($ofi=0;$ofi<count($osoby_fields);$ofi++)
	{
		$p=($ofi<30)?pow(2,$ofi):pow(2,$ofi-30);
		$v=($ofi<30)?$suf:$suf2;
		if (! ($p&$v)) continue;

		$of=$osoby_fields[$ofi];
		$pole=$of[1];

		if ($pole=="su_pass" && !strlen($FORM[su_pass])) continue;

		$inserts.=",$pole";
		$values.=",";
		if ($of[4]=="d")
		{
			$values.=unixdate($FORM[$pole]);
		}
		else
		{
			if (strstr($pole,"_id_"))
			{
				if (!$FORM[$pole]) $FORM[$pole]="NULL";
				$values.=$FORM[$pole];	
			}
			else
				$values.="'".substr($FORM[$pole],0,$of[3])."'";
		}
		
	}

	$su_id=0;
	$SU_ID="max(su_id)";

	if (!$su_id)
	{
		$inserts.= ",su_parent";
		$values.= ",NULL";
		$add_sql = "";
		if (is_array($upraw))
			while (list($key,$val) = each($upraw))
				if (strlen($key) && $val)
					$add_sql.= "INSERT INTO system_acl_grupa (sag_grupa_id,sag_user_id,sag_server) 
								SELECT $key,max(su_id),$SERVER_ID FROM system_user;";

		$query = "INSERT INTO system_user ($inserts) VALUES ($values); $add_sql
				  INSERT INTO kontrahent_sklep (ks_sk_id,ks_su_id)
					SELECT sk_id,max(su_id) FROM sklep,system_user GROUP BY sk_id;
				 SELECT MAX(su_id) AS maxid FROM system_user;
				 
				 ";
	}
	else
	{
		$query = "	INSERT INTO system_acl_grupa (sag_grupa_id,sag_user_id,sag_server) 
				VALUES ($id[1],$su_id,$SERVER_ID)";
		$SU_ID=$su_id;
		$maxid = $su_id;
		
	}
//	$projdb->debug=1;
	parse_str(query2url($query));
	$action_id=$maxid;
//	echo nl2br($query);
//	$projdb->debug=0;

?>
