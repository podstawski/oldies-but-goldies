<?
global $_SENDMAIL2_INCLUDED;
if ($_SENDMAIL2_INCLUDED==1 ) return;
$_SENDMAIL2_INCLUDED=1;

class sendmail_obj 
{  
	var $from,$to;
	var $cc=null;
	var $subject,$msg;
	var $type;
	var $bcc=null;
	var $att=null;
	var $precedence;
} 


function win2iso2($str)
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


function encode($_str,$iso)
{

	global $lang,$CHARSET_TAB;

	if ($lang=='p') $_str = win2iso2($_str);
	$encoding=$CHARSET_TAB[$lang];
	if (strtolower($encoding)=='windows-1250') $encoding='iso-8859-2';
	if (!strlen($encoding)) $encoding='utf-8';

	$str_ok = explode(" ", $_str);
	
	$ret = "";
	for ($i=0; $i<sizeof($str_ok); $i++)
	{
		$orig = $str_ok[$i];

		if (function_exists('quoted_printable_encode')) $str_ok[$i]=quoted_printable_encode($str_ok[$i]);
		else
		{
			$wynik='';

			for ($j=0;$j<strlen($str_ok[$i]);$j++)
			{
				
				if (ord($str_ok[$i][$j])>127 || $str_ok[$i][$j]=='=') $wynik.=sprintf('=%02X',ord($str_ok[$i][$j]));
				else $wynik.=$str_ok[$i][$j];
			}
			$str_ok[$i]=$wynik;
		}

		if (strcmp($orig, $str_ok[$i])!=0)
		{
			if ($iso) $ret.="=?${encoding}?Q?";
			$ret.=$str_ok[$i];
			if ($iso) $ret.="_?= ";
			else $ret.=" ";
		}
		else
			$ret.= $str_ok[$i]." ";
	}




	return $ret;

}



function sendmail_full($obj)
{
	return sendmail2($obj);
}


