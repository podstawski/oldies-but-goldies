<?
if (!strlen($API_SERVER)) return;

$apipre="$INCLUDE_PATH/apipre.h";
if (file_exists($apipre)) include ("$apipre");

$url="api_href=$PHP_SELF";


$api_email=urlencode($AUTH_EMAIL);
$api_osoba=urlencode($AUTH_NAME);

$url.="&api_mode=$cos&api_size=$size&api_next=$next&page=$page&api_email=$api_email&api_osoba=$api_osoba&api_km=$KAMELEON_MODE&api_em=$editmode&api_lang=$lang&api_ver=$ver&api_more=$more";
global $_COOKIE;
if (strlen($_COOKIE["WKSESSID"])) $url.='&WKSESSID='.$_COOKIE["WKSESSID"];

$_api_server=parse_url($API_SERVER);

$_api_server['path'].="/api.php";
if (!isset($_api_server['port'])) $_api_server['port']=80;

global $_REQUEST;
$_REQUEST['sid']=$sid;
$req=base64_encode(serialize($_REQUEST));


$fp = @fsockopen ($_api_server['host'], $_api_server['port'], $errno, $errstr, 30);
$header = "POST ".$_api_server['path']." HTTP/1.0\r\n";
$header .= "Host: ".$_api_server['host']."\r\n";
$header .= "Connection: close\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";

$post="$api_key&$url&api_req=$req";
$body = "Content-Length: ".strlen( $post )."\r\n\r\n";

fputs ($fp, $header . $body . $post);

// echo "<pre>$header$body$post</pre>";


$txt='';
$header_end=false;
$t=time();
while (!feof($fp))
{
        $line=fgets ($fp, 4096);
        if (!strlen(trim($line))) $header_end=true;
        if($header_end) $txt.=$line;
}

fclose ($fp);
echo $txt;
