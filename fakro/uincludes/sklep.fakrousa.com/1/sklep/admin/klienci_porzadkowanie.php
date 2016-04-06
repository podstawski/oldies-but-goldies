<?
		
	$query="SELECT su_id,su_nazwisko,su_login FROM system_user WHERE su_parent IS NULL 
			AND (su_nazwisko='' OR su_login='' OR su_login IS NULL OR su_nazwisko IS NULL OR length(su_login)<5)
			";

	$result = $projdb->Execute($query);	


	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));

		$set=array();

		if (!strlen($su_nazwisko))
		{
			$sql="SELECT su_nazwisko,su_imiona FROM system_user WHERE su_parent=$su_id LIMIT 1";
			parse_str(ado_query2url($sql));

			$set[su_nazwisko]="$su_nazwisko $su_imiona";
		}

		if (strlen($su_login)<5)
		{
			$su_login=strtolower(str_replace(' ','',unpolish($su_nazwisko))).$su_id;
			$set[su_login]=$su_login;
		}

		if (count($set))
		{
			$query='';
			foreach ($set AS $key=>$val)
			{
				if (strlen($query)) $query.=',';
				$query.="$key='$val'";
			}
			$query="UPDATE system_user SET $query WHERE su_id=$su_id";
			$projdb->Execute($query);	
		}
	}
?>