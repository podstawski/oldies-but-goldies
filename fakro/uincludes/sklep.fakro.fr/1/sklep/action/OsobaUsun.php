<?

	$id=explode(":",$LIST[id]);

	$id[0]+=0;
	$id[1]+=0;

	$query="DELETE FROM system_acl_grupa WHERE sag_user_id=$id[0];
			DELETE FROM system_user WHERE su_id=$id[0]";

	$LIST[id]="";
	if (!$projdb->Execute($query)) $error=$dberror;
	$action_id=$id[0];
?>