function sendmail2(&$obj)
{
	global $REMOTE_HOST,$REMOTE_ADDR;
	global $CONST_SENDMAIL_PATH;
	global $PHP_SUFFIX;

	global $lang,$CHARSET_TAB;

	$encoding=$CHARSET_TAB[$lang];
	if (!strlen($encoding) || strtolower($encoding)=='windows-1250') $encoding='iso-8859-2';
	if (strlen($lang)==2) $encoding='utf-8';

	$sendmail_path = ini_get ("sendmail_path");
	if (!strstr($sendmail_path,"-t")) $sendmail_path.=" -t";

	if (strlen($CONST_SENDMAIL_PATH)) $sendmail_path=$CONST_SENDMAIL_PATH;


	$from=$obj->from;
	$to=$obj->to;
	$cc=$obj->cc;
	$subject=$obj->subject;
	$msg=$obj->msg;
	$type=$obj->type;
	$bcc=$obj->bcc;

	$att=$obj->att;
	$att_cid= $obj->att_cid;

	if ( !strlen($from) ) 
	{
		if ($obj->debug) echo "NO FROM";
		return 0;
	}
	if ( !strlen($to) ) 
	{
		if ($obj->debug) echo "NO TO";
		return 0;
	}
	if ( !strlen($msg) )
	{
		if ($obj->debug) echo "NO BODY";
		return 0;
	}

	$cs_from=addslashes($from);
	$cs_to=addslashes($to);
	$cs_subject=addslashes($subject);
	$cs_msg=addslashes($msg);

	$insert="cs_from,cs_to,cs_subject,cs_msg";
	$values="'$cs_from','$cs_to','$cs_subject','$cs_msg'";

	if ($obj->webtd_sid)
	{
		$insert.=",cs_webtd_sid";
		$values.=",".$obj->webtd_sid;
	}
	global $SERVER_ID,$lang;

	if ($SERVER_ID)
	{
		$insert.=",cs_server";
		$values.=",".$SERVER_ID;
	}
	if (strlen($lang))
	{
		$insert.=",cs_lang";
		$values.=",'$lang'";
	}

	if (strlen($obj->action))
	{
		$insert.=",cs_action";
		$values.=",'$obj->action'";
	}

	$cs_cc_count=0;
	if (is_array($cc) && count($cc))
	{
		$cs_cc_count=count($cc);
		$insert.=",cs_cc";
		$values.=",'".addslashes(implode("\n",$cc))."'";
	}
	$insert.=",cs_cc_count";
	$values.=",".$cs_cc_count;

	$cs_bcc_count=0;
	if (is_array($bcc) && count($bcc))
	{
		$cs_bcc_count=count($bcc);
		$insert.=",cs_bcc";
		$values.=",'".addslashes(implode("\n",$bcc))."'";
	}
	$insert.=",cs_bcc_count";
	$values.=",".$cs_bcc_count;

	$__att=array_merge(@array_keys($att),@array_keys($att_cid));
	if (is_array($__att))
	{
		$insert.=",cs_att";
		$values.=",'".addslashes(implode("\n",$__att))."'";
	}



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

	$boundary = "0Ga-".time()."-".time()%2001;
	$msgbody.="Mime-Version: 1.0\n";
	$msgbody.= "Content-Type: multipart/mixed;";
	$msgbody.= " BOUNDARY=\"".$boundary."\"\n\n";

//	$msgbody.= "--".$boundary."\n";



	if (is_Array($att_cid))
	{
		if ($obj->debug)
		{
			echo "<br>ATTACHMENT_CID:";
		}

		$msgbody.= "--".$boundary."\n";
		$cid_boundary = "0Ga-".time()."-".time()%2002;
		$msgbody.="Content-Type: multipart/related;\n";
		$msgbody.= " BOUNDARY=\"".$cid_boundary."\"\n\n";

// add by Camel 
/*
		$msgbody.= "--".$boundary."\n";
		$cid_boundary = "0Ga-".time()."-".time()%2002;
		$msgbody.="Content-Type: multipart/alternative;\n";
		$msgbody.= " BOUNDARY=\"".$cid_boundary."\"\n\n";
*/
// add by Camel 

		$msgbody.= "--".$cid_boundary."\n";
		$msgbody.= "Content-Type: TEXT/$type; ";
		$msgbody.= "\n	charset=\"$encoding\"\n";
		$msgbody.= "Content-Transfer-Encoding: quoted-printable\n\n";
		$msgbody.= "$msg\n";


		while( list($key,$val) = each ($att_cid) )
		{

			if ($obj->debug)
			{
				echo "<br>&nbsp;";
				if (!file_exists($key)) echo "<span style='text-decoration:line-through'>";
				echo $key;
				if (!file_exists($key)) 
				{
					echo "</span>";
					echo " PWD=";
					system("pwd");
				}

				echo "<br>&nbsp;&nbsp;&nbsp;(".htmlspecialchars($val).")";

			}

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
		$msgbody.= "\n	charset=\"$encoding\"\n";
		$msgbody.= "Content-Transfer-Encoding: quoted-printable\n\n";
		$msgbody.= "$msg\n";
	}


	if (is_Array($att))
	{

		if ($obj->debug)
		{
			echo "<br>ATTACHMENT:";
		}

	
		while( list($key,$val) = each ($att) )
		{

			if ($obj->debug)
			{
				echo "<br>&nbsp;";
				if (!file_exists($key)) echo "<span style='text-decoration:line-through'>";
				echo $key;
				if (!file_exists($key)) 
				{
					echo "</span>";
					echo " PWD=";
					system("pwd");
				}

			}

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




	$cs_size=strlen($msgheader)+strlen($msgbody);
	$insert.=",cs_size";
	$values.=",".$cs_size;


	$query="INSERT INTO crm_sendmail_report 
			($insert) VALUES ($values)";
	global $adodb;
	if (is_Object($adodb)) $adodb->execute($query);



	if (!is_array($bcc)) $bcc=array("$to");


	$rm=(CONST_WINDOWS)?'del':'rm -f';
	$sh=(CONST_WINDOWS)?'"'.dirname(__FILE__).'\..\win\ShelExec.exe" /ShowCmd:none':'/bin/sh';
	$ext=(CONST_WINDOWS)?'bat':'sh';
	

	$bccpole="";
	for ($i=0;$i<count($bcc);$i++)
	{
		$bccpole.="Bcc: ".encode($bcc[$i],1)."\n";
		if ( ($i && !($i%100)) || $i==count($bcc)-1 )
		{
			$_msg="$msgheader$bccpole$msgbody";

			$plik="/tmp/sendmail".time().uniqid("");
			$f=fopen($plik,"w");
			if (!$f) return 0;
        	fputs($f,$_msg);
			fclose($f);

			
			if ($obj->debug) 
			{
				echo "<hr size=1>";
				echo nl2br(htmlspecialchars($_msg));
			}
			
			if ( !strlen($obj->exec_file) ) $obj->exec_file="$plik.$ext";

			$f=fopen($obj->exec_file,"a");



        	fputs($f,"$sendmail_path -f \"$from\" <$plik \n");
        	fputs($f,"$rm $plik\n"); 
        	if (!$obj->wait4flush) fputs($f,"$rm $obj->exec_file \n");
        	fclose($f);

			if (!$obj->wait4flush) 
			{
				$sm=$obj->exec_file;
				if (CONST_WINDOWS) $sm=str_replace('/','\\',$sm);
				//echo "$sh $sm $PHP_SUFFIX";
				exec("$sh $sm$PHP_SUFFIX");
				$obj->exec_file="";
			}

			$bccpole="";
		}
	}

	if ($obj->wait4flush && $obj->flush && strlen($obj->exec_file) ) 
	{
		$f=fopen($obj->exec_file,"a");
		fputs($f,"\n$rm -f $obj->exec_file\n");
		fclose($f);
		exec("/bin/sync");
		exec("$sh $obj->exec_file $PHP_SUFFIX");
		$obj->exec_file="";
		$obj->flush=0;

	}

	return 1;
	
}

function explode_path($path,$result_as_key=false)
{
		$wynik="";
		$path=explode(":",$path);

		if (is_Dir($path[0]))
		{
			$handle=opendir($path[0]);
			$dn=$path[0];
			while (($file = readdir($handle)) !== false) 
			{
				if ($file=="." || $file=="..") continue;
				if (is_dir("$dn/$file"))
				{
					$path[]="$dn/$file";
					continue;
				}
				if ($result_as_key) $wynik["$dn/$file"]="";	
				else $wynik[]="$dn/$file";
			}
			closedir($handle);
		}

		for ($i=1;$i<count($path);$i++ )
		{
			$w=explode_path($path[$i],$result_as_key);
			if (is_array($w)) $wynik=array_merge($wynik,$w);

		}

		return($wynik);
}


