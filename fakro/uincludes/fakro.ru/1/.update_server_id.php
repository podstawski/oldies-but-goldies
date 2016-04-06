<?
	global $NEW_SERVER_ID;
	global $adodb;
	
	$adodb=$fakrodb;


	if ($NEW_SERVER_ID)
	{
		$adodb->debug=1;

		//$adodb->begin();

		
		$query="
			DELETE FROM system_grupa;
			DELETE FROM system_acl_grupa;
			DELETE FROM system_action;
			DELETE FROM system_obiekt;
			DELETE FROM system_acl_obiekt;
			DELETE FROM system_log;
			DELETE FROM system_user;
		";

		$adodb->execute($query);
		exit();
		


		//$adodb->commit();
		$adodb->debug=0;



	}


	$query="SELECT count(*) AS count_all FROM system_obiekt";
	parse_str(ado_query2url($query));

	if ($count_all>0) 
	{
		echo "<form method=post action=$self><input type=hidden name=NEW_SERVER_ID value=$SERVER_ID>";
		echo "<font style=\"color:red\">W bazie danych znajduje siъ system autoryzacji, usunac ?</font>";
		echo "<br><input type=submit value=\" Dostosuj \"></form>";
	}

?>
