<?
	$action="";

	$header=-5;
	$footer=-6;
	$c=0;
	$query="SELECT count(*) AS c FROM servers WHERE nazwa='$nazwa'";
	parse_str(ado_query2url($query));
	if ($c)  $error=label("Server name exists");
	if (strpos($nazwa," ")) $error=label("Server name should not contain space");


	$query="SELECT count(*) AS c FROM servers";
	parse_str(ado_query2url($query));

	$licence_over=label("License does not allow next server");
	if ($c>=$CONST_LICENSE_SERVERS) $error="$licence_over ($c>=$CONST_LICENSE_SERVERS)";


	if (strlen($error) ) return;

	//$grupa=$groupid;
	$grupa+=0;
	$query="INSERT INTO servers (nazwa,groupid,ftp_server,ver,lang,header,footer,editbordercolor) 
		VALUES ('$nazwa',$grupa,'$nazwa',1,'$lang',$header,$footer,'#B00000')";

//	echo nl2br($query);return;

	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		$query="SELECT max(id) AS server FROM servers";
		parse_str(ado_query2url($query));
		@mkdir("../uimages/$server",0755);
		$query="SELECT id AS server FROM servers WHERE nazwa='$nazwa'";
		parse_str(ado_query2url($query));

		$query="INSERT INTO rights (username,server,ftp,class)
			 VALUES ('$PHP_AUTH_USER',$server,1,1)";

		if ($adodb->Execute($query)) logquery($query) ;

	}

?>
