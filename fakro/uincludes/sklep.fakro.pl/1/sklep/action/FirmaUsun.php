<?

	$id=explode(":",$LIST[id]);

	$id[0]+=0;
	$id[1]+=0;
	
	$query="DELETE FROM zamowienia WHERE za_su_id=$id[0] AND za_status=-5;
			DELETE FROM system_action_log WHERE sal_user_id IN (SELECT su_id FROM system_user WHERE su_parent=$id[0]);
			DELETE FROM system_acl_grupa WHERE sag_user_id=$id[0];
			DELETE FROM system_user WHERE su_id=$id[0]";

	//echo $query;

	$LIST[id]="";
	//$projdb->debug=1;
	if (!$projdb->Execute($query)) $error=$dberror;
	//$projdb->debug=0;
	$action_id=$id[0];
?>