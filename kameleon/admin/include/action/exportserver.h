<?
	$action="";
	set_time_limit(0);

	$query="SELECT nazwa AS s_nazwa,lang AS s_lang, ver AS s_ver, szablon AS s_szablon
			FROM  servers WHERE  id=$server";
	parse_str(ado_query2url($query));


	$tables=array();
	//AM jesli eksport pe³ny to tak¿e eksport danych servera
	if ($_GET['install']) 
	{
		$tables[]=array("servers:id", 'server');
	}

	$tables[]=array("webpage:server","nd_update","nd_ftp","server","fakro_header");
	$tables[]=array("class:server","server");
	$tables[]=array("weblink:server","menu_sid","server");
	$tables[]=array("webtd:server","server");


	$export_sql="";


	for ($d=0; $d<count($tables); $d++)
	{
		$t=$tables[$d];
		$_t=explode(":",$t[0]);
		
		$table=$_t[0];
		$index=$_t[1];

		$columns=$adodb->adodb->MetaColumns($table);


		$query="SELECT * FROM $table WHERE $index=$server";
		$res=$adodb->Execute($query);

		for ($r=0;$r<$res->RecordCount();$r++)
		{
			$fields=$index;
			$values=$CONST_EXPORT_SERVER_TOKEN;

			$res->Move($r);
			$data=$res->FetchRow();

			foreach ($columns AS $attr)
			{
				$type=$attr->type;
				if ( strstr($type,'char') || strstr($type,'text') || strstr($type,'date') || strstr($type,'time')) $type='a';
				else $type='i';
				$field=$attr->name;

				if (!strlen(trim($data[$field]))) continue;
				if (in_array($field,$t)) continue;
				if ($field=='sid') continue;

				$val=trim($data[$field]);
				$val=ereg_replace("uimages/$server/[0-9]+","uimages/$CONST_EXPORT_SERVER_TOKEN/$CONST_EXPORT_VER_TOKEN",$val);
				$val=ereg_replace("ufiles/$server-att","ufiles/$CONST_EXPORT_SERVER_TOKEN-att",$val);

				$fields.=",$field";
				$values.=",";
				if ($type!="i") $values.="'";
				$values.=addslashes($val);
				if ($type!="i") $values.="'";
			
			}
		

			$sql="INSERT INTO $table ($fields) VALUES ($values)";
			$sql=ereg_replace("\n",$CONST_EXPORT_NL_TOKEN,$sql);
			$export_sql.="$sql;\n";

		}

	}

	


	if (strlen($CONST_TEMP_DIR))
	{
		$temp_dir=$CONST_TEMP_DIR;
		if ($temp_dir[0]!="/") $temp_dir="../$temp_dir";
		if (!is_dir($temp_dir)) $temp_dir="";
	}
	else
		$temp_dir=$adodb->getSesionDir();


	$tmp=tempnam($temp_dir,"export_${server}_".time());
	@unlink($tmp);
	@mkdir ($tmp,0700);




	
	chdir("..");

	$filesIn .= "log/$CONST_EXPORT_SQL ";
	$f=fopen("log/$CONST_EXPORT_SQL","w");
	fwrite($f,$export_sql);
	fclose($f);

	$filesIn .= "log/$CONST_EXPORT_TRANSLATION_PHP ";
	$f=fopen("log/$CONST_EXPORT_TRANSLATION_PHP","w");
	fwrite($f,"<?\n");

	fwrite($f,"\$CONST_EXPORT_SQL='log/$CONST_EXPORT_SQL';\n");

	for ($sz_ver=$s_ver;$sz_ver ;$sz_ver-- )
	{
		if (is_dir("szablony/$s_szablon/$sz_ver")) break;
	}

	if (!$sz_ver) $sz_ver="";
	else $sz_ver="/$sz_ver";

	//AM w windows nie ma symlinków
	$filesIn .= "szablony/$s_szablon$sz_ver ";
	fwrite($f,"\$_szablon='szablony/$s_szablon$sz_ver';\n");

	for ($uimg_ver=$s_ver;$uimg_ver ;$uimg_ver-- )
	{
		if (is_dir("uimages/$server/$uimg_ver")) break;
	}


	$filesIn .= "uimages/$server/$uimg_ver ";
	fwrite($f,"\$_uimages='uimages/$server/$uimg_ver';\n");

	for ($uinc_ver=$s_ver;$uinc_ver ;$uinc_ver-- )
	{
		if (is_dir("uincludes/$s_nazwa/$uinc_ver")) break;
	}

	if (!$uinc_ver) $uinc_ver="";
	else $uinc_ver="/$uinc_ver";

	$uincludes="uincludes/$s_nazwa$uinc_ver";

	if (is_dir($uincludes))
	{
		$filesIn .= "$uincludes ";
		fwrite($f,"\$_uincludes='$uincludes';\n");
	}

	$ufiles="ufiles/$server-att";
	if (is_dir($ufiles))
	{
		$filesIn .= "$ufiles ";
		fwrite($f,"\$_ufiles='$ufiles';\n");
	}
	
	//AM tarujemy i gzipujemy
	//PP a mo¿e lepiej ZIP-owaæ

	fclose($f);

	$tar = "$tmp/kameleon.tar";
	$zip = "$tmp/kameleon.zip";

	$tarCommand = str_replace('{file}', $tar, CONST_TAR_EXE)." $filesIn";
	if (strlen(CONST_ZIP_EXE)) $zipCommand = str_replace('{file}', $zip, CONST_ZIP_EXE). " $tar";
	

	//AM pod windows trzeba odwróciæ znaczki katalogów
	if (CONST_WINDOWS) 
	{
		//$tarCommand = str_replace('/', "\\", $tarCommand);
		//$zipCommand = str_replace('/', "\\", $zipCommand);
	}


	@exec($tarCommand);
	@exec($zipCommand);


	
	if ($_GET['install']) 
	{
		//AM podczas instalacji przesy³amy jest plik [server_id].[nazwa].[ver].[ext]
		$name="$server.$s_nazwa.$s_ver.zip";
	} 
	else 
	{
		$name="$s_nazwa.zip";
		if (!file_exists($zip)) $name="$s_nazwa.tar";
	}

	if (!$_GET['install'] && is_writable($_SERVER["DOCUMENT_ROOT"]."/out"))
	{
		if (CONST_WINDOWS) 
		{
			rename($zip,$_SERVER["DOCUMENT_ROOT"]."/out/$name");
		}
		else
		{
			$name="$s_nazwa.tar";
			rename($tar,$_SERVER["DOCUMENT_ROOT"]."/out/$name");
		}
		$error=$name." ".label("is in public directory out");
	}
	else
	{
		$size=file_exists($zip)?filesize($zip):filesize($tar);

		Header("Content-Type: application/x-tar ; name=\"$name\"");
		Header("Content-Length: $size");
		Header("Content-Disposition: attachment; filename=\"$name\"");

		if (file_exists($zip)) readfile($zip);
		else readfile($tar);
	}

	@unlink("$tar");
	@unlink("$zip");
	@unlink("log/$CONST_EXPORT_SQL");
	@unlink("log/$CONST_EXPORT_TRANSLATION_PHP");

	@rmdir($tmp);


	if (!strlen($error)) exit();

?>