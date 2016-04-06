<?
	$killid = $FORM[killid];

	if (strlen($killid))
	{
		$sql = "DELETE FROM towar WHERE to_id = $killid";
		if (!$projdb->execute($sql))
		{
			$sql = "UPDATE towar_sklep 
					SET ts_aktywny = 0
					WHERE ts_sk_id = $SKLEP_ID
					AND ts_to_id = $killid";
			$projdb->execute($sql);
		}

	}

?>
