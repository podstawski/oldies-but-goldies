<?
	global $SERVER_ID, $NLFRONT;
	$action = "";
	$wypisz_from=$NLFRONT[wypisz_from];
	$outpage=$NLFRONT[outpage];
	$szablon_page=$NLFRONT[szablon_page];
	$nl_info=$NLFRONT[nl_info];
	$inpage=$NLFRONT[inpage];
	$szablon_in=$NLFRONT[szablon_in];
	$host_addres=$NLFRONT[host_addres];

	if (strlen($NLFRONT[adres_email]))
	{
		if ($NLFRONT[whattodo] == 'in')
		{
			$sql = "SELECT COUNT(*) AS jest FROM crm_customer 
					WHERE c_email = '$NLFRONT[adres_email]' 
					AND c_email2 = '$NLFRONT[nl_grupa]'
					AND c_server = $SERVER_ID";
			parse_str(ado_query2url($sql));
			if (!$jest) 
			{
////////////////////////////////////////////////////////////
				$random_core = md5(uniqid(rand()));

				if ($KAMELEON_MODE)
					$link = "<A HREF=\"http://$HTTP_HOST/index.php?page=$inpage&cemail=$NLFRONT[adres_email]&cid=$random_core&act=in\">link</A>";
				else
					$link = "<A HREF=\"http://$host_addres/".kameleon_href("","cemail=".$NLFRONT[adres_email]."&cid=".$random_core."&act=in",$inpage)."\">link</A>";

				$sql = "INSERT INTO crm_customer (c_email, c_email2, c_server)
						VALUES ('$random_core','$NLFRONT[nl_grupa]',$SERVER_ID)";

//				$adodb->debug=1;
				$adodb->execute($sql);
//				$adodb->debug=0;
				$form = label("Dziêkujemy! Twój email zosta³ dodany do naszej bazy.");

				$mailto = $NLFRONT[adres_email];				
				$action="SendmailOnAction";
				$sendmail_action = $szablon_in;	//$mail_action;
//				include("$INCLUDE_PATH/.api/action/SendmailOnAction.h");

//////////////////////////////////////////////////////////////////

			} 
			else 
			{				
				$form = label("Twój email jest ju¿ w naszej bazie !");
				$error = label("Podany email jest ju¿ w naszej bazie !");
			}
				
		}
		else
		{
			$sql = "SELECT c_id AS jest FROM crm_customer 
					WHERE c_email = '$NLFRONT[adres_email]' 
					AND c_email2 = '$NLFRONT[nl_grupa]'
					AND c_server = $SERVER_ID";
			parse_str(ado_query2url($sql));
			if ($jest)
			{
				$subject = "Wypisanie";
				$mailfrom = "$wypisz_from";

				if ($KAMELEON_MODE)
					$link = "<A HREF=\"http://$HTTP_HOST/index.php?page=$outpage&cemail=$NLFRONT[adres_email]&cid=$jest&act=out\">link</A>";
				else
					$link = "<A HREF=\"http://$host_addres/".kameleon_href("","cemail=".$NLFRONT[adres_email]."&cid=".$jest."&act=out",$outpage)."\">link</A>";

				$mailto = $NLFRONT[adres_email];				
				$action="SendmailOnAction";
				$sendmail_action = $szablon_page;	//$mail_action;
//				include("$INCLUDE_PATH/.api/action/SendmailOnAction.h");
			}

			$form = "Na podany adres wys³ano informacjê co zrobiæ, aby zakoñczyæ proces wypisywania siê.";
		}
	}

?>