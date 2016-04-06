<?
	if (!strlen($FORM[id])) return;
	$add_sql = "DELETE FROM system_acl_grupa WHERE sag_user_id = ".$FORM[id];
	if (is_array($upraw))
		while (list($key,$val) = each($upraw))
			if (strlen($key) && $val)
				$add_sql.= ";INSERT INTO system_acl_grupa (sag_grupa_id,sag_user_id,sag_server)
							VALUES ($key,".$FORM[id].",$SERVER_ID)";
	
	$projdb->execute($add_sql);
	$action_id = $FORM[id];

?>
