<?
	global $SERVER_ID, $NLFRONT;
	$action = "";
	$mailfrom = "newsletter@lena.pl";
	$subject = "Newsletter www.lenalighting.pl";
	$headers = "From: newsletter@lenalighting.pl\r\n";
	$headers.= "Content-type: text/html; charset=iso-8859-2\r\n";
	if (strlen($NLFRONT[adres_email]))
	{
		if ($NLFRONT[whattodo] == 'in')
		{
			$sql = "SELECT COUNT(*) AS jest FROM crm_customer 
					WHERE c_email = '$NLFRONT[adres_email]' 
					AND c_email2 = '$NLFRONT[nl_grupa]'
					AND c_server = $SERVER_ID";
//			echo $sql;
			parse_str(query2url($sql));
			if (!$jest) 
			{
				$random_core = md5(uniqid(rand()));

				$link = "<A HREF=\"http://www.lenalighting.pl/newsletter/potwierdzenie".$lang.".php?cemail=".$NLFRONT[adres_email]."&cid=".$random_core."&act=in\">link</A>";

				$sql = "INSERT INTO crm_customer (c_email, c_email2, c_server)
						VALUES ('$random_core','$NLFRONT[nl_grupa]',$SERVER_ID)";

				pg_exec($db,$sql);
//				echo $sql;
				$mailto = $NLFRONT[adres_email];
				mail($mailto,$subject,"Aby potwierdziц chъц zapisania siъ do newsletera lenalighting.pl kliknij na ten $link",$headers);

			} 
			else 
			{				
				$error = "Podany email jest juП w naszej bazie !";
			}
				
		}
		else
		{
			$sql = "SELECT c_id AS jest FROM crm_customer 
					WHERE c_email = '$NLFRONT[adres_email]' 
					AND c_email2 = '$NLFRONT[nl_grupa]'
					AND c_server = $SERVER_ID";
//			echo $sql;
			parse_str(query2url($sql));
			if ($jest)
			{			
				$link = "<A HREF=\"http://www.lenalighting.pl/newsletter/potwierdzenie".$lang.".php?cemail=".$NLFRONT[adres_email]."&cid=".$jest."&act=out\">link</A>";

				$mailto = $NLFRONT[adres_email];
				mail($mailto,$subject,"Aby potwierdziц chъц wypisania siъ z newsletera lenalighting.pl kliknij na ten $link",$headers);
			}

		}
	}

?>