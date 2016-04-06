<?
	$action="";

	
	$sql="SELECT server FROM rights WHERE rights.username='$login'";
	$res=$adodb->Execute($sql);
	$query="";
	$ile = $res->RecordCount();
	for ($i=0;$i<$ile;$i++)
	{
		parse_str(ado_ExplodeName($res,$i));

		$ftp[$server]=($ftp[$server])?1:0;
		$class[$server]=($class[$server])?1:0;
		$basic[$server]=($basic[$server])?1:0;
		$acl[$server]=($acl[$server])?1:0;
		$accesslevel[$server]+=0;
		$template[$server]+=0;

		$exp=",nexpire=NULL";
		if (strlen(trim($expire[$server]))) $exp=",nexpire='".FormatujDateSql(trim($expire[$server]))."'";

		$query.="UPDATE rights 
 			    SET pages='".$_POST['pages'][$server]."',menus='".$menus[$server]."',
				class=".$class[$server].",ftp=".$ftp[$server].",basic=".$basic[$server].",
				proof='".$proof[$server]."',acl=$acl[$server],accesslevel=$accesslevel[$server],template=$template[$server]
				$exp
			    WHERE username='$login' AND server=$server;\n";
	
		//echo "$query <br><br><br>";

	}
	if ($NewRight)
	{
		$_query="SELECT count(*) AS c FROM rights WHERE username='$login' 
			 AND server=$NewRight";
		parse_str(ado_query2url($_query));
		
		if (!$c) $query.="INSERT INTO rights (username,server)	
				  VALUES ('$login',$NewRight)";
	}

	
	
		
	//echo nl2br($query);return;
	if ($adodb->Execute($query)) logquery($query) ;


?>
