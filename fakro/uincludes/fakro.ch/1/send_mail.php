<?
	global $FORM;

	$tab = unserialize(stripslashes($costxt));

	$MAILF = $tab["MAILF"];

	if (!is_array($FORM)) return;

	reset($FORM);
	$plain='';

	while (list($key,$val) = each($FORM))
	{
		$list.= $key." : ".$val."<br>";
		$plain.= $key." : ".$val."\n";
	}

	$boundary = "---=nextPart_".md5(rand());

	$list=base64_encode("<html><body>$list</body></html>");

$mail = "From: ".$MAILF[mailto]."
To: ".$MAILF[mailto]."
Subject: ".$MAILF[subject]." 
MIME-Version: 1.0
Content-Type: multipart/alternative;
	boundary=\"$boundary\"

This is a multi-part message in MIME format.

--$boundary
Content-Type: text/plain;
	charset=\"iso-8859-2\"
Content-Transfer-Encoding: 8bit

$plain
--$boundary
Content-Type: text/html;
	charset=\"iso-8859-2\"
Content-Transfer-Encoding: base64

$list

--$boundary--

";
	
	$sendmail_path = ini_get ("sendmail_path");
	if (!strstr($sendmail_path,"-t")) $sendmail_path.=" -t";
	if (strlen($CONST_SENDMAIL_PATH)) $sendmail_path=$CONST_SENDMAIL_PATH;
	
	$prg=popen($sendmail_path,"w");
	fwrite($prg,$mail);
	pclose($prg);
?>