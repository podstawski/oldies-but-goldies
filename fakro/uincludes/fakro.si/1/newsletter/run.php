<?
function win2iso2($str)
{
	//return $str;

	$str=ereg_replace("¥","¡",$str);
	$str=ereg_replace("","¦",$str);
	$str=ereg_replace("","¬",$str);
	$str=ereg_replace("¹","±",$str);
	$str=ereg_replace("","¶",$str);
	$str=ereg_replace("","¼",$str);

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

	$dir=dirname(dirname(dirname($arv[0])));

	while(strlen($dir)>1)
	{
		if (file_exists("$dir/const.h")) 
		{
			include ("$dir/const.h");
			break;
		}
		if (file_exists("$dir/const.php")) 
		{
			include ("$dir/const.php");
			break;
		}
	}

	$sendmail_path = ini_get ("sendmail_path");
	if (!strstr($sendmail_path,"-t")) $sendmail_path.=" -t";

	if (strlen($CONST_SENDMAIL_PATH)) $sendmail_path=$CONST_SENDMAIL_PATH;


	$ludzie=file($argv[1]);
	$template=$argv[2];
	$mailfile=$template.".msg";

	$mail=implode("",file($template));
	
	

	$ile=count($ludzie);
	//$ile=5;


	if ($ile>200)
	{
			$d=date('d');$m=date('m');$y=date('Y');
			$dzis18=mktime(17,59,0,$m,$d,$y);
			$time2go=$dzis18-time();
			if ($time2go>0) sleep($time2go);
	}


	for ($i=0;$i<$ile;$i++)
	{
		$to=trim($ludzie[$i]);
		if ($to[0]=="<") $to=substr($to,1,strlen($to)-2);
		$to0=encode($to,0);
		$to1=encode($to,1);
		$pure=$to;

		if (strstr($pure,'<'))
		{
			$p1=strpos($pure,'<');
			$p2=strpos($pure,'>');
			$pure=substr($pure,$p1+1,$p2-$p1-1);
		}

		$str=ereg_replace("\\\$EMAIL",$to1,$mail);
		$str=ereg_replace("\\\$TO",$to1,$str);
		$str=ereg_replace("\\\$NAME",$to0,$str);
		$str=ereg_replace("\\\$PUREEMAIL",$pure,$str);

		$plik=fopen($mailfile,"w");
		fwrite($plik,$str);
		fclose($plik);


		system("$sendmail_path -f $argv[3] <$mailfile");
	}
	unlink ($mailfile);
	unlink ($template);
	unlink ($argv[1]);

?>
