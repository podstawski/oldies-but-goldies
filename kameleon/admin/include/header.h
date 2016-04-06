<?
	$SERVER_ID="admin";
	//$PHP_AUTH_USER=$REMOTE_USER;

	if (is_object($auth_acl))
	{
		$users_exist=true;
	}
	else
	{
		$query="SELECT count(*) AS users_exist FROM passwd WHERE admin>0";
		parse_str(ado_query2url($query));
	}

	if ($users_exist) $kattab2[]=array(label("Billing"),"1","billing.php","SetAdmMenu","");
	if ($users_exist) $kattab2[]=array(label("Report"),"8","report.php","SetAdmMenu","");

	$link=is_object($auth_acl)?('../plugins/acl/index.php/user/return_href/'.base64_encode($_SERVER['REQUEST_URI'])):"kameleonusers.php";
	$param=is_object($auth_acl)?'':"SetAdmMenu";
	$kattab2[]=array(label("Users"),"2",$link,$param,"");

	$kattab2[]=array(label("Groups"),"5","kameleongroups.php","SetAdmMenu","");
	if ($users_exist) $kattab2[]=array(label("Servers"),"3","servers.php","SetAdmMenu","");
	//$kattab2[]=array(label("Multi-languages"),"4","multi.php","SetAdmMenu","");
	$kattab2[]=array(label("Version description")." $KAMELEON_VERSION","6","versions.php","SetAdmMenu","");
	if (strlen($login)) $kattab2[]=array("$login","21","kameleonusers.php","SetAdmMenu","");
	if ($adodb->debug()) $kattab2[]=array(label("SQL Analyzer"),"7","sql.php","SetAdmMenu","SetServer=&page=$referpage");
	if ($users_exist) $kattab2[]=array(label("Web Kameleon"),"8","../index.php","SetAdmMenu","SetServer=&page=$referpage");


$pre="<li class=\"km_item_n\">";
$post="</li>";

$apre="<li class=\"km_item_a\">";
$apost="</li>";

echo "<div class=\"km_mainmenu\"><ul>";
echo zakladki2($kattab2, 10, $SetAdmMenu , "$pre", "$post", "$apre", "$apost");
echo "</ul></div>";
?>
