<?
	$action = "";

	$FORM[su_nip]=ereg_replace("[^0-9]","",$FORM[su_nip]);

	$FORM[su_login]=trim($FORM[su_login]);
	$FORM[su_pass]=trim($FORM[su_pass]);
	
	$FORM[su_email] = strtolower($FORM[su_email]);

	if ( (!strlen($FORM[su_login]) && !strlen($FORM[su_email])) || !strlen($FORM[su_pass]) )
	{
		$error="Należy podać kod użytkownika i hasło";
	}
	

	if (strlen($FORM[su_login]))
	{
		$FORM[su_login]=strtolower($FORM[su_login]);	
		$sql="SELECT count(*) AS c FROM system_user WHERE su_login='$FORM[su_login]'";
		parse_str(ado_query2url($sql));
		if ($c) $error="Nazwa użytkownika już istnieje";
	}

	if (strlen($FORM[su_email]))
	{
		$sql="SELECT count(*) AS c FROM system_user WHERE su_email='$FORM[su_email]'";
		parse_str(ado_query2url($sql));
		if ($c) $error="Podany email już istnieje";
	}

	if (strlen($error)) return;

	$id=explode(":",$FORM[su_id]);
	$action_id = $id;

	$inserts="su_server,su_data_dodania";
	$values="$SERVER_ID,$NOW";

	include_once ("$SKLEP_INCLUDE_PATH/autoryzacja/osoby_fields.h");
	$suf=$id[3];
	$suf2=$id[4];


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

	if (strlen($FORM[su_pesel]))
	{
		
		$sql="SELECT su_id FROM system_user WHERE su_pesel='$FORM[su_pesel]' LIMIT 1";
		parse_str(ado_query2url($sql));
	}
	
	if (!$su_id)
	{
		$inserts.= ",su_parent";
		$values.= ",".$FORM["parent_id"];

		$query = "INSERT INTO system_user ($inserts) VALUES ($values);";
	}
	else
	{
		$query = "	INSERT INTO system_acl_grupa (sag_grupa_id,sag_user_id,sag_server) 
				VALUES ($id[1],$su_id,$SERVER_ID)";
		$SU_ID=$su_id;
		
	}
//	$projdb->debug=1;
	if (!$projdb->Execute($query)) $error=$dberror;
//	echo nl2br($query);
//	$projdb->debug=0;

?>
