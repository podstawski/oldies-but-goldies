<?
	global $nazwa_grupy, $grupa_id, $PRAWA,$PRIV;
	$action="";
//	echo "G: ======== ".$grupa_id;
//	echo "P: ".print_r($PRAWA);

	$sg_admin = 0;

	if (is_array($PRIV))
	{
		reset($PRIV);
		while (list($key,$val) = each($PRIV))
			if ($val) $sg_admin+=$val;
	}

	if (strlen($grupa_id))
	{
		$sql = "UPDATE system_grupa SET
				sg_nazwa = '$nazwa_grupy',
				sg_admin = $sg_admin
				WHERE sg_id = $grupa_id";
		pg_exec($db,$sql);

		$sql = "DELETE FROM system_acl_obiekt 
				WHERE sao_grupa_id = $grupa_id
				AND sao_server = $SERVER_ID";
		pg_exec($db,$sql);

	}
	else
	{
		$sql = "INSERT INTO system_grupa
				(sg_nazwa,sg_server,sg_admin) VALUES ('$nazwa_grupy',$SERVER_ID,$sg_admin)";
		pg_exec($db,$sql);
		$sql = "SELECT MAX(sg_id) AS maxid FROM system_grupa
				WHERE sg_server = $SERVER_ID";
		parse_str(query2url($sql));
	}
	
	if (!strlen($grupa_id))
		$grupa_id = $maxid;

	if (strlen($grupa_id))
	{
		$sql = "DELETE FROM system_acl_obiekt 
			WHERE sao_grupa_id = $grupa_id
			AND sao_server = $SERVER_ID";
		pg_exec($db,$sql);
	}

	if (is_array($PRAWA) && strlen($grupa_id))
	{
		while(list($key,$val) = each($PRAWA))
			if ($val && strlen($key))
			{
				$sql = "INSERT INTO system_acl_obiekt 
						(sao_server,sao_grupa_id,sao_klucz)
						VALUES
						($SERVER_ID,$grupa_id,'$key')";
				pg_exec($db,$sql);
			}
	}

?>
