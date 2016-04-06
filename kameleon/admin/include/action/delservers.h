<?
	$action="";

	if (!is_array($del_list)) $error=label("Nothing has been selected");

	foreach (array_keys($del_list) AS $server)
	{
		include("include/action/delserver.h");
	}
?>
