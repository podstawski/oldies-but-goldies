<?
	global $action;

	if (strlen($action) && !file_exists("$INCLUDE_PATH/action/$action.h")) return;

	$previous_action="";
	while( strlen($action) && !strlen($error) && $action!=$previous_action )
	{
		$previous_action=$action;


		if (file_exists("$INCLUDE_PATH/action/$action.h"))
		{
			include("$INCLUDE_PATH/action/$action.h");
		}
		
	}
	$action="";


	if (strlen($error))
	{
		echo "<script>alert('$error');history.go(-1);</script>";
		exit();
	}

?>
