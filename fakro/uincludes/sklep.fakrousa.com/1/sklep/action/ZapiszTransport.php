<?
	$transport = $_REQUEST[TRANSPORT];

	if (strlen($transport[id]))
	{
		$sql = "UPDATE tr_typ SET
				tr_typ_name = '".$transport[nazwa]."'
				WHERE tr_typ_id = ".$transport[id];
	}
	else
	{
		$sql = "INSERT INTO tr_typ (
				tr_typ_name
				) VALUES (
				'".$transport[nazwa]."'
				)";
	}

	$adodb->execute($sql);
?>
