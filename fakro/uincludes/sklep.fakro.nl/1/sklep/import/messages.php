<?

	foreach ($obj->magazyn->messages AS $label)
	{
		$msg_label=$label->label;
		$msg_group=$label->group;

		while ( list($msg_lang,$msg_msg) = each ($label->langs) )
		{
			$msg_msg=addslashes(stripslashes($msg_msg));


			if (!strlen($msg_msg)) 
				$query="DELETE FROM messages WHERE msg_lang='$msg_lang' AND msg_label='$msg_label'";
			else
			{
				$sql="SELECT count(*) AS c FROM messages WHERE msg_lang='$msg_lang' AND msg_label='$msg_label'";
				parse_str(ado_query2url($sql));

				$query=$c?
					"UPDATE messages SET msg_msg='$msg_msg' WHERE msg_lang='$msg_lang' AND msg_label='$msg_label'"
					:
					"INSERT INTO messages (msg_label,msg_lang,msg_msg) VALUES ('$msg_label','$msg_lang','$msg_msg')";
			}
			$projdb->execute($query);
		}

		if (strlen($msg_group))
		{
	
			$query="SELECT count(*) AS c  FROM messages WHERE msg_lang='ms' AND msg_label='$msg_label'";
			parse_str(ado_query2url($query));
			if (!$c)
			{
				$query="INSERT INTO messages (msg_label,msg_lang,msg_msg) VALUES ('$msg_label','ms','$msg_label')";
				$projdb->execute($query);
			}

			$sql="UPDATE messages SET msg_group='$msg_group' WHERE msg_label='$msg_label'";
			$projdb->execute($sql);
		}
	}


?>