<?
	

	global $NEW_SKLEP_SERVER_ID;
	if ($NEW_SKLEP_SERVER_ID)
	{
		$query="SELECT distinct(sag_server) AS old_server FROM system_acl_grupa";
		parse_str(ado_query2url($query));
		
		$adodb->debug=1;
		$adodb->begin();


		$query="UPDATE system_grupa SET sg_server=$NEW_SKLEP_SERVER_ID WHERE sg_server=$old_server;";
		$adodb->execute($query);
		$query="UPDATE system_acl_grupa SET sag_server=$NEW_SKLEP_SERVER_ID WHERE sag_server=$old_server;";
		$adodb->execute($query);
		$query="UPDATE system_action SET sa_server=$NEW_SKLEP_SERVER_ID WHERE sa_server=$old_server;";
		$adodb->execute($query);

		$query="DELETE FROM system_obiekt WHERE so_server=$NEW_SKLEP_SERVER_ID ";
		$adodb->execute($query);
		
		$query="UPDATE system_obiekt SET so_server=$NEW_SKLEP_SERVER_ID WHERE so_server=$old_server;";
		$adodb->execute($query);
		$query="UPDATE system_acl_obiekt SET sao_server=$NEW_SKLEP_SERVER_ID WHERE sao_server=$old_server;";
		$adodb->execute($query);

		$query="UPDATE system_log SET sl_server=$NEW_SKLEP_SERVER_ID WHERE sl_server=$old_server;";
		$adodb->execute($query);
		

		$query="UPDATE sklep SET sk_server=$NEW_SKLEP_SERVER_ID WHERE sk_server=$old_server;";
		$adodb->execute($query);


		$adodb->commit();
		$adodb->debug=0;



	}


	$query="SELECT count(*) AS count_all FROM system_acl_grupa";
	parse_str(ado_query2url($query));

	$query="SELECT count(*) AS count_server FROM system_acl_grupa WHERE sag_server=$SERVER_ID";
	parse_str(ado_query2url($query));


	if ($count_all>0 && $count_server==0)
	{
		echo "<form method=post action=$self><input type=hidden name=NEW_SKLEP_SERVER_ID value=$SERVER_ID>";
		echo "<font style=\"color:red\">W bazie danych znajduje siê sklep, natomiast nie jest zwi±zany z bie¿±cy serwerem.</font>";
		echo "<br><input type=submit value=\" Dostosuj ".C_PROJ_CONNECT_DBNAME."\"></form>";
	}

?>