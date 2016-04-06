<?
	$action="";


	// uwaga moze nie kopiowac naglowkow dla starych serwisow
	if (!$version) $version=1;

	$hide_identity+=0;
	if (!$server) return;

        $c=0;
        $query="SELECT count(*) AS c FROM servers WHERE nazwa='$nazwa' AND id<>$server";
        parse_str(ado_query2url($query));
        if ($c)  $error=label("Server name exists");
	if (strpos($nazwa," ")) $error=label("Server name should not contain space");
	if (strlen($error) ) return;

	if (!$ver) $ver="1";
	if (!$header) $header="NULL";
	if (!$footer) $footer="NULL";
	$hide_identity+=0;
	$f_grupa+=0;
	$query = " SELECT nazwa AS nazwaold FROM servers WHERE id=$server";
	parse_str(ado_query2url($query));

	if (strlen($szablon))
		$u_szablon="szablon='$szablon',";
	else
		$u_szablon="";

	$versions+=0;

	if ($szablon[0]==':')
	{
		if (!file_exists("../szablony/$nazwa/$version"))
		{
			cp_r("../szablony.def/".substr($szablon,1),"../szablony/$nazwa/$version" );
			$u_szablon="szablon='$nazwa',";
		}
		else
			$u_szablon="";
		
	}

	$svn=addslashes($svn);

	$query="UPDATE servers SET
			nazwa='$nazwa',
			ftp_pass='$ftp_pass',
			ftp_user='$ftp_user',
			ftp_server='$ftp_server',
			$u_szablon
			ver=$version,
			editbordercolor='$editbordercolor',
			lang='$slang',
			ftp_dir='$ftp_dir',
			file_ext='$file_ext',
			http_url='$http_url',
			hide_identity=$hide_identity,
			groupid=$f_grupa,
			svn='$svn',versions=$versions
		  WHERE id=$server";
		
	//echo nl2br($query);return;

	
	if ($adodb->Execute($query)) 
	{
		logquery($query) ;
		if ($nazwa!=$nazwaold)
		{
			$query = "
		     	UPDATE services SET servername='$nazwa' WHERE servername='$nazwaold';
		     	UPDATE kontakt SET servername='$nazwa' WHERE servername='$nazwaold';
		     	UPDATE ksiega SET servername='$nazwa' WHERE servername='$nazwaold';
		     	UPDATE ogloszenia SET servername='$nazwa' WHERE servername='$nazwaold';
		    	UPDATE search_desc SET servername='$nazwa' WHERE servername='$nazwaold';
		     	UPDATE search_index SET servername='$nazwa' WHERE servername='$nazwaold';
				UPDATE search_slownik SET servername='$nazwa' WHERE servername='$nazwaold';
	     		UPDATE webaktual SET servername='$nazwa' WHERE servername='$nazwaold';
	     		UPDATE polecam SET servername='$nazwa' WHERE servername='$nazwaold';
	     		UPDATE forum SET servername='$nazwa' WHERE servername='$nazwaold';
	     		UPDATE counter SET servername='$nazwa' WHERE servername='$nazwaold';
			";
			$adodb->Execute($query);
		}
		$srcversion=$version-1;
		if (!file_exists("../uimages/$server/$version"))
		{
			@mkdir("../uimages/$server",0755);
			@mkdir("../uimages/$server/$version",0755);
		}

		if (!file_exists("../uimages/$server/$version") &&
			file_exists("../uimages/$server/$srcversion"))
		{
			cp_r("../uimages/$server/$srcversion","../uimages/$server/$version");
		}
		
	}
?>
