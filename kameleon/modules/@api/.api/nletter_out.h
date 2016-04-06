<?
	if ($cos == 1)
	{
		if (strlen($cemail))
		{
			$sql = "UPDATE crm_customer 
					SET c_tel = '1' WHERE 
					c_email = '$cemail'
					AND c_server = $SERVER_ID";

			$adodb->execute($sql);

			echo "Dziêkujemy. Twoje potwierdzenie zosta³o przyjête.";
			return;
		}

		echo "
		<TABLE>
		<TR>
			<TD>
			<FORM METHOD=POST ACTION=\"$self\">
			<INPUT TYPE=\"text\" NAME=\"cemail\">
			</TD>
		</TR>
		<TR>
			<TD align=\"center\"><INPUT TYPE=\"submit\" value=\"Potwierdzam\"></TD>
		</TR>
		</TABLE>
		</FORM>";
		return;
	}
	
	if (strlen($cemail) && strlen($cid))
	{
		if ($act == 'in')
		{
			$sql = "UPDATE crm_customer SET 
					c_email = '$cemail' WHERE
					c_email = '$cid' AND
					c_server = $SERVER_ID";

			$adodb->execute($sql);

			echo label("Dziêkujemy za potwierdzenie.")."<br>";
			echo label("Twój email zosta³ dodany do naszej bazy.");

		} 
		else
		{
			$sql = "SELECT COUNT(*) AS jest FROM crm_customer 
					WHERE c_id = $cid
					AND c_email = '$cemail'
					AND c_server = $SERVER_ID";

			parse_str(ado_query2url($sql));

			if ($jest)
			{
				$sql = "DELETE FROM crm_customer WHERE 
						c_id = $cid AND c_server = $SERVER_ID";

				$adodb->execute($sql);

				echo label("Twój adres email zosta³ usuniêty z naszej bazy");
			}
			else
				echo label("W naszej bazie nie figuruje podany adres email.");

		}
	} 

?>