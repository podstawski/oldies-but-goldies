<?
	global $_REQUEST;

	$tab = unserialize(stripslashes($costxt));

	$MAILF = $tab["MAILF"];

	reset($_REQUEST);
	$plain='';
	$html='';

	while (list($header,$t) = each($_REQUEST))
		if (is_array($t)) 
		{
			$html.="<b>$header</b><br>";
			$plain.="--- $header ---\n";
			while (list($k,$v)=each($t))
			{
				$html.="&nbsp;$k: $v<br>";
				$plain.=" $k: $v\n";
			}
		}
		$list.= $key." : ".$val."<br>";



	if (!strlen($plain)) return;

	$html=base64_encode("<html><body>$html</body></html>");


	$boundary = "---=nextPart_".md5(rand());

$mail = "From: ".$MAILF[mailfrom]."
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

$html

--$boundary--

";
	
	$sendmail_path = ini_get ("sendmail_path");
	if (!strstr($sendmail_path,"-t")) $sendmail_path.=" -t";
	if (strlen($CONST_SENDMAIL_PATH)) $sendmail_path=$CONST_SENDMAIL_PATH;
	
	$prg=popen($sendmail_path,"w");
	fwrite($prg,$mail);
	pclose($prg);
?>