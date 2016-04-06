<?
	$action="";

	$temp_dir=dirname(dirname(dirname(dirname(__FILE__)))).'/log';

	

	if (is_array($_FILES) && count($_FILES))
	{
		$importfile = $_FILES['importfile']['tmp_name'];
		$importfile_name = $_FILES['importfile']['name'];
		$importfile_type = $_FILES['importfile']['type'];
	}


	if (isset($_GET['install'])) {
		//AM podczas instalacji przesy³amy jest plik [server_id].[nazwa].[ver].[ext]
		$fileData = explode('.', $importfile_name);
		$server = $fileData[0];
		$s_nazwa = $ServerName = $fileData[1];
		$s_ver = $fileData[2];
		
	} else {
		if (!$server) 
		{
			$query="SELECT id AS server FROM servers WHERE nazwa='$ServerName'";
			parse_str(ado_query2url($query));
		}
	}
	if (!$server) return;

	
	$query="SELECT count(*) AS c1 FROM webpage WHERE server=$server";
	parse_str(ado_query2url($query));
	$query="SELECT count(*) AS c2 FROM weblink WHERE server=$server";
	parse_str(ado_query2url($query));
	$query="SELECT count(*) AS c3 FROM webtd WHERE server=$server";
	parse_str(ado_query2url($query));

	if ($c1+$c2+$c3) $error="$ServerName ($server)".label("is not empty !. Delete all content from this server an try again.");
	if ($c1+$c2+$c3) return;

	$query="SELECT nazwa AS s_nazwa, ver AS s_ver
			FROM  servers WHERE  id=$server";
	parse_str(ado_query2url($query));

	$tmp=tempnam($temp_dir,"export_${server}_".time());
	@unlink($tmp);
	@mkdir($tmp,0755);

	//@rmdir($tmp);

	chdir("..");
	if (!is_writable("szablony")) $error=label("The template directory (szablony) is not writable");


	if (@filesize($importfile))
	{

		if ( !strstr($importfile_name,".tar") && !strstr($importfile_type,"zip") )
			$error=label("Avaliable format").": TAR";

		if ( strstr($importfile_type,"zip") && !strlen(CONST_UNZIP_EXE) )
			$error=label("Path to unzip not found").": " . CONST_UNZIP_EXE;
	}
	else
	{	
		if (substr($importfile,0,5)=='http:')
		{
			$f=@fopen("$importfile","r");
			if ( strstr($importfile,"zip") && !strlen(CONST_UNZIP_EXE) )
				$error=label("Path to unzip not found").": " . CONST_UNZIP_EXE;
			
			$importfile_type="tar";
			if ( strstr($importfile,"zip")) $importfile_type="zip";
		}
		else
		{
			if (file_exists("./out/$importfile.tar"))
				$f=@fopen("./out/$importfile.tar","r");
			else
				$f=@fopen("http://kameleon.gammanet.pl/out/$importfile.tar","r");
		
			$importfile_type="tar";
		}

		if (!$f) $error=label("Missing file");
		else
		{	
			$importfile=tempnam($temp_dir,"tar_${server}_".time());
			$fw=fopen($importfile,"w");

			while (1)
			{
				$tar=fread($f,1024);	
				if (!strlen($tar)) break;
				fwrite($fw,$tar);
			}

			fclose($fw);
			fclose($f);
		}
	}

	$pwd=getcwd();
	if (strlen($error)) return;


	chdir($tmp);
	if ( strstr($importfile_type,"zip") ) 
	{

		$cmd = str_replace('{file}', $importfile, CONST_UNZIP_UNTAR_EXE);

	} 
	elseif ( strstr($importfile_type,"zip") ) 
	{
		$cmd = str_replace('{file}', $importfile, CONST_UNZIP_UNTAR_EXE);
	} 
	else 
	{
		$cmd = str_replace('{file}', $importfile, CONST_UNTAR_EXE);
	}



	
	foreach (explode(';',$cmd) AS $c) exec($c);
	chdir($pwd);


	@unlink($importfile);

	$_szablon='szablony';
	$_uimages='uimages';
	$_uincludes='uincludes';
	$_ufiles='ufiles';


	if (file_exists("$tmp/log/$CONST_EXPORT_TRANSLATION_PHP")) include("$tmp/log/$CONST_EXPORT_TRANSLATION_PHP");
	if (file_exists("$tmp/$CONST_EXPORT_TRANSLATION_PHP")) include("$tmp/$CONST_EXPORT_TRANSLATION_PHP");


	
	

	if (is_dir("$tmp/$_szablon")) 
	{
		if (!is_dir("szablony/$s_nazwa/$s_ver"))
		{
			@mkdir("szablony/$s_nazwa",0755);

			rename("$tmp/$_szablon", "szablony/$s_nazwa/$s_ver");
		}
	}

	if (is_dir("$tmp/$_uimages")) 
	{
		if (!is_dir("uimages/$server/$s_ver"))
		{
			@mkdir("uimages/$server",0755);
			rename("$tmp/$_uimages", "uimages/$server/$s_ver");
		}
		else
		{
			rename("$tmp/$_uimages", "uimages/$server/$s_ver");
		}
	}

	if (is_dir("$tmp/$_ufiles")) 
	{
		if (!is_dir("ufiles/$server-att"))
		{
			rename("$tmp/$_ufiles", "ufiles/$server-att");
		}
	}



	if (is_dir("$tmp/$_uincludes")) 
	{
		
		
		if (!is_dir("uincludes/$s_nazwa/$s_ver"))
		{
			//AM niech pokazuje komunikat, ¿e siê nie uda³o utworzyæ katalogu
			mkdir("uincludes/$s_nazwa",0755);

			rename("$tmp/$_uincludes", "uincludes/$s_nazwa/$s_ver");
		}
	}




	if (file_exists("$tmp/$CONST_EXPORT_SQL"))
	{
		$sql=file("$tmp/$CONST_EXPORT_SQL");
		for ($i=0;$i<count($sql);$i++ )
		{
			$query=trim($sql[$i]);
			if (!strlen($query)) continue;
			$query=ereg_replace($CONST_EXPORT_SERVER_TOKEN,"$server",$query);
			$query=ereg_replace($CONST_EXPORT_VER_TOKEN,"$s_ver",$query);
			$query=ereg_replace($CONST_EXPORT_NL_TOKEN,"\n",$query);

			if (!$adodb->Execute($query))
			{
				push($SERVER_ID);
				$SERVER_ID="import";
				$lq=logquery($adodb->adodb->ErrorMsg()."\n\n".$query,'admin');
				$SERVER_ID=pop();

				$error=label("The SQL data format is incorrect").':'.$lq;
			}

		}
	}

   function remove_dir($dir) 
   { 
      $handle = opendir($dir); 
      while (false!==($item = readdir($handle))) 
       { 
          if($item != '.' && $item != '..') 
          { 
              if(is_dir($dir.'/'.$item))  
               { 
                   remove_dir($dir.'/'.$item); 
               }else{ 
                   unlink($dir.'/'.$item); 
               } 
           } 
       } 
       closedir($handle); 
       if(rmdir($dir)) 
       { 
           $success = true; 
       } 
       return $success; 
   } 

	remove_dir($tmp);
	chdir("admin");


	$query="UPDATE servers SET szablon=nazwa WHERE id=$server";
	if (!$adodb->Execute($query))
	{
	}
	else $sysinfo=label("Server successfully imported");

	$action='deleteduplicateidentyfiers';

