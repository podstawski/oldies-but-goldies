<?
	$action="";

	$sql="DELETE FROM webtrans WHERE wt_server=$server AND wt_lang='$_lang'";
	$adodb->execute($sql);


	$sql="SELECT trans FROM servers WHERE id=$server";
	parse_str(ado_query2url($sql));

	if (strlen($trans)) $trans=unserialize($trans);

	unset($trans[$_lang]);
	$strans=serialize($trans);

	$sql="UPDATE servers SET trans='$strans' WHERE id=$server";
	$adodb->execute($sql);


?>