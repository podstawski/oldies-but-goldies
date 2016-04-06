<?

class api_sendmail_obj 
{  
	var $from,$to,$cc;
	var $subject,$msg;
	var $type;
	var $bcc;
	var $att;
	var $precedence;
	var $att_cid;
} 


function api_win2iso($str)
{
	//return $str;

	$str=ereg_replace("¥","¡",$str);
	$str=ereg_replace("Œ","¦",$str);
	$str=ereg_replace("","¬",$str);
	$str=ereg_replace("¹","±",$str);
	$str=ereg_replace("œ","¶",$str);
	$str=ereg_replace("Ÿ","¼",$str);

	return ($str);
}


function api_encode($_str,$iso)
{

	$_str = api_win2iso($_str);


	$str_ok = explode(" ", $_str);
	
	$ret = "";
	for ($i=0; $i<sizeof($str_ok); $i++)
	{
		$orig = $str_ok[$i];

		$str_ok[$i] = ereg_replace("=", "=3D", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("\?", "=3F", $str_ok[$i]);

		$str_ok[$i] = ereg_replace("¡", "=A1", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("¦", "=A6", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Æ", "=C6", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("¬", "=AC", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("¯", "=AF", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ñ", "=D1", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ó", "=D3", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("£", "=A3", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("Ê", "=CA", $str_ok[$i]);

		$str_ok[$i] = ereg_replace("±", "=B1", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("¶", "=B6", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("æ", "=E6", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("¼", "=BC", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("¿", "=BF", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ñ", "=F1", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ó", "=F3", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("³", "=B3", $str_ok[$i]);
		$str_ok[$i] = ereg_replace("ê", "=EA", $str_ok[$i]);


		if (strcmp($orig, $str_ok[$i])!=0)
		{
			if ($iso) $ret.="=?iso-8859-2?Q?";
			$ret.=$str_ok[$i];
			if ($iso) $ret.="_?= ";
			else $ret.=" ";
		}
		else
			$ret.= $str_ok[$i]." ";
	}

	return $ret;
}



function api_sendmail($obj)
{
	global $REMOTE_HOST,$REMOTE_ADDR;

	$from=$obj->from;
	$to=$obj->to;
	$cc=$obj->cc;
	$subject=$obj->subject;
	$msg=$obj->msg;
	$type=$obj->type;
	$bcc=$obj->bcc;
	$att=$obj->att;
	$att_cid= $obj->att_cid;
	if ( !strlen($from) ) return 0;
	if ( !strlen($to) ) return 0;
	if ( !strlen($msg) ) return 0;

	if ( strtolower($type)!="html" ) $type="plain";

	$msg = stripslashes(api_encode($msg,0));

	$subject = stripslashes($subject);
	$subject = api_encode($subject,1);

	$from = api_encode($from,1);
	
	$to = api_encode($to,1);
	$mailcc="";
	if (is_Array($cc))
	{
		for ($c=0;$c<count($cc);$c++)
		{
			$mailcc.="Cc: ".api_encode($cc[$c],1)."\n";
		}
	}
	else if (strlen($cc)) $mailcc ="Cc: ".api_encode($cc,1)."\n";

	

	$msgheader="Received: from $REMOTE_HOST ($REMOTE_HOST [$REMOTE_ADDR])\n";
	$msgheader.="From: $from\nTo: $to\n$mailcc";
	if (strlen($obj->precedence)) $msgheader.="Precedence: ".$obj->precedence."\n";

	$boundary = "0Ga-".time()."-".time()%2001;
	$msgbody.="Mime-Version: 1.0\n";
	$msgbody.= "Content-Type: MULTIPART/MIXED;";
	$msgbody.= " BOUNDARY=\"".$boundary."\"\n\n";

//	$msgbody.= "--".$boundary."\n";



	if (is_Array($att_cid))
	{

		$msgbody.= "--".$boundary."\n";
		$cid_boundary = "0Ga-".time()."-".time()%2002;
		$msgbody.="Content-Type: multipart/related;\n";
		$msgbody.= " BOUNDARY=\"".$cid_boundary."\"\n\n";

		$msgbody.= "--".$cid_boundary."\n";
		$msgbody.= "Content-Type: TEXT/$type; ";
		$msgbody.= "\n	charset=\"iso-8859-2\"\n";
		$msgbody.= "Content-Transfer-Encoding: quoted-printable\n\n";
		$msgbody.= "$msg\n";


		while( list($key,$val) = each ($att_cid) )
		{
			if (!file_exists($key)) continue;	
			$f=popen("file -bi $key","r");
			$type=fread($f,100);
			pclose($f);
			$type=trim($type);

			$f=fopen($key,"rb");
			$data=fread($f,filesize ($key));
			fclose($f);

			$name=basename($key);

			$at_file = chunk_split(base64_encode($data));

			$msgbody.= "\n--".$cid_boundary."\n";
			$msgbody.= "Content-Type: $type;";
			$msgbody.= "\n	name=\"$name\"\n";
			$msgbody.= "Content-Transfer-Encoding: base64\n";

			if (strlen($val)) 
				$msgbody.=$val;

			$msgbody.= "\n";

			$msgbody.= $at_file;			
		}

		$msgbody.= "\n--".$cid_boundary."--\n";

	} else 
	{
		$msgbody.= "--".$boundary."\n";
		$msgbody.= "Content-Type: TEXT/$type; ";
		$msgbody.= "\n	charset=\"iso-8859-2\"\n";
		$msgbody.= "Content-Transfer-Encoding: quoted-printable\n\n";
		$msgbody.= "$msg\n";
	}


	if (is_Array($att))
	{
	
		while( list($key,$val) = each ($att) )
		{
			if (!file_exists($key)) continue;	
			$f=popen("file -bi $key","r");
			$type=fread($f,100);
			pclose($f);
			$type=trim($type);

			$f=fopen($key,"rb");
			$data=fread($f,filesize ($key));
			fclose($f);

			$name=basename($key);

			$at_file = chunk_split(base64_encode($data));

			$msgbody.= "\n--".$boundary."\n";
			$msgbody.= "Content-Type: $type;";
			$msgbody.= "\n	name=\"$name\"\n";
			$msgbody.= "Content-Transfer-Encoding: base64\n";

			if (strlen($val)) 
				$msgbody.=$val;
			else
			{
				$msgbody.= "Content-Description: $name\n";
				$msgbody.= "Content-Disposition: attachment; filename=\"$name\"\n";
			}
			$msgbody.= "\n";

			$msgbody.= $at_file;
	
		}

		$msgbody.= "\n--".$boundary."--\n";
	}

	$msgheader.="Subject: $subject\n";

	if (!is_array($bcc)) $bcc=array("$to");

	$bccpole="";
	for ($i=0;$i<count($bcc);$i++)
	{
		$bccpole.="Bcc: ".api_encode($bcc[$i],1)."\n";
		if ( ($i && !($i%100)) || $i==count($bcc)-1 )
		{
		   $_msg="$msgheader$bccpole$msgbody";

		   $plik="/tmp/sendmail".uniqid("");
		   $f=fopen($plik,"w");

	       	   if (!$f) return 0;
        	   fputs($f,$_msg);
        	   fclose($f);

		   $f=fopen("$plik.sh","w");
        	   fputs($f,"/usr/sbin/sendmail -t -f \"$from\" <$plik \n");
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
