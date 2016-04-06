<?
	$id=explode(":",$FORM[su_id]);

	$id[0]+=0;
	$id[1]+=0;
//	if (!$id[1]) return;
/*
	$query="SELECT *,oid AS sg_oid FROM system_grupa WHERE sg_id=$id[1]";
	parse_str(ado_query2url($query));

	if ($id[2]!=crypt($sg_oid,$id[2])) $error="Brak praw";
	if (strlen($error)) return;



	$sag_id=0;
	$sql="SELECT sag_id FROM system_acl_grupa 
		WHERE sag_server=$SERVER_ID AND sag_grupa_id=$sg_id AND sag_user_id=$id[0]";
	parse_str(ado_query2url($sql));
	if (!$sag_id) $error="Brak praw";
	if (strlen($error)) return;
*/


	$FORM[su_nip]=ereg_replace("[^0-9]","",$FORM[su_nip]);
	
	$FORM[su_email] = strtolower($FORM[su_email]);

	if (strlen($FORM[su_login]))
	{
		$FORM[su_login]=strtolower($FORM[su_login]);	
		$sql="SELECT count(*) AS c FROM system_user WHERE su_login='$FORM[su_login]' AND su_id<>$id[0] AND su_parent IS NOT NULL";
		parse_str(ado_query2url($sql));
		if ($c) $error="Nazwa uПytkownika juП istnieje";
	}

	if (strlen($FORM[su_email]))
	{
		$sql="SELECT count(*) AS c FROM system_user WHERE su_email='$FORM[su_email]' AND su_id<>$id[0] AND su_parent IS NOT NULL";
		parse_str(ado_query2url($sql));
		if ($c) $error="Podany email juП istnieje";
	}

	if (strlen($error)) return;

	if (strlen($FORM[su_pesel]))
	{
		$sql="SELECT count(*) AS c FROM system_user WHERE su_pesel='$FORM[su_pesel]' AND su_id<>$id[0] AND su_parent IS NOT NULL";
		parse_str(ado_query2url($sql));
		if ($c) $error="Nr PESEL juП istnieje";
	}
	if (strlen($error)) return;

	
	$action_id=$id[0];

	$query="UPDATE system_user SET su_data_modyfikacji=$NOW";
	include_once ("$SKLEP_INCLUDE_PATH/autoryzacja/osoby_fields.h");
	$_suf=$id[3];
	$_suf2=$id[4];
	
	for ($ofi=0;$ofi<count($osoby_fields);$ofi++)
	{
		$p=($ofi<30)?pow(2,$ofi):pow(2,$ofi-30);
		$v=($ofi<30)?$_suf:$_suf2;
		if (! ($p&$v)) continue;

		$of=$osoby_fields[$ofi];
		$pole=$of[1];

		if ($pole=="su_pass" && !strlen($FORM[su_pass])) continue;

		$query.=",$pole=";

		if ($of[4]=="d")
		{
			$query.=unixdate($FORM[$pole]);
		}
		else
		{
			if (strstr($pole,"_id_"))
			{
				if (!$FORM[$pole]) $FORM[$pole]="NULL";
				$query.=$FORM[$pole];	
			}
			else
			$query.="'".substr($FORM[$pole],0,$of[3])."'";
		}
		
	}
	$query.=" WHERE su_id=$id[0]";


//	$projdb->debug=1;
	if (!$projdb->Execute($query)) $error=$dberror;
//	$projdb->debug=0;
	
?>
