<?
	global $MODULES,$KAMELEON;

	$file=crm_file($page);
	if (!strlen($file)) return;

	$id=0+obj_id_on_page($page);

	$warunek="cr_server=$SERVER_ID AND cr_username='$KAMELEON[username]'
		 AND cr_file_id='$file' AND cr_id=$id";

	$query="SELECT count(*) AS c FROM crm_recent
		 WHERE $warunek";
	parse_str(ado_Query2url($query));
	
	if (!$c && $id) 
	{
		$query="INSERT INTO crm_recent (cr_server,cr_username,cr_file_id,cr_id)
			 VALUES ($SERVER_ID,'$KAMELEON[username]','$file',$id)";

		$adodb->Execute($query);
	}
	$query="UPDATE crm_recent SET cr_timestamp=CURRENT_TIMESTAMP WHERE $warunek";
	$adodb->Execute($query);

	$obj=$MODULES->crm->files->$file->toolbar;
	if (!is_Object($obj)) return;

	echo crm_toolbar($obj,$self);

?>