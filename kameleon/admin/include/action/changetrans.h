<?
	$action="";

	$sql="SELECT trans FROM servers WHERE id=$server";
	parse_str(ado_query2url($sql));

	if (strlen($trans)) $trans=unserialize($trans);

	$trans[$_lang][users]=is_array($transusers) ? array_keys($transusers) : array();

	$strans=serialize($trans);

	$sql="UPDATE servers SET trans='$strans' WHERE id=$server";
	$adodb->execute($sql);
?>