<?
$action="";
include ("$INCLUDE_PATH/sendmail2.h");

$obj=new sendmail_obj;
$obj->to="$mailto";
$obj->from="$mailfrom";
$obj->subject="$subject";
$obj->reply="$mailreply";


if (isset($attachment) && file_exists($attachment))
{
	$plik=fopen($attachment,"r");
	$binadata=fread( $plik, $attachment_size );
	fclose($plik);
	$obj->att[]=array($binadata,$attachment_type,$attachment_name);	
}

if (strlen($sendmail_action))
{
	$query="SELECT * FROM mailer WHERE action='$sendmail_action'";
	$query="SELECT * FROM mailer 
			WHERE action IN ('$sendmail_action','$sendmail_action:$C_AGENT')
			ORDER BY action DESC
			LIMIT 1";
	parse_str(query2url($query));
	$action="";
}

$msg=addslashes($msg);
eval("\$msg_list=\"$msg\";");

if (!strlen($obj->from)) $obj->from="$mailfrom";
eval("\$obj->to=\"$mailto\";");

if (is_array($mailcc)) $obj->cc=$mailcc;
if (is_array($mailbcc)) $obj->bcc=$mailbcc;

eval("\$obj->subject=\"$subject\";");
$obj->type="$type";
$obj->msg="$msg_list";

if (is_array($bcc)) $obj->bcc=$bcc;

sendmail2($obj);

//echo "From: $obj->from<br>To: $obj->to<br>";
//echo nl2br($msg_list);


?>