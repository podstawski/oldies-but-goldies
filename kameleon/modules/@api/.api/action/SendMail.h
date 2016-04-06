<?
$action = "";

global $UIMAGES,$HTTP_HOST, $ADD_INC;

if ($ADD_INC)
{
	global $subject, $mailfrom, $mailto, $kurs, $pytanie;
	$CONST_MAILER_LOOKUP_COND = $CONST_COURS_LOOKUP_COND;
}

include ("include/sendmail2.h");

$obj=new sendmail_obj;

$subject=addslashes(stripslashes($subject));

$query="SELECT plain,costxt,bgcolor, bgimg,sid 
		FROM webtd WHERE title='$subject'  
		AND $CONST_MAILER_LOOKUP_COND
		LIMIT 1";

parse_str(ado_query2url($query));

$obj->webtd_sid = $sid;

if (strlen($costxt) && !strlen($mailto))
	eval ("\$mailto=\"$costxt\";");



if (strlen($plain))	
{
	$UIMG=ereg_replace("\.\./","",$UIMAGES);
//	echo htmlspecialchars("1:$plain")."<br><br>";
	$dirplus=dirname($SCRIPT_NAME);
	$dirplus = ($dirplus==".") ? "" : "$dirplus/";
	$plain = eregi_replace("$UIMAGES","http://$HTTP_HOST/$dirplus$UIMG",$plain);	
//	echo htmlspecialchars("2:$plain");
	$msg=stripslashes($plain);
}

/*
echo "=== UIMAGES = $UIMAGES<br>";
echo "=== UIMAG = $UIMG<br>";
echo "=== HTTP_HOST = $HTTP_HOST<br>";
echo "=== http://$HTTP_HOST/$UIMG";
*/
if (strlen($bgcolor))
{
	$_bgcolor="bgcolor=\"#$bgcolor\"";
}
if (strlen($bgimg))
{
	$_bgimg="background=\"http://$HTTP_HOST/$UIMG/$bgimg\"";
}


$msg="<body $_bgcolor $_bgimg >$msg</body>";



$msg=addslashes($msg);
eval("\$msg_list=stripslashes(\"$msg\");");
eval("\$obj->to=\"$mailto\";");
eval("\$obj->from=\"$mailfrom\";");
eval("\$obj->subject=stripslashes(\"$subject\");");
$obj->precedence="Bulk";

if (is_array($mailcc)) $obj->cc=$mailcc;
if (is_array($mailbcc)) $obj->bcc=$mailbcc;

$obj->type="html";
$obj->msg="$msg_pre$msg_list";

if (is_array($bcc)) $obj->bcc=$bcc;

sendmail2($obj);

/*
echo "From: $obj->from<br>To: $obj->to<br>Subject: $obj->subject <br><br>";
echo nl2br(htmlspecialchars($msg_list));
echo "<br><br>";
*/
?>
