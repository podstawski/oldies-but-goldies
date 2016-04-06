<?

if (!is_array($SENDMAIL)) return;

if (!is_array($SENDMAIL[att])) $SENDMAIL[att]=$SENDMAIL_att;
if (!is_array($SENDMAIL[cc])) $SENDMAIL[cc]=$SENDMAIL_cc;
if (!is_array($SENDMAIL[bcc])) $SENDMAIL[bcc]=$SENDMAIL_bcc;

//echo "SM="; print_r($SENDMAIL);


include_once("include/sendmail2.h");
global $UIMAGES;

$att=$SENDMAIL[att];

$msg=stripslashes($SENDMAIL[msg]);


$msg=eregi_replace("<br>","<br>\r\n",$msg);
$msg=eregi_replace("<p>","<p>\r\n",$msg);
$msg=eregi_replace("</span>","</span>\r\n",$msg);
$msg=eregi_replace("<a","\r\n<a",$msg);

//$f=fopen("/tmp/dupa","w"); fwrite($f,$msg); fclose($f);

if (!is_Object($list) ) 
{
	$list = new sendmail_obj;
	$list->debug=0;
}

$list->type = "html";
$hit = array();
$tresc = $msg;
	

//echo "$tresc = $UIMAGES";

while (strpos($tresc,$UIMAGES) > 0)
{
	$poz = strpos($tresc,$UIMAGES);
	$delim=$tresc[$poz-1];
	$tresc = substr($tresc,$poz+strlen($UIMAGES));

	if ($delim=="=")
	{
		$closest=10000000;
		foreach(array(" ",">","\n","\r","\t") AS $v)
		{
			$p=strpos($tresc,$v);
			if ($p && $p<$closest)
			{
				$closest=$p;	
				$delim=$v;
			}
		}
	}
		
	$img=substr($tresc,0,strpos($tresc,$delim));
	$img_path="$UIMAGES/$img";
	$img_path=ereg_replace("[/]+","/",$img_path);
	$hit[$img_path] = 1;		
}


$att_cid=null;

while (list($img,$v)=each($hit))
{
	$cid=base64_encode($img);
	$msg=str_replace("$img","cid:$cid",$msg);
	$att_cid[$img] = "Content-ID: <$cid>\n";

}


$SENDMAIL[msg]=$msg;
if ($SENDMAIL[debug])
{
	$DEBUG_FILE="/tmp/sendmail_debug_".uniqid("").time()."/debug.txt";
	mkdir(dirname($DEBUG_FILE),0755);
	$att[$DEBUG_FILE]="";

}


$list->att = $att;
$list->att_cid = $att_cid;

unset ($hit);unset ($poz);unset ($cid);unset ($tresc); unset ($msg); unset ($att); unset ($att_cid);



$to_arr=$SENDMAIL[to];
if (!is_array($to_arr)) $to_arr=array($to_arr);

if (count($to_arr)>10) $list->wait4flush=1;

for ($_to=0;$_to<count($to_arr);$_to++)
{
	if ( ($_to && !($_to%10000)) || $_to==count($to_arr)-1) $list->flush=1;

	$SENDMAIL[to]=$to_arr[$_to];

	foreach(array("from","to","subject","msg") AS $v)
	{
		$var=ereg_replace("-&gt;","->",$SENDMAIL[$v]);
		$var=addslashes(stripslashes($var));
		$str2eval="\$list->$v = \"$var\" ;";
		eval($str2eval);
	}

	foreach(array("cc","bcc") AS $v)
	{
		$a=$SENDMAIL[$v];
		if (!is_array($a)) continue;
		while (list($key,$val)=each($a))
		{
			$val=addslashes(stripslashes($val));
			$str2eval="\$a[$key] = \"$val\"; ";
			eval($str2eval);
		}
		$list->$v=$a;
	}
	
	if ($SENDMAIL[debug])
	{
		push($adodb,$_kameleon_vars,$valtab,$_str2eval_after_include,$HTTP_POST_VARS,$HTTP_GET_VARS,$MODULES);

		$adodb="";$_kameleon_vars="";$valtab=""; $_str2eval_after_include="";$MODULES="";
		$HTTP_POST_VARS=""; $HTTP_GET_VARS="";
		$v=var_export (get_defined_vars(),true);
		
		pop(&$adodb,&$_kameleon_vars,&$valtab,&$_str2eval_after_include,&$HTTP_POST_VARS,&$HTTP_GET_VARS,&$MODULES);

		$v=ereg_replace("\n","\r\n",$v);
		$f=fopen($DEBUG_FILE,"w");
		fwrite($f,$v);
		fclose($f);		
	}

	sendmail2($list);
//	print_r($list);
}

if ($SENDMAIL[debug]) system("rm -rf ".dirname($DEBUG_FILE));

?>
