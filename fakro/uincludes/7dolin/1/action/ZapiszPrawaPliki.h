<?
	if (!strlen($FORM[sg_id])) return;
	
	$sql = "DELETE FROM system_acl_obiekt WHERE sao_server = $SERVER_ID AND sao_grupa_id = ".$FORM[sg_id]."
			AND sao_klucz LIKE 'a_%'";

	if (is_array($PLIKI))
		while(list($key,$val)=each($PLIKI))
			if ($val)
			{
				$sql.= ";INSERT INTO system_acl_obiekt (sao_grupa_id,sao_server,sao_klucz)
						VALUES (".$FORM[sg_id].",$SERVER_ID,'$key')";
			}

	$projdb->debug=0;
	$projdb->execute($sql);
	$projdb->debug=0;

?>