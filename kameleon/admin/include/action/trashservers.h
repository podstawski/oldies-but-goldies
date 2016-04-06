<?
	$action="";

	//if (!is_array($del_list)) $error=label("Nothing has been selected");

	foreach (array_keys($del_list) AS $server)
	{
		include("include/action/trashserver.h");
	}

	foreach (explode(":",$del_users) AS $SetLogin)
	{
		if (!strlen(trim($SetLogin))) continue;
		include("include/action/deluser.h");
	}

?>
