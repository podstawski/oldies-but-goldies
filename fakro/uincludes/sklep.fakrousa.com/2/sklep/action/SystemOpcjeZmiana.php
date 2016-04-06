<?
	$sql="SELECT * FROM system_opcje ORDER BY so_nazwa2";
	$result = $projdb->Execute($sql);
	$query="";
	for ($i=0;$i<$result->RecordCount();$i++)
	{
		parse_str(ado_ExplodeName($result,$i));

		$query.="UPDATE system_opcje SET so_wart='$FORM[$so_id]' WHERE so_id=$so_id;";
	}

	$projdb->execute($query);

	$SKLEP_SESSION["SYSTEM"]="";

?>
