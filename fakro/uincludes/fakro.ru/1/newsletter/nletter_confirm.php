<?
	if (strlen($cemail) && strlen($cid))
	{
		if ($act == 'in')
		{
			$sql = "UPDATE crm_customer SET 
					c_email = '$cemail' WHERE
					c_email = '$cid' AND
					c_server = $SERVER_ID";

			pg_exec($db,$sql);

			echo "Dziъkujemy za potwierdzenie.<br>";
			echo "Twѓj email zostaГ dodany do naszej bazy.";

		} 
		else
		{
			$sql = "SELECT COUNT(*) AS jest FROM crm_customer 
					WHERE c_id = $cid
					AND c_email = '$cemail'
					AND c_server = $SERVER_ID";

			parse_str(query2url($sql));

			if ($jest)
			{
				$sql = "DELETE FROM crm_customer WHERE 
						c_id = $cid AND c_server = $SERVER_ID";

				pg_exec($db,$sql);

				echo "Twѓj adres email zostaГ usuniъty z naszej bazy";
			}
			else
				echo "W naszej bazie nie figuruje podany adres email.";

		}
	} 

?>