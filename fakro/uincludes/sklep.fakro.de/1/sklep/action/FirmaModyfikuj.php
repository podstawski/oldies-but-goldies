<?

	$id=explode(":",$FORM[su_id]);


	$id[0]+=0;
	$id[1]+=0;
	$FORM[su_nip]=ereg_replace("[^0-9]","",$FORM[su_nip]);
	
	$query="UPDATE system_user SET su_data_modyfikacji=$NOW";
	include_once ("$SKLEP_INCLUDE_PATH/autoryzacja/firmy_fields.h");
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
		elseif ($of[4]=="f")
		{
			$query.=toFloat($FORM[$pole]);
		}
		else
		{
			if ($pole=="su_opiekun")
			{
				if (!$FORM[$pole]) $FORM[$pole]="NULL";
				$query.=$FORM[$pole];	
			}
			else
			$query.="'".substr($FORM[$pole],0,$of[3])."'";
		}
		
	}
	$query.=" WHERE su_id=$id[0]";

	$action_id=$id[0];


	$query.=";".$add_sql;

	if (!$projdb->Execute($query)) $error=$dberror;

?>
