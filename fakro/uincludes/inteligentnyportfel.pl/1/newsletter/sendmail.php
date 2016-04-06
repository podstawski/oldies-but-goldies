<?
global $_SENDMAIL2_INCLUDED;
if ($_SENDMAIL2_INCLUDED==1 ) return;
$_SENDMAIL2_INCLUDED=1;

if (function_exists('encode')) return;

class sendmail_obj 
{  
	var $from,$to,$cc;
	var $subject,$msg;
	var $type;
	var $bcc;
	var $att;
	var $precedence;
} 


function win2iso2($str)
{
	//return $str;

	$str=ereg_replace("Ľ","Ą",$str);
	$str=ereg_replace("","Ś",$str);
	$str=ereg_replace("","Ź",$str);
	$str=ereg_replace("š","ą",$str);
	$str=ereg_replace("","ś",$str);
	$str=ereg_replace("","ź",$str);

	return ($str);
}


function encode($_str,$iso)
{

	$_str = win2iso2($_str);


	$str_ok = explode(" ", $_str);
	
	$ret = "";
	for ($i=0; $i<sizeof($str_ok); $i++)
	{
		$orig = $str_ok[$i];

		$str_ok[$i] = ereg_replace("=", "=3D", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("\?", "=3F", $str_ok[$i]);


		$str_ok[$i] = ereg_replace("Ą", "=A1", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ś", "=A6", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ć", "=C6", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ź", "=AC", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ż", "=AF", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ń", "=D1", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ó", "=D3", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ł", "=A3", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ę", "=CA", $str_ok[$i]);

		$str_ok[$i] = ereg_replace("ą", "=B1", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ś", "=B6", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ć", "=E6", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ź", "=BC", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ż", "=BF", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ń", "=F1", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ó", "=F3", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ł", "=B3", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ę", "=EA", $str_ok[$i]);


		if (strcmp($orig, $str_ok[$i])!=0)
		{
			if ($iso==1) $ret.="=?iso-8859-2?Q?";
			$ret.=$str_ok[$i];

			if ($iso==1) $ret.="?=";
			$ret.=" ";
		}
		else
			$ret.= $str_ok[$i]." ";
	}

	if ($iso==2) $ret="=?iso-8859-2?Q?${ret}?=";
	return $ret;
}



function sendmail_full($obj)
{
	return sendmail2($obj);
}

function sendmail2($obj)
{
	global $REMOTE_HOST,$REMOTE_ADDR;
	global $CONST_SENDMAIL_PATH;

	$from=$obj->from;
	$to=$obj->to;
	$cc=$obj->cc;
	$subject=$obj->subject;
	$msg=$obj->msg;
	$type=$obj->type;
	$bcc=$obj->bcc;
	$att=$obj->att;

	if ( !strlen($from) ) return 0;
	if ( !strlen($to) ) return 0;
	if ( !strlen($msg) ) return 0;

	if ( strtolower($type)!="html" ) $type="plain";

	$msg = stripslashes(encode($msg,0));

	$subject = stripslashes($subject);
	$subject = encode($subject,1);

	$from = encode($from,1);
	
	$to = encode($to,1);
	$mailcc="";
	if (is_Array($cc))
	{
		for ($c=0;$c<count($cc);$c++)
		{
			$mailcc.="Cc: ".encode($cc[$c],1)."\n";
		}
	}
	else if (strlen($cc)) $mailcc ="Cc: ".encode($cc,1)."\n";

	

	$msgheader="Received: from $REMOTE_HOST ($REMOTE_HOST [$REMOTE_ADDR])\n";
	$msgheader.="From: $from\nTo: $to\n$mailcc";
	if (strlen($obj->precedence)) $msgheader.="Precedence: ".$obj->precedence."\n";
	if (strlen($obj->reply)) $msgheader.="Reply-to: ".$obj->reply."\n";



	if (is_Array($att))
	{
		$boundary = "0Ga-".time()."-".time()%2001;

		$msgbody.="Mime-Version: 1.0\n";
		$msgbody.= "Content-Type: MULTIPART/MIXED;";
		$msgbody.= " BOUNDARY=\"".$boundary."\"\n\n";

		$msgbody.= "--".$boundary."\n";
		$msgbody.= "Content-Type: TEXT/$type; ";
		$msgbody.= "\n	charset=\"iso-8859-2\"\n";
		$msgbody.= "Content-Transfer-Encoding: quoted-printable\n\n";
		$msgbody.= "$msg\n";

		for ($x=0; $x<sizeof($att); $x++)
		{
			$attache = $att[$x];
			$at_file = chunk_split(base64_encode($attache[0]));

			$msgbody.= "\n--".$boundary."\n";
			$msgbody.= "Content-Type: ";
			$msgbody.= $attache[1]."; ";
			$msgbody.= "name=\"";
			$msgbody.= $attache[2];
			$msgbody.= "\"\n";
			$msgbody.= "Content-Transfer-Encoding: BASE64\n";
			$msgbody.= "Content-Description: ";
			$msgbody.= $attache[2]."\n";
			$msgbody.= "Content-Disposition: attachment; filename=\"";
			$msgbody.= $attache[2]."\"\n\n";

			$msgbody.= $at_file;
		}

		$msgbody.= "\n--".$boundary."--\n";
	}
	else
	{
		$msgbody.="Mime-Version: 1.0\n";
		$msgbody.="Content-Type: text/$type;\n   charset=\"iso-8859-2\"\n";
		$msgbody.="Content-Transfer-Encoding: quoted-printable\n";
		$msgbody.= "\n".$msg;
	}
	$msgheader.="Subject: $subject\n";

	if (!is_array($bcc)) $bcc=array("$to");

	$bccpole="";
	for ($i=0;$i<count($bcc);$i++)
	{
		$bccpole.="Bcc: ".encode($bcc[$i],1)."\n";
		if ( ($i && !($i%1000)) || $i==count($bcc)-1 )
		{
		   $_msg="$msgheader$bccpole$msgbody";

		   $plik="/tmp/sendmail".uniqid("");
		   $f=fopen($plik,"w");

	   	   if (!$f) return 0;
       	   fputs($f,$_msg);
       	   fclose($f);

		   $f=fopen("$plik.sh","w");
        	   fputs($f,"$CONST_SENDMAIL_PATH <$plik \n");
        	   fputs($f,"rm -f $plik\n");
        	   fputs($f,"rm -f $plik.sh\n");
        	   fclose($f);

		   exec("/bin/sh $plik.sh >/dev/null 2>/dev/null &");



		   $bccpole="";
		}
	}


	return 1;
	
}
?>
