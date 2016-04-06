<?
$prompt="Sk³adnia: u¿ytkownik@serwer";

function unauthorize($realm,$noauthpage)
{
        Header("WWW-Authenticate: Basic realm=\"$realm\"");
        Header("HTTP/1.0 401 Unauthorized");

        echo "<META
                HTTP-EQUIV='refresh'
                CONTENT='1;URL=$noauthpage'>";
	exit();
}

if ($KAMELEON_MODE)
{
	if (!strlen($PHP_AUTH_USER)) unauthorize("$prompt","login_pls.html");
	$_auth=explode("@",$PHP_AUTH_USER);
	$USERNAME=strtolower($_auth[0]);
	$SERVER=strtolower($_auth[1]);

	$query="SELECT password FROM passwd WHERE username='$USERNAME'";
	parse_str(ado_query2url($query));

	if ( $password!=$PHP_AUTH_PW ) unauthorize("$prompt","wrong_password.html");

	$query="SELECT * FROM servers WHERE nazwa='$SERVER'";
	$_server=ado_ObjectArray($adodb,$query);
	if (!is_array($_server) || !count($_server) ) unauthorize("$prompt","no_such_server.html");
	$SERVER=$_server[0];
	$SERVER_ID=$SERVER->id;
}
else
{
	$query="SELECT * FROM servers WHERE id=$SERVER_ID";
	$_server=ado_ObjectArray($adodb,$query);
	$SERVER=$_server[0];
}

?>
