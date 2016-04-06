<?
	$query="SELECT xml FROM webtd WHERE sid=".$API_REQUEST[sid];
	parse_str(ado_query2url($query));
	if (!strlen($xml)) return;

	$tab = unserialize(stripslashes($xml));

	$MAILF = $tab["MAILF"];
	if (!strlen($MAILF[mailfrom]) || !strlen($MAILF[mailto])) return;

	if (strlen($plain) && ($ob & 1) && ($ob & 2)) // ob_start + ob_end
	{
		ob_start();
		include('../remote/ob_start.h');
		$_REQUEST=array_merge($_REQUEST,$API_REQUEST);
		echo $plain;
		include('../remote/ob_end.h');

		$plain=ob_get_contents();
		ob_end_clean;

		$plain=stripslashes($plain);
		$html=$plain;

		$plain=ereg_replace("\n"," ",$plain);
		$plain=ereg_replace("\r","",$plain);

		$plain=eregi_replace("<br[^>]*>","\n",$plain);
		$plain=eregi_replace("</p>","\n",$plain);
		$plain=eregi_replace("<[^>]+>","",$plain);

		
	}
	else
	{
		reset($API_REQUEST);
		$plain='';
		$html='';

		while (list($header,$t) = each($API_REQUEST))
			if (is_array($t)) 
			{
				$html.="<b>$header</b><br><table style=\"border-top:3px double gray; border-bottom:3px double gray;\">";
				$plain.="--- $header ---\n";
				while (list($k,$v)=each($t))
				{
					$html.="<tr><td style=\"border-bottom:1px solid silver;\">$k</td><td style=\"border-bottom:1px solid silver;\">$v</td></tr>";
					$plain.=" $k: $v\n";
				}
				$html.="\n</table>";
			}
			$list.= $key." : ".$val."<br>\n";
	}
	if (!strlen($plain)) return;

	$mail_tresc_listu_html	= $html;
	$mail_tresc_listu_tekst	= $plain;
	$html=chunk_split(base64_encode("<html><body>$html</body></html>"));

	$boundary = "---=nextPart_".md5(rand());

	foreach (array('mailfrom','mailto','subject') AS $pole)
	{
		if (strstr($MAILF[$pole],'$') ) eval ('$MAILF[$pole]='.$MAILF[$pole].';');
	}
	
	
	
	if (file_exists('../remote/kameleon_sendmail.php'))
	{
		include_once('../remote/kameleon_sendmail.php');
		kameleon_sendmail($mail_tresc_listu_html,$mail_tresc_listu_tekst,$MAILF);
		return;
	}
	
	

$mail = "From: ".$MAILF[mailfrom]."
To: ".$MAILF[mailto]."
Subject: ".$MAILF[subject]." 
MIME-Version: 1.0
Content-Type: multipart/alternative;
	boundary=\"$boundary\"

This is a multi-part message in MIME format.

--$boundary
Content-Type: text/plain;
	charset=\"utf-8\"
Content-Transfer-Encoding: 8bit

$plain
--$boundary
Content-Type: text/html;
	charset=\"utf-8\"
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