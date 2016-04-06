<?
	global $msg_id;
	$action="";

	$msg_id+=0;	
	$action_id = $msg_id;
	$query="SELECT msg_label FROM messages WHERE msg_id=$msg_id";
	parse_str(query2url($query));

	$query="DELETE FROM messages WHERE msg_label='$msg_label'";
	
	//echo nl2br($query);return;
	if (pg_Exec($db,$query)) {}
?>

