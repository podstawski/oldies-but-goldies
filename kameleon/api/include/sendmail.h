<?
if ($_SENDMAIL_INCLUDED==1 ) return;
$_SENDMAIL_INCLUDED=1;

function win2iso($str)
{
	$str=str_replace("¥","¡",$str);
	$str=str_replace("Œ","¦",$str);
	$str=str_replace("","¬",$str);
	$str=str_replace("¹","±",$str);
	$str=str_replace("œ","¶",$str);
	$str=str_replace("Ÿ","¼",$str);

	return ($str);
}

function sendmail($from,$to,$subject,$msg)
{

	if ( !strlen($from) ) return 0;
	if ( !strlen($to) ) return 0;
	if ( !strlen($msg) ) return 0;

	$msg = stripslashes(win2iso($msg));
	$subject = stripslashes($subject);

	$msgheader="From: $from\nTo: $to\n";
	$msgheader.="Content-Type: text/plain\n   charset='iso-8859-2'\n";
	$msgheader.="Content-Transfer-Encoding: 8BIT\n";
	$msgheader.="Subject: $subject\n";


	$prg=popen("/usr/sbin/sendmail -t -f $from","w");
	if (!$prg) return 0;
	fputs($prg,"$msgheader\n$msg");
	pclose($prg);
	return 1;
	
}

function sendmailbcc($from,$to,$subject,$msg,$bcc)
{

	if ( !strlen($from) ) return 0;
	if ( !strlen($to) ) return 0;
	if ( !strlen($msg) ) return 0;
	

	$msg = stripslashes(win2iso($msg));
	$subject = stripslashes($subject);

	$msgheader="From: $from\nTo: $to\n";
	$msgheader.="Content-Type: text/plain\n   charset='iso-8859-2'\n";
	$msgheader.="Content-Transfer-Encoding: 8BIT\n";
	$msgheader.="Subject: $subject\n";


	$msg = stripslashes($msg);
	$bccpole="";
	for ($i=0;$i<count($bcc);$i++)
	{
		$bccpole.="Bcc: $bcc[$i]\n";
		if ( ($i && !$i%100) || $i==count($bcc)-1 )
		{
		   $msg="$msgheader$bccpole\n$msg";
        	   $prg=popen("/usr/sbin/sendmail -t -f $from","w");
        	   if (!$prg) return 0;
        	   fputs($prg,$msg);
        	   pclose($prg);

		   $bccpole="";
		}
	}


}

?>
