<?
$UNIQ="kjhsdf87y6843rgasuadytfuywegkjsadf8634tsddjhg";


include_once("$INCLUDE_PATH/sendmail_cid.h");

$att=$SENDMAIL[att];
$msg=stripslashes($SENDMAIL[msg]);
$list = new crm_sendmail_obj;
$list->type = "text";

if (!is_array($SENDMAIL[to]) )
	if ( !strlen($SENDMAIL[to]))
		$SENDMAIL[to]=$mailto;


if (strlen($sendmail_action))
{
	$query="SELECT * FROM mailer 
			WHERE action IN ('$sendmail_action','$sendmail_action:$C_AGENT')
			ORDER BY action DESC
			LIMIT 1";
	parse_str(query2url($query));
	$action="";

	$msg=stripslashes($msg);


	while ( is_array($EVALFIRST) && list ($k,$v) = each($EVALFIRST) )
	{
		$msg=ereg_replace("\\\$EVALFIRST\[$k\]","$v",$msg);

	
	}
	
	$msg=eregi_replace("http://kameleon[^/]+/","",$msg);
	$msg=eregi_replace("uimages/[0-9]+/[0-9]+/","$UNIQ/",$msg);
	$msg=eregi_replace("$UIMAGES","$UNIQ",$msg);
	$msg=eregi_replace("http://http://","http://",$msg);


	if (!strlen($SENDMAIL[from])) $SENDMAIL[from]=$mailfrom;
	$SENDMAIL[msg]=$msg;
	$SENDMAIL[subject]=$subject;

	$list->type = $type;
}



	
if ($list->type == "html")
{
	$hit = array();
	$tresc = $msg;
	while (strpos($tresc,$UNIQ) > 0)
	{	
		$poz = strpos($tresc,$UNIQ);
		$delim=$tresc[$poz-1];
		$tresc = substr($tresc,$poz+strlen($UNIQ));

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
	

		$img_path="$UNIQ/$img";
		$img_path=ereg_replace("[/]+","/",$img_path);
		$hit[$img_path] = 1;
	}

	
	while (count($hit) && list($img,$v)=each($hit))
	{
	
		$cid=base64_encode($img);
	//	echo "NOWY: $img => $cid <br>";
		$msg=ereg_replace("$img","cid:$cid",$msg);
		$img=ereg_replace($UNIQ,$UIMAGES,$img);
		$img=ereg_replace("[/]+","/",$img);
		$att_cid[$img] = "Content-ID: <$cid>\n";
	}

	$msg="<link href='cid:$UNIQ' rel='stylesheet' type='text/css'>\n$msg";
	$att_cid["$IMAGES/textstyle.css"]="Content-ID: <$UNIQ>\n";
}

$SENDMAIL[msg]=$msg;
$list->att = $att;
$list->att_cid = $att_cid;


$to_arr=$SENDMAIL[to];
if (!is_array($to_arr)) $to_arr=array($to_arr);




if (count($to_arr)>10) $list->wait4flush=1;

for ($_to=0;$_to<count($to_arr);$_to++)
{
	if ( ($_to && !($_to%10000)) || $_to==count($to_arr)-1) $list->flush=1;

	$SENDMAIL[to]=$to_arr[$_to];

	foreach(array("from","to","subject","msg","reply") AS $v)
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
	
	
	crm_sendmail($list);
}


?>
