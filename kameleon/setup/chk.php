<?php

	if (!function_exists('is_executable'))
	{
		function is_executable($file)
		{
			return file_exists($file);
		}
	}


	if (file_exists("../const.php") || file_exists("../const.h"))
	{
		$label=label("System is configured");
		echo "<script>alert('$label');</script>";
		return;
	}

	// zrob const.php.sample w logu :)

//	$DATABASES[] = array("DB Name", "function", "adodbcode", "php_Exe_code", "shema_file");
	$DATABASES[] = array("PostgreSQL", "pg_Connect", "postgres", "pgsql", '../changes/postgres-schema-$KAMELEON_VERSION.sql');
//	$DATABASES[] = array("MYSQL", "mysql_Connect", "mysql", "mysql");
//	$DATABASES[] = array("Oracle", "ora_Connect", "oracle");


	$DEBUG = 0;

	$db_type_options = "";
	for ($i=0; $i<count($DATABASES); $i++)
		$db_type_options.= "<option value='".$DATABASES[$i][2]."'>".$DATABASES[$i][0]."</option>";

	define ('ADODB_DIR','../adodb/');
	@include_once(ADODB_DIR."../include/class/kdb.php");

	function createdb($C_DB_CONNECT_DBTYPE)
	{
		global $adodb, $DATABASES, $DEBUG;

		include("../include/const.h");

		for ($i=0; $i<count($DATABASES); $i++)
			if ($C_DB_CONNECT_DBTYPE == $DATABASES[$i][2])
				$filename = $DATABASES[$i][4];


		eval("\$filename=\"$filename\";");
		
		if (!file_exists($filename)) 
		{
			echo "<script>alert('".label("Missing file").": $filename');</script>";
			return 0;
		}

		$content = file($filename);

		for ($i=0; $i<count($content); $i++)
		{
			$content[$i] = trim($content[$i]);

//			if (eregi("", $content[$i])) continue;
			if (eregi("\connect -", $content[$i])) continue;
			if (eregi("^--", $content[$i])) continue;

			if (!strlen(trim($content[$i]))) continue;

			$content[$i] = ereg_replace(";$", "%%%", $content[$i]);

			$sql[] = $content[$i];
		}

		$sql = explode("%%%", implode(" ", $sql));
		$adodb->debug = $DEBUG;
		$adodb->BeginTrans();
		for ($i=0; $i<count($sql); $i++)
		{
			if (!strlen($sql[$i])) continue;
			if (!$res = $adodb->Execute($sql[$i]))
			{
				$adodb->RollBackTrans();
				return 0;
			}
		}
		$adodb->Execute("insert into kameleon (version,nd_issue,opis) values ($KAMELEON_VERSION,".time().",'setup')");
		$adodb->CommitTrans();
		$adodb->debug = 0;
	}

	if ($go_create)
	{
		$fd = @fopen("../log/const.php", "w");
		if ($fd)
		{

			fwrite($fd, "<?php\n\n");
			fwrite($fd, "// file created: ".date("d-m-Y H:m:s", time())."\n\n\n");
			fwrite($fd, "\$C_DB_CONNECT_DBTYPE=\"$db_type\";\n");
			fwrite($fd, "\$C_DB_CONNECT_HOST=\"$db_host\";\n");
			fwrite($fd, "\$C_DB_CONNECT_USER=\"$db_user\";\n");
			fwrite($fd, "\$C_DB_CONNECT_PASSWORD=\"$db_pass\";\n");
			fwrite($fd, "\$C_DB_CONNECT_DBNAME=\"$db_name\";\n\n");
			fwrite($fd, "\$PHP_EXE=\"$php_exe\";\n");
			fwrite($fd, "\$PHP_SUFFIX=\"$php_sufix\";\n\n");

			fwrite($fd, "\$API_SERVER=\"http://" . ereg_replace("/setup/.*", "/api", "$HTTP_HOST$SCRIPT_NAME") . "\";\n\n");

			if (strlen($temp_dir))
				fwrite($fd, "\$CONST_TEMP_DIR=\"$temp_dir\";\n\n");
			if (strlen($unzip_exe))
				fwrite($fd, "\$CONST_UNZIP_EXE=\"$unzip_exe\";\n\n");
			if (strlen($license_key))
				fwrite($fd, "\$CONST_LICENSE_KEY=\"$license_key\";\n\n");

			fwrite($fd, "\$persistant_connection=$persistant;\n\n");
				
			fwrite($fd, "?>");

			fclose($fd);

		} 
		else echo "<script>alert('".label("Unable to create default const.php file in")." ../log')</script>";
	}

	//sprawdzenie loadera

		if (!extension_loaded('ionCube Loader'))
		{
			$_ln='/ioncube/ioncube_loader_'.
				strtolower(substr(php_uname(),0,3)).
				'_'.substr(phpversion(),0,3).'.so';

			$_rd=str_repeat('/..',substr_count($_id=realpath(ini_get('extension_dir')),'/')).
				dirname(__FILE__).'/';

			$_i=strlen($_rd);

			while($_i--)
			{
				if($_rd[$_i]=='/')
				{
					$_lp=substr($_rd,0,$_i).$_ln;
					if(file_exists($_id.$_lp))
					{
						$_ln=$_lp;
						break;
					}
				}
			}
//			@dl($_ln);
		}

		if (function_exists('_il_exec'))
		{
			return _il_exec();
		}

		$library = basename($_ln);

		ob_start();
		phpinfo();
		$tmp = ob_get_contents();
		ob_end_clean();

		preg_match_all("/[a-z,A-Z,0-9,\/\_\-\.]+php.ini/", $tmp, $arr);

		$phpini = $arr[0][0];

		$loader = str_replace("setup","",dirname($SCRIPT_FILENAME))."loaders/$library";


		if (strlen($library))
		{
		}

		if (strlen($library))
		{
			$encoded_title =  label("webkameleon has been encoded!");
			if (file_exists($loader))
			{
				$encoded_text  = label("There is an appropirate loader in loaders subdirectory:")." $library<br>";
				$encoded_text .= label("To run loader please copy line below into Your")." <i>".$phpini."</i> ".label("file").": <br>";
				$encoded_text .= "<b>zend_extension=$loader</b><br>";
				$encoded_text .= label("and restart Your web server")."<br>";
			}
			else
			{
				$encoded_text = label("webkameleon has been encoded with ionCube PHP Encoder and requires the free")." <a href=\"http://www.ioncube.com/loader/\">ionCube PHP Loader</a>";
			}

			$RESULT["loader"]["ionCube"][0] = 0;
			$RESULT["loader"]["ionCube"][1] = $encoded_text;
		}
		else
			$RESULT["loader"]["ionCube"][0] = 1;

	//sprawdzanie php.ini
	//register_globals, max_excution_time, allow_url_fopen, short_open_tag


	/*	$chk = ini_get("register_globals");
		if ($chk==1 && strcasecmp($chk,"on")!=0)
			$RESULT["php.ini"]["register_globals"][0] = 1;
		else
		{
			$RESULT["php.ini"]["register_globals"][0] = 0;
			$RESULT["php.ini"]["register_globals"][1] = label("set register_globals to on");
		}
	*/


		$chk = ini_get("short_open_tag");
		if ($chk==1 && strcasecmp($chk,"on")!=0)
			$RESULT["php.ini"]["short_open_tag"][0] = 1;
		else
		{
			$RESULT["php.ini"]["short_open_tag"][0] = 0;
			$RESULT["php.ini"]["short_open_tag"][1] = label("set short_open_tag to on");
		}


		$chk = ini_get("register_argc_argv");
		if ($chk==1 && strcasecmp($chk,"on")!=0)
			$RESULT["php.ini"]["register_argc_argv"][0] = 1;
		else
		{
			$RESULT["php.ini"]["register_argc_argv"][0] = 0;
			$RESULT["php.ini"]["register_argc_argv"][1] = label("set register_argc_argv to on");
		}


		$chk = ini_get("max_execution_time");
		if ($chk<1800)
		{
			$RESULT["php.ini"]["max_excution_time"][0] = -1;
			$RESULT["php.ini"]["max_excution_time"][1] = label("set max_execution_time").": ".label("at least")." 1800, ".label("currently").": $chk";
		}
		else
			$RESULT["php.ini"]["max_excution_time"][0] = 1;


	//sprawdzanie bibliotek
	// pgsql, ftp, xml, db

		for ($i=0; $i<count($DATABASES); $i++)
		{
			$db_code = $DATABASES[$i][0];
			if (function_exists($DATABASES[$i][1]))
				$RESULT["libraries"][$db_code][0] = 1;
			else
			{
				$RESULT["libraries"][$db_code][0] = 0;
				$RESULT["libraries"][$db_code][1] = label("install reqiured db library").$DATABASES[$i][0];
			}
		}


		if (function_exists("ftp_connect"))
			$RESULT["libraries"]["ftp"][0] = 1;
		else
		{
			$RESULT["libraries"]["ftp"][0] = 0;
			$RESULT["libraries"]["ftp"][1] = label("compile: --enable-ftp");
		}
		
		if (function_exists("xml_set_element_handler"))
			$RESULT["libraries"]["xml"][0] = 1;
		else
		{
			$RESULT["libraries"]["xml"][0] = -1;
			$RESULT["libraries"]["xml"][1] = label("compile: --enable-xml");
		}

		if (function_exists("dbmopen"))
			$RESULT["libraries"]["db"][0] = 1;
		else
		{
			$RESULT["libraries"]["db"][0] = -1;
			$RESULT["libraries"]["db"][1] = label("compile: --with-db");
		}



	//sprawdzanie const.h

	if (!file_exists("../const.h") && !file_exists("../log/const.php") && !file_exists("../const.php"))
	{
		$RESULT["configuration"]["const_exist"][0] = 0;
		$RESULT["configuration"]["const_exist"][1] = label("No configuration file, enter configuration data:")."
		 <br>
		<form method='POST'  action=\"$SCRIPT_NAME\">
			<input type=hidden name=go_create value=1>
			<table>
				<tr><td class='k_td'>".label("db_type")."</td><td><select name=db_type  class='k_select'>$db_type_options</select></td></tr>
				<tr><td class='k_td'>".label("db_host:db_port")."</td><td><input type=text name=db_host class='k_input' size=50></td></tr>
			<!--	<tr><td class='k_td'>".label("db_port")."</td><td><input type=text name=db_port class='k_input' size=50></td></tr> -->
				<tr><td class='k_td'>".label("db_name")."</td><td><input type=text name=db_name class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("db_user")."</td><td><input type=text name=db_user class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("db_pass")."</td><td><input type=password name=db_pass class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("php_exe")."</td><td><input type=text name=php_exe class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("php_sufix")."</td><td><input type=text name=php_sufix class='k_input' value='>/dev/null 2>/dev/null &' size=50></td></tr>
				<tr><td class='k_td'>".label("unzip_exe")."</td><td><input type=text name=unzip_exe class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("temp_dir")."</td><td><input type=text name=temp_dir class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("license_key")."</td><td><input type=text name=license_key class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("persistant_connection")."</td><td class='k_td'>".label("yes").": <input type=radio name=persistant value=1> ".label("no").":<input type=radio name=persistant value=0 checked></td></tr>
				<tr><td class='k_td' colspan=2><input type=submit class='k_button' value='".label("Save")."'></td></tr>
			</table>
		</form>";
	}
	else
	{
		if (file_exists("../const.php"))
		{
			$RESULT["configuration"]["const_exist"][0] = 1;
		}
		else
		{
			$RESULT["configuration"]["const_exist"][0] = -1;
			$RESULT["configuration"]["const_exist"][1] = "log/const.php <b>if this file is ok then copy it: cp log/const.php .</b><br>";
		}

		if (file_exists("../const.php")) { include("../const.php"); $is_const = 1; }
		else { include("../log/const.php"); $is_const = 0; }

		if (!strlen($PHP_SUFFIX)) $PHP_SUFFIX=" >/dev/null 2>/dev/null &";


		$_C_DB_CONNECT_HOST = $C_DB_CONNECT_HOST;
		$C_DB_CONNECT_HOST = explode(":", $C_DB_CONNECT_HOST);
		if (strlen($C_DB_CONNECT_HOST[1])) $C_DB_CONNECT_PORT = $C_DB_CONNECT_HOST[1];
		else $C_DB_CONNECT_PORT = 5432;

		$configuration_file = 1;

/*
		if (!$is_const)
		{
			$RESULT["configuration"]["const_sample"][0] = -1;
			$RESULT["configuration"]["const_sample"][1] = label("this is sample file - if it is ok than copy it from log directory into main kamaleon dorectory");
		}
*/

//		$CONST_DB_CONNECT=" host=".$C_DB_CONNECT_HOST[0]." port=".$C_DB_CONNECT_PORT." user=".$C_DB_CONNECT_USER." password=".$C_DB_CONNECT_PASSWORD." dbname=".$C_DB_CONNECT_DBNAME;
//		$db = pg_Connect($CONST_DB_CONNECT);
		$adodb = &ADONewConnection($C_DB_CONNECT_DBTYPE);
		@$adodb->Connect($_C_DB_CONNECT_HOST, $C_DB_CONNECT_USER, $C_DB_CONNECT_PASSWORD, $C_DB_CONNECT_DBNAME);


		if (!is_object($adodb) || !$adodb->_connectionID)
		{
			$configuration_file = 0;
			$RESULT["configuration"]["dbconnect"][0] = 0;
			$RESULT["configuration"]["dbconnect"][1] = label("no conection to database");
		}
		else
			$RESULT["configuration"]["dbconnect"][0] = 1;

		$_PHP_EXE=strpos($PHP_EXE, " ") ? substr($PHP_EXE, 0, strpos($PHP_EXE, " ")) : $PHP_EXE;
		if (!is_executable($_PHP_EXE))
		{
			$configuration_file = -1;
			$RESULT["configuration"]["php"][0] = -1;
			$RESULT["configuration"]["php"][1] = $_PHP_EXE." ".label("is not executable");
			$p=popen("whereis php","r");
			if ($p)
			{
				$txt=fread($p,10000);
				pclose($p);
				$RESULT["configuration"]["php"][1].="<br><b>whereis</b> $txt";
			}
		}
		else
			$RESULT["configuration"]["php"][0] = 1;

		if (strlen($CONST_UNZIP_EXE))
		{
			if (!is_executable($CONST_UNZIP_EXE))
			{
				$configuration_file = -1;
				$RESULT["configuration"]["_unzip_exe"][0] = -1;
				$RESULT["configuration"]["_unzip_exe"][1] = $CONST_UNZIP_EXE.label(" is not executable");

				$p=popen("whereis unzip","r");
				if ($p)
				{
					$txt=fread($p,10000);
					pclose($p);
					$RESULT["configuration"]["_unzip_exe"][1].="<br><b>whereis</b> $txt";
				}

			}
			else
				$RESULT["configuration"]["_unzip_exe"][0] = 1;
		}
		else
		{
			$configuration_file = -1;
			$RESULT["configuration"]["_unzip_exe"][0] = -1;
			$RESULT["configuration"]["_unzip_exe"][1] = "\$CONST_UNZIP_EXE".label(" is not defined - option will not be enabled");
		}

		if (!strlen($CONST_LICENSE_KEY))
		{
//			$configuration_file = 0;
			$RESULT["configuration"]["license"][0] = -1;
			$RESULT["configuration"]["license"][1] = label("no license key found - system will work in trial mode");
		}
		else
			$RESULT["configuration"]["license"][0] = 1;


		$FORM = "<form method='POST' action=\"$SCRIPT_NAME\">
			<input type=hidden name=go_create value=1>
			<table>
				<tr><td class='k_td'>".label("db_type")."</td><td><select name=db_type class='k_select'>$db_type_options</select></td></tr>
				<tr><td class='k_td'>".label("db_host:db_port")."</td><td><input type=text name=db_host value='$_C_DB_CONNECT_HOST' class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("db_name")."</td><td><input type=text name=db_name value='$C_DB_CONNECT_DBNAME' class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("db_user")."</td><td><input type=text name=db_user value='$C_DB_CONNECT_USER' class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("db_pass")."</td><td><input type=password name=db_pass value='$C_DB_CONNECT_PASSWORD' class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("php_exe")."</td><td><input type=text name=php_exe value='$PHP_EXE' class='k_input' size=50></td></tr>
				<tr><td class='k_td'>".label("php_sufix")."</td><td><input type=text name=php_sufix class='k_input' value='$PHP_SUFFIX' size=50></td></tr>
				<tr><td class='k_td'>".label("unzip_exe")."</td><td><input type=text name=unzip_exe class='k_input' value='$CONST_UNZIP_EXE' size=50></td></tr>
				<tr><td class='k_td'>".label("temp_dir")."</td><td><input type=text name=temp_dir class='k_input' value='$CONST_TEMP_DIR' size=50></td></tr>
				<tr><td class='k_td'>".label("license_key")."</td><td><input type=text name=license_key class='k_input' value='$CONST_LICENSE_KEY' size=50></td></tr>
				<tr><td class='k_td'>".label("persistant_connection")."</td><td class='k_td'>".label("yes").": <input type=radio name=persistant value=1> ".label("no").":<input type=radio name=persistant value=0 checked></td></tr>
				<tr><td class='k_td' colspan=2><input type=submit class='k_button' value='".label("Save")."'></td></tr>
			</table>
		</form>";


		if ($configuration_file<1)
		{
			$db_host_port = $C_DB_CONNECT_HOST[0];
			if (strlen($C_DB_CONNECT_PORT)) $db_hodt_port.= ":$C_DB_CONNECT_PORT";

//			$RESULT["configuration"]["configuration_file"][0] = ($RESULT["configuration"]["dbconnect"][0]?$configuration_file:$RESULT["configuration"]["dbconnect"][0]);
			$RESULT["configuration"]["configuration_file"][0] = -1;
			$RESULT["configuration"]["configuration_file"][1] = label("Configuration file from").": ";
			if ($is_const>0)
				$RESULT["configuration"]["configuration_file"][1].= "const.php<br>";
			else
				$RESULT["configuration"]["configuration_file"][1].= "log/const.php <b>if this file is ok then copy it: cp log/const.php .</b><br>";

			$RESULT["configuration"]["configuration_file"][1].= $FORM;
		}
		else $RESULT["configuration"]["configuration_file"][0] = 1;
	}

	//sprawdzanie polaczenia do bazy danych


	//sprawdzanie praw dostepu
	// tmp, uimages, ufiles

		$TEMP_DIR=(strlen(trim($CONST_TEMP_DIR))?$CONST_TEMP_DIR:"/tmp");
		if (!@$fd=fopen("$TEMP_DIR/setup.tmp", "w"))
		{
			$RESULT["filesystem"]["$TEMP_DIR"][0] = 0;
			$RESULT["filesystem"]["$TEMP_DIR"][1] = label("unable to write to ").$TEMP_DIR;
		}
		else
		{
			fclose($fd);
			unlink("$TEMP_DIR/setup.tmp");
			$RESULT["filesystem"]["$TEMP_DIR"][0] = 1;
		}

		if (!@$fd=fopen("../uimages/setup.tmp", "w"))
		{
			$RESULT["filesystem"]["uimages"][0] = 0;
			$RESULT["filesystem"]["uimages"][1] = label("unable to write to ")."uimages";
		}
		else
		{
			fclose($fd);
			unlink("../uimages/setup.tmp");
			$RESULT["filesystem"]["uimages"][0] = 1;
		}

		if (!@$fd=fopen("../ufiles/setup.tmp", "w"))
		{
			$RESULT["filesystem"]["ufiles"][0] = 0;
			$RESULT["filesystem"]["ufiles"][1] = label("unable to write to ")."ufiles";
		}
		else
		{
			fclose($fd);
			unlink("../ufiles/setup.tmp");
			$RESULT["filesystem"]["ufiles"][0] = 1;
		}


		if (!@$fd=fopen("../log/setup.tmp", "w"))
		{
			$RESULT["filesystem"]["log"][0] = 0;
			$RESULT["filesystem"]["log"][1] = label("unable to write to ")."log";
		}
		else
		{
			fclose($fd);
			unlink("../log/setup.tmp");
			$RESULT["filesystem"]["log"][0] = 1;
		}


		if (!@$fd=fopen("/dev/null", "w"))
		{
			$RESULT["filesystem"]["/dev/null"][0] = -1;
			$RESULT["filesystem"]["/dev/null"][1] = label("unable to write to ")."/dev/null";
		}
		else
		{
			fclose($fd);
			$RESULT["filesystem"]["/dev/null"][0] = 1;
		}


		if (!@$fd=fopen("../szablony/setup.tmp", "w"))
		{
			$RESULT["filesystem"]["templates"][0] = -1;
			$RESULT["filesystem"]["templates"][1] = label("unable to write to ")."../szablony";
		}
		else
		{
			fclose($fd);
			$RESULT["filesystem"]["templates"][0] = 1;
		}


	//sprawdzanie md5

		if (!function_exists("md5_file"))
		{
			function md5_file($file)
			{
				$fd = fopen($file,"r");
				$content = fread($fd, filesize($file));
				fclose($fd);
				return md5($content);
			}
		}

		include("kameleon.md5");
		$return=0;
		if (is_array($kameleon_md5)) while (list($key, $val)=each($kameleon_md5))
		{
			//echo "$key -> $val <br>";
			$file = "../".$key;

			if (!file_exists($file))
			{
				$RESULT["md5"][$key][0] = -1;
				$RESULT["md5"][$key][1] = label("no file ").$key;
			}
			else
				if (md5_file($file)!=$val)
				{
					$RESULT["md5"][$key][0] = 0;
					$RESULT["md5"][$key][1] = label("file")." $key".label(" corrupted");
					$return=1;
				}
				else
					$RESULT["md5"][$key][0] = 1;
		}
		if ($return) return;


	// sprawdzenie php_exe


	if (is_executable(substr($PHP_EXE, 0, strpos($PHP_EXE, " "))))
	{
		ob_start();
		system(substr($PHP_EXE, 0, strpos($PHP_EXE, " "))." -m");
		$tmp = explode("\n", ob_get_contents());
		ob_end_clean();

		for ($i=0; $i<count($DATABASES); $i++)
		{
			$db_code = $DATABASES[$i][0];
			if (in_array($DATABASES[$i][3], $tmp))
				$RESULT["_php_exe"]["php_".$db_code][0] = 1;
			else
			{
				$RESULT["_php_exe"]["php_".$db_code][0] = 0;
				$RESULT["_php_exe"]["php_".$db_code][1] = label("install ").$DATABASES[$i][0];
			}
		}

		if (in_Array("ftp", $tmp))
			$RESULT["_php_exe"]["php_ftp"][0] = 1;
		else
		{
			$RESULT["_php_exe"]["php_ftp"][0] = 0;
			$RESULT["_php_exe"]["php_ftp"][1] = label("compile: --enable-ftp");
		}

		if (in_Array("ionCube Loader", $tmp))
			$RESULT["_php_exe"]["php_ioncube"][0] = 1;
		else
		{
			$RESULT["_php_exe"]["php_ioncube"][0] = 0;
			$RESULT["_php_exe"]["php_ioncube"][1] = label("install ioncCube");
		}

		if (in_Array("xml", $tmp))
			$RESULT["_php_exe"]["php_xml"][0] = 1;
		else
		{
			$RESULT["_php_exe"]["php_xml"][0] = -1;
			$RESULT["_php_exe"]["php_xml"][1] = label("compile: --enable-xml");
		}

		
		if (in_Array("db", $tmp))
			$RESULT["_php_exe"]["php_db"][0] = 1;
		else
		{
			$RESULT["_php_exe"]["php_db"][0] = -1;
			$RESULT["_php_exe"]["php_db"][1] = label("compile: --with-db");
		}
	}

	// sprawdzanie bazy danych (zakladanie)
//	$RESULT["configuration"]["dbconnect"][0] = 1;


	if ($RESULT["configuration"]["dbconnect"][0] == 1 && $RESULT["loader"]["ionCube"][0] == 1)
	{
		if (!$adodb->Execute("select * from kameleon limit 1"))
		{
			$nodb = 1;
			if (isSet($go_create_db))
				if (createdb($C_DB_CONNECT_DBTYPE)) $nodb=0;

			if ($nodb)
			{
				$RESULT["database"]["tables"][0] = 0;
				$RESULT["database"]["tables"][1] = label("no tables in database")."
					<form method='POST'>
						<input type=submit class='k_button' value='".label("create db")."' name=go_create_db>
					</form>\n";
			}
			else
				$RESULT["database"]["tables"][0] = 1;
		}
		else
		{
			$RESULT["database"]["tables"][0] = 1;
		}
	}
	if (is_Object($adodb))
		$adodb->Close();
  	
?>
