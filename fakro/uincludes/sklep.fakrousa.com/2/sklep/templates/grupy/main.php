<?
	$to_id=$LIST[to_id];
	
	if (!$to_id)
	{
		$error="&nbsp;";
		return;
	}

        $sql = "SELECT * FROM grupy_towarow
                        WHERE $to_id IN (gt_to_id1, gt_to_id2)
                        AND gt_grupa = '$_grupa'";

	$res=$projdb->Execute($sql);

	echo "Rekordy: ".$res->RecordCount();
	if (!$res->RecordCount())
	{
		$error="&nbsp;";
		return;
	}

	$sysmsg_grupa=sysmsg("$_grupa","system");
	$TO_ID=$to_id;
	$i=0;
?>
