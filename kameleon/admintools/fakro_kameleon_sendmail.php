<?

function kameleon_sendmail($mail_tresc_listu_html,$mail_tresc_listu_tekst)
{
	#kodowanie maila
	define('CHARSET',"UTF-8");
	
	#ustawienia do poczty 
	$params["host"] = "mail.fakro.com.pl";		// adres serwera SMTP
	$params["port"] = "25";						// port serwera SMTP (zazwyczaj: 25)
	$params["auth"] = true;						// czy serwer wymaga autoryzacji (zazwyczaj: true)
	$params["username"] = "robotfakro";			// login konta (ewentualnie adres e-mail konta)
	$params["password"] = "2wsxcde3";			// haslo konta
	
	include("Mail.php");
	include("Mail/mime.php");
	
	$headers['From']		= $MAILF[mailfrom];
	$headers['To']			= $MAILF[mailto];
	$headers['Reply-to']		= $MAILF[mailfrom];
	$headers['Subject']		= $MAILF[subject];
	
	$htmlMessage	= $mail_tresc_listu_html;
	$tekstMessage	= $mail_tresc_listu_tekst;
	
	$mime = new Mail_Mime();
	$mime->setHtmlBody($htmlMessage);
	$mime->setTxtBody($tekstMessage);
	
	$body = $mime->get(array(
			"text_charset" => CHARSET,
			"html_charset" => CHARSET,
			"head_charset" => CHARSET,
			"html_encoding" => CHARSET,
			"text_encoding" => "8bit")
	);
	$hdrs = $mime->headers($headers);
	
	$m=&Mail::Factory("smtp",$params);
	$error = @$m->send($MAILF[mailto],$hdrs,$body);
	
	if(PEAR::isError($error))
	{
	    $error_sendmail = $error->toString();
	    }else{
	    $error_sendmail = "OK";
	}

return $error_sendmail;
}
