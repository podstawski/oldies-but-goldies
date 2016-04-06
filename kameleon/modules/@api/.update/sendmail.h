<?
$xml="";

$costxt="";

$mod_action=$SENDMAIL[action];


$podstawa=array("to","from","debug","subject","interactive");
while( list($key_key,$key_val) = each($podstawa) )
{
	$costxt.=" <$key_val>";
	$costxt.=addslashes(htmlspecialchars(stripslashes($SENDMAIL[$key_val])));
	$costxt.="</$key_val>\n";		
}

$SENDMAIL[bcc]=ereg_replace("\r|,|;","\n",$SENDMAIL[bcc]);
$SENDMAIL[bcc]=ereg_replace("[\n]+","\n",$SENDMAIL[bcc]);
$SENDMAIL[cc]=ereg_replace("\r|,|;","\n",$SENDMAIL[cc]);
$SENDMAIL[cc]=ereg_replace("[\n]+","\n",$SENDMAIL[cc]);


$SENDMAIL[a_bcc]=explode("\n",$SENDMAIL[bcc]);
while( list($key_key,$key_val) = each($SENDMAIL[a_bcc]) )
{
	$costxt.=" <bcc>";
	$costxt.=addslashes(htmlspecialchars(stripslashes($key_val)));
	$costxt.="</bcc>\n";		
}

$SENDMAIL[a_cc]=explode("\n",$SENDMAIL[cc]);
while( list($key_key,$key_val) = each($SENDMAIL[a_cc]) )
{
	$costxt.=" <cc>";
	$costxt.=addslashes(htmlspecialchars(stripslashes($key_val)));
	$costxt.="</cc>\n";		
}


$costxt="<xml>\n$costxt</xml>";

$query="SELECT sid FROM webtd WHERE server=$SERVER_ID
		AND page_id=$page_id AND ver=$ver AND lang='$lang'
		AND pri=$pri ";
parse_str(ado_query2url($query));

$MODULE_PATH="modules/@".$MODULES->api->name;


include("$MODULE_PATH/.api/sendmail_const.h");
eval("\$cookieufpath = \"$CONST_SENDMAIL_UFILES\";");
@rmdir("$UFILES/$cookieufpath");

?>
