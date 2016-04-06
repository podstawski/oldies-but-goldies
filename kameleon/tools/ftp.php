<?php

	$FTP_DEBUG=0;

	$startat=0;
	if ($FTP_DEBUG) system("echo \"poczatek ".time()."\" >>/tmp/kam_time");

	if (file_exists('../const.php')) include('../const.php'); else include('../const.h');

	for ($i=2;$i<$argc;$i++) parse_str($argv[$i]);
	
	while ($startat>time()) sleep(10);


	if (!is_object($adodb))
	{
		define ('ADODB_DIR',"../adodb/");
		include ("../include/adodb.h");
	}
	else
	{
		global $kameleon;
		
	}


	include ("../include/const.h");
	include_once ("../include/kameleon.h");
	include_once ("../include/fun.h");
	include_once ("../include/ftpfun.h");
	@include_once ("../incuser/convert.h");
	@include_once ("../include/webver.h");



	if ($FTP_DEBUG) system("echo \"bazaok ".time()."\" >>/tmp/kam_time");

	if (!function_exists('file_put_contents'))
	{
		function file_put_contents($name,&$bin)
		{
			$plik=fopen($name,'w');
			fwrite($plik,$bin);
			fclose($plik);
		}
	}

		

	global $FTP_ID; 

	$FTP_ID=$argv[1];
	$ftp_port=21;

	for ($i=2;$i<$argc;$i++) parse_str($argv[$i]);
	
	if (!$limitimages && !strlen($limitpage) && !$limitinc && !$limitatt)  $nolimit=1;
	if (strlen($limitpage)) $limitpage+=0;
	
	if (!$FTP_ID) return;

	$CMS_API_HOST="CMS_API_HOST=".urlencode($CMS_API_HOST);

	$query="SELECT * FROM ftp WHERE id=$FTP_ID";
	parse_str(ado_query2url($query));



	if(!$FTP_DEBUG) 
	if (!$id || $t_begin ) 
	{
		$adodb->Close();
		return;
	}
	$AUTH_PHP_USER=$username;

	$kameleon->lang=$lang;
	$kameleon->ver=$ver;
	$kameleon->server=$server;

	$kameleon->user[username]=$username;


	if (CONST_WINDOWS) if (!strlen($CONST_TEMP_DIR)) $CONST_TEMP_DIR='log';
	$temp_dir="";
	if (strlen($CONST_TEMP_DIR))
	{
		$temp_dir=$CONST_TEMP_DIR;
		if ($temp_dir[0]!="/") $temp_dir="../$temp_dir";

		if (!is_dir($temp_dir)) $temp_dir="";
	}



	$query="SELECT pages AS rights_pages FROM rights WHERE server=$server AND username='$username'";
	parse_str(ado_query2url($query));



	$query="SELECT * FROM servers WHERE id=$server";
	$_server=ado_ObjectArray($adodb,$query);


	if (ftp_time2bekilled()) return;


	if (!is_Array($_server)) return;
	$SERVER=$_server[0];
	$SERVER_ID=$SERVER->id;
	$SERVER_NAME=$SERVER->nazwa;
	$file_ext=$SERVER->file_ext;
	if (!strlen($file_ext)) $file_ext=$KAMELEON_EXT;
	$SERVER->file_ext=$file_ext;
	$nazwa=$SERVER->ftp_server;
	$ftp_user=$SERVER->ftp_user;
	$ftp_pass=$SERVER->ftp_pass;
	$ftp_dir=$SERVER->ftp_dir;
	$szablon=$SERVER->szablon;

	$kameleon->current_server->versions=$SERVER->versions;
	
	$http_url=$SERVER->http_url;
	if (strlen($http_url) && $http_url[strlen($http_url)-1]!='/') $http_url.='/';
	$http_url_dir=eregi_replace("^http[s]*://[^/]+","",$http_url);


	ftp_begin();

	$ftp_server=$nazwa;
	$slash=strpos($nazwa,"/");
	$tylda=strpos($nazwa,"~");
	if ($slash)
	{
		$ftp_server=substr($nazwa,0,$slash);
		$ftp_=substr($nazwa,$slash+1);
		if ($ftp_dir) $ftp_dir.="/";
		if (!$tylda)
		{
			$ftp_dir.="$ftp_chdir";
			$ftp_chdir.="/";
		}
	}

	$colon=strpos($ftp_server,":");
	if ($colon)
	{
		$ftp_port=substr($ftp_server,$colon+1);
		$ftp_server=substr($ftp_server,0,$colon);
	}

	$DOCBASE="http://$ftp_server/$ftp_chdir";
	if ($DOCBASE[strlen($DOCBASE)-1]!="/") $DOCBASE.="/";
	
	for ($i=$ver;$i>0;$i--)
	{
		if (is_array($CONST_EXCLUDE_MINOR_VERS)) if (in_array($i,$CONST_EXCLUDE_MINOR_VERS)) continue;
		$sz="../szablony/$szablon/$i";
		if (file_exists($sz))
		{
			$SZABLON_PATH=$sz;
			$images_ver=$i;
			break;
		}
	}
	if (!strlen($SZABLON_PATH))
	{
		$sz="../szablony/$szablon";
		if (file_exists($sz)) $SZABLON_PATH=$sz;
		$images_ver=$ver;
	}

	$KAMELEON_MODE=1; // zeby nie robil tablicy linkow
	include_once ("../include/cache.h");
	include_once ("../include/kameleon_href.h");
	$KAMELEON_MODE=0;

	if (strlen($SZABLON_PATH)) 
	{
		if (file_exists($SZABLON_PATH.'/const.php')) include($SZABLON_PATH.'/const.php'); else include($SZABLON_PATH.'/const.h');
	}

	$kameleon->charset=$CHARSET;



	$_ver=$ver;
	for ($ver=$_ver;$ver>0;$ver--)
	{
		if (is_array($CONST_EXCLUDE_MINOR_VERS)) if (in_array($ver,$CONST_EXCLUDE_MINOR_VERS)) continue;

		eval("\$KAMELEON_UIMAGES=\"$DEFAULT_PATH_KAMELEON_UIMAGES\";");
		eval("\$UIMAGES=\"$DEFAULT_PATH_UIMAGES\";");

		if (file_exists("../$KAMELEON_UIMAGES")) break;
	}
	$ver=$_ver;


	eval("\$IMAGES=\"$DEFAULT_PATH_IMAGES\";");
	eval("\$PAGES=\"$DEFAULT_PATH_PAGES\";");
	eval("\$PATH_PAGES_PREFIX=\"$DEFAULT_PATH_PAGES_PREFIX\";");
	eval("\$INCLUDE=\"$DEFAULT_PATH_INCLUDE\";");
	eval("\$UFILES=\"$DEFAULT_PATH_UFILES\";");

	$PAGES="$PATH_PAGES_PREFIX$PAGES";
	$_ver=$ver;
	for ($ver=$_ver;$ver>=0;$ver--)
	{
		if (is_array($CONST_EXCLUDE_MINOR_VERS)) if (in_array($ver,$CONST_EXCLUDE_MINOR_VERS)) continue;
		if (!$ver) $ver="";
		eval("\$KAMELEON_UINCLUDES=\"$DEFAULT_PATH_KAMELEON_UINCLUDES\";");
		if (file_exists("../$KAMELEON_UINCLUDES")) break;
	}
	$ver=$_ver;


	if ($CONST_COMPRESS_JS)
	{
		$v=phpversion(); 
		$v=$v[0];
		//if ($v=='4') require('./class.JavaScriptPacker.4.php');
		//if ($v=='5') require('./class.JavaScriptPacker.php');

		//require('./class.JavaScriptPacker.4.php');

	}

	

	while(1)
	{
		if (!function_exists('ftp_connect'))
		{
			ftp_log(label('No ftp compiled'));
			break;
		}


		if (ftp_time2bekilled()) return;

		$result=false;
		if (function_exists('ssh2_connect'))
		{
			global $SFTP_CONN,$SSH_CONN;

			$port=$ftp_port;
			if ($port==21) $port=22;

			$SSH_CONN=ssh2_connect($ftp_server,$port);
			if ($SSH_CONN)
			{
				if (in_array('password',ssh2_auth_none($SSH_CONN, $ftp_user) )) 
				{
					if (ssh2_auth_password($SSH_CONN, $ftp_user, $ftp_pass))
					{
						$SFTP_CONN=ssh2_sftp($SSH_CONN);
					}
					else
					{
						ftp_log(label("SFTP authentication failed"),label("FAIL"));
					}
				}
				else
				{
					ftp_log(label("SFTP server does not support password authentication"),label("FAIL"));	
				}
			}
			
			if ($SFTP_CONN)  ftp_log(label("Using SSH+SFTP"),label("OK"));	
		}
		
		if (!$SFTP_CONN)
		{
			if (!$result) $result=@ftp_connect($ftp_server,$ftp_port);

			$wynik=$result?label("OK"):label("FAIL");
			
			ftp_log(label("Connecting")." $ftp_server:$ftp_port",$wynik);
			if (!$result) break;
			$FTP_CONN=$result;

			if (ftp_time2bekilled()) return;


			$result=@ftp_login($FTP_CONN, $ftp_user, $ftp_pass);
			$wynik=$result?label("OK"):label("FAIL");
			ftp_log(label("Logging as")." $ftp_user",$wynik);	
			if (!$result) break;

			if (!$CONST_FTP_PASSIVE) 
			{
				@ftp_pasv($FTP_CONN,0);
			}
			else 
			{
				@ftp_pasv($FTP_CONN,1);
				ftp_log(label("Passive mode"),label("OK"));
			}
		}



		if (strlen($ftp_dir))
		{
			$result=k_ftp_oper("chdir",$FTP_CONN, $ftp_dir);
			$wynik=$result?label("OK"):label("FAIL");
			ftp_log(label("Chdir")." $ftp_dir",$wynik);
			
			if (!$result)
			{
				$result=mkdir_chdir($FTP_CONN,$ftp_dir);
				$wynik=$result?label("OK"):label("FAIL");
				ftp_log(label("Mkdir")." $ftp_dir",$wynik);
				if (!$result) break;
			}
		}

		$tmp=tempnam($temp_dir,"wgbw_${AUTH_PHP_USER}_${server}_".time());

		// *****************  Transport IMAGES

		$result=k_ftp_oper("chdir",$FTP_CONN, $IMAGES);
		if (!$result)
		{
			$result=mkdir_chdir($FTP_CONN,$IMAGES);
			if (!$result) break;
		}

		$img=$SZABLON_PATH;
		$images=0;
		if (strlen($img) && ($limitimages || $nolimit) )
		{
			$img.="/images";
		
			$handle=opendir($img); 
			while (($file = readdir($handle)) !== false) 
			{ 
				if ($file[0]==".") continue;
				if (is_dir("$img/$file")) 
				{
					$images+=upload_dir($FTP_CONN,"$img/$file");
					continue;
				}

				$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,$file);
				$ts_local=filemtime("$img/$file");
				if ($ts_local>$ts_remote || $no_date_check)	
					if (k_ftp_oper("put",$FTP_CONN,$file,"$img/$file",FTP_BINARY) )
						$images++;

				if (ftp_time2bekilled()) return;

			} 
			closedir($handle); 
			ftp_log(label("Upload")." ->> $IMAGES","$images ".label("files"));

		}		
		up_dir($FTP_CONN, $IMAGES);


		// *****************  Transport UIMAGES
		
		$result=k_ftp_oper("chdir",$FTP_CONN, "$UIMAGES");
		if (!$result)
		{
			$result=mkdir_chdir($FTP_CONN,$UIMAGES);
			if (!$result) break;
		}

		$wf_gal=poszukaj_galerii($UIMAGES);

		$query="SELECT wf_id,wf_file FROM webfile 
				WHERE wf_server=$SERVER_ID AND wf_gal IN ($wf_gal)
				AND wf_ver=$ver AND wf_status='D'
				ORDER BY wf_d_create
				";
		
		$res=$adodb->Execute($query);
		for ($i=0;$i<$res->RecordCount();$i++)
		{
			parse_str(ado_ExplodeName($res,$i));
			@k_ftp_oper('delete',$FTP_CONN,$wf_file);
			$sql="UPDATE webfile SET wf_status='-' WHERE wf_id=$wf_id";
			$adodb->execute($sql);
		}


		$v=$ver;
		$img="/kxdfhkjdh";
		while ($v && !file_exists($img))
		{
			$img="../uimages/$SERVER_ID/$v";
			$uimages_ver=$v--;
		}




		$images=0;
		if (strlen($img) && ($limitimages || $nolimit) )
		{
		
			$handle=opendir($img); 
			while (($file = readdir($handle)) !== false) 
			{ 
				if ($file=="." || $file=="..") continue;
				if (is_dir("$img/$file")) 
				{
					$images+=upload_dir($FTP_CONN,"$img/$file");
					continue;
				}
				$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,$file);
				$ts_local=filemtime("$img/$file");
				if ($ts_local>$ts_remote || $no_date_check)	
					if (k_ftp_oper("put",$FTP_CONN,$file,"$img/$file",FTP_BINARY) )
						$images++;

				if (ftp_time2bekilled()) return;

			} 
			closedir($handle); 
			ftp_log(label("Upload")." ->> $UIMAGES","$images ".label("files"));

		}		

				
		up_dir($FTP_CONN, $UIMAGES);



		// *****************  Transport UFILES
	
		if ($limitatt)
		{
			$result=k_ftp_oper("chdir",$FTP_CONN, $UFILES);
			if (!$result)
			{	
				$result=mkdir_chdir($FTP_CONN,$UFILES);
				if (!$result) break;
			}

			$wf_gal=poszukaj_galerii($UFILES);

			$query="SELECT wf_id,wf_file FROM webfile 
					WHERE wf_server=$SERVER_ID AND wf_gal IN ($wf_gal)
					AND wf_ver=$ver AND wf_status='D'
					ORDER BY wf_d_create
					";
			
			$res=$adodb->Execute($query);
			for ($i=0;$i<$res->RecordCount();$i++)
			{
				parse_str(ado_ExplodeName($res,$i));
				@k_ftp_oper('delete',$FTP_CONN,$wf_file);
				$sql="UPDATE webfile SET wf_status='-' WHERE wf_id=$wf_id";
				$adodb->execute($sql);
			}

			if ($ver==$SERVER->ver)
			{

				$tmp=tempnam ($temp_dir,"wgbw_${AUTH_PHP_USER}_${server}_".time());
				@unlink($tmp);
				$htaccess=fopen($tmp,'w');
				$wf_gal=0+poszukaj_galerii($UFILES);

				fwrite($htaccess,"# File created by WebKameleon ".date('d-m-Y H:i')."\n");


				$query="SELECT webfile.*,webpage.file_name FROM webfile 
						LEFT JOIN webpage ON id=wf_page AND ver=wf_ver AND server=$SERVER_ID AND lang='$lang' 
						WHERE wf_server=$SERVER_ID AND wf_gal=$wf_gal AND wf_page IS NOT NULL
						AND wf_ver=$ver
						ORDER BY wf_file DESC
						";

				
				$res=$adodb->Execute($query);
				for ($i=0;$i<$res->RecordCount();$i++)
				{
					parse_str(ado_ExplodeName($res,$i));
					$p=kameleon_href('','',$wf_page);

					if (strlen($file_name) )
					{
						eval("\$fn=\"$file_name\";");
						$p="$PATH_PAGES_PREFIX$fn";
					}

					fwrite($htaccess,"Redirect $http_url_dir$UFILES/$wf_file $http_url$p?redirect_file=$wf_file\n");

					if (ftp_time2bekilled()) return;
				}

				fclose($htaccess);
				$result=k_ftp_oper("put",$FTP_CONN,'.htaccess',$tmp,FTP_BINARY);
				if ($result) ftp_log(".htaccess",$wynik);		

				@unlink($tmp);
			}


			$ufiles=0;
			eval("\$img=\"../$DEFAULT_PATH_KAMELEON_UFILES\";");

			


			$handle=opendir($img); 
			while (($file = readdir($handle)) !== false) 
			{ 
					if ($file[0]=="." ) continue;

					if (is_dir("$img/$file")) 
					{
						$ufiles+=upload_dir($FTP_CONN,"$img/$file");
						continue;
					}
					$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,$file);
					$ts_local=filemtime("$img/$file");
					if ($ts_local>$ts_remote || $no_date_check)	if (k_ftp_oper("put",$FTP_CONN,$file,"$img/$file",FTP_BINARY) ) $ufiles++;
					if (ftp_time2bekilled()) return;

			}	
			closedir($handle); 
			ftp_log(label("Upload")." ->> $UFILES","$ufiles ".label("files"));
				
			up_dir($FTP_CONN, $UFILES);

			$ufiles=0;
			eval("\$img=\"../$DEFAULT_PATH_KAMELEON_UFILES/.root\";");

			
			$_PATH_PAGES_PREFIX='';
			if (strlen($DEFAULT_PATH_PAGES_PREFIX))
			{
				eval("\$_PATH_PAGES_PREFIX=\"\$DEFAULT_PATH_PAGES_PREFIX\";");
				if ($_PATH_PAGES_PREFIX[strlen($_PATH_PAGES_PREFIX)-1]=='/') $_PATH_PAGES_PREFIX=substr($_PATH_PAGES_PREFIX,0,strlen($_PATH_PAGES_PREFIX)-1);
			}

			if (strlen($_PATH_PAGES_PREFIX))
			{
				$result=k_ftp_oper("chdir",$FTP_CONN, $_PATH_PAGES_PREFIX);
				if (!$result)
				{	
					$result=mkdir_chdir($FTP_CONN,$_PATH_PAGES_PREFIX);
					if (!$result) break;
				}
			}


			$handle=opendir($img); 
			while (($file = readdir($handle)) !== false) 
			{ 
					if ($file[0]=="." ) continue;

					if (is_dir("$img/$file")) 
					{
						$ufiles+=upload_dir($FTP_CONN,"$img/$file");
						continue;
					}
					$ts_remote=@k_ftp_oper('mdtm',$FTP_CONN,$file);
					$ts_local=@filemtime("$img/$file");
					if ($ts_local>$ts_remote || $no_date_check)	if (k_ftp_oper("put",$FTP_CONN,$file,"$img/$file",FTP_BINARY) ) $ufiles++;
					if (ftp_time2bekilled()) return;

			}	
			closedir($handle); 
			ftp_log(label("Upload")." ->> /","$ufiles ".label("files"));
				
			if (strlen($_PATH_PAGES_PREFIX)) up_dir($FTP_CONN, $_PATH_PAGES_PREFIX);

		}


		// ***************** Trash page deleting

		if (isset($limitpage)) $_lp="AND page_id=$limitpage";

		$query="SELECT id,file_name AS fn,page_id FROM webpagetrash 
			WHERE server=$SERVER_ID AND status='N'
			AND lang='$lang' AND ver=$ver $_lp
			ORDER BY id";
		
		$res=$adodb->Execute($query);
		for ($i=0;$i<$res->RecordCount();$i++)
		{
			parse_str(ado_ExplodeName($res,$i));

			$result=@k_ftp_oper('delete',$FTP_CONN,"$fn");
			$wynik=$result?label("OK"):label("NO FILE");
			if ($result)
				ftp_log(label("Deleting")." $fn [$page_id]",$wynik);	

			$query="UPDATE webpagetrash SET status='D', nd_complete=".time()." WHERE id=$id";
			$adodb->Execute($query);

			if (ftp_time2bekilled()) return;
		}


		// ***************** Page transfer

		$result=k_ftp_oper("chdir",$FTP_CONN, "$PAGES");
		if (!$result)
		{
			$result=mkdir_chdir($FTP_CONN,$PAGES);
			if (!$result) break;
		}

		$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,"swf.php");
		$ts_local=filemtime("../remote/swf.php");
		if ($ts_local>$ts_remote || $no_date_check)	
		{
			$result=k_ftp_oper("put",$FTP_CONN,"swf.php","../remote/swf.php",FTP_BINARY,'dont_log');
			$wynik=$result?label("OK"):label("FAIL");
			ftp_log(label("Upload")." $PAGES/swf.php",$wynik);
		}


		$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,"popupimg.enc.js");
		$ts_local=filemtime("../jsencode/popupimg.enc");
		if ($ts_local>$ts_remote || $no_date_check)	
		{
			$result=k_ftp_oper("put",$FTP_CONN,"popupimg.enc.js","../jsencode/popupimg.enc",FTP_BINARY,'dont_log');
			$wynik=$result?label("OK"):label("FAIL");
			ftp_log(label("Upload")." $PAGES/popupimg.enc.js",$wynik);
		}


		$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,"swf.js");
		$ts_local=filemtime("../remote/swf.js");
		if ($ts_local>$ts_remote || $no_date_check)	
		{
			$result=k_ftp_oper("put",$FTP_CONN,"swf.js","../remote/swf.js",FTP_BINARY,'dont_log');
			$wynik=$result?label("OK"):label("FAIL");
			ftp_log(label("Upload")." $PAGES/swf.js",$wynik);
		}

		$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,"ufo.js");
		$ts_local=filemtime("../remote/ufo.js");
		if ($ts_local>$ts_remote || $no_date_check)	
		{
			$result=k_ftp_oper("put",$FTP_CONN,"ufo.js","../remote/ufo.js",FTP_BINARY,'dont_log');
			$wynik=$result?label("OK"):label("FAIL");
			ftp_log(label("Upload")." $PAGES/ufo.js",$wynik);
		}


		if (isset($limitpage) && !$limitpage_all_tree) 
		{
			$_lp="AND id=$limitpage";
		}
		else
		{
			$_lp="";
		}

		if (!$no_date_check && !isset($limitpage)) 
			$_date="AND (nd_update>=nd_ftp OR nd_update IS NULL OR nd_ftp IS NULL OR ver<>$ver)";

		$_exclude_vers = (is_array($CONST_EXCLUDE_MINOR_VERS)) ? 'AND ver NOT IN ('.implode(',',$CONST_EXCLUDE_MINOR_VERS).')' : '';

		$query="SELECT id AS page,file_name AS fn,ver AS v,hidden,noproof,tree,sid 
				FROM webpage 
				WHERE server=$SERVER_ID AND lang='$lang'  $_lp $_date $_exclude_vers
				AND ver<=$ver
				ORDER BY id,ver DESC";

		$res=$adodb->Execute($query);

		//echo $query;

		//$tmp="/tmp/webpage_generated_by_webkameleon_$AUTH_PHP_USER.$server.".time();



		$tmp=tempnam ($temp_dir,"wgbw_${AUTH_PHP_USER}_${server}_".time());

		if ( isset($limitpage) || $nolimit )
		  for ($i=0;$i<$res->RecordCount();$i++)
		  {
			parse_str(ado_ExplodeName($res,$i));



			if ($limitpage_all_tree && $page!=$limitpage && !strstr($tree,":$limitpage:"))  continue; 
			if ($page_transfered[$page]==1) continue;

			if ($noproof) ftp_log(label("Upload")." $page",label("NO PROOF"));	

			if ($hidden || $noproof) continue;
			
			if (!$dont_check_rights && !$kameleon->checkRight('publish','page',$page)) continue;
			//if (!checkRights($page,$rights_pages)) continue;

			$page_transfered[$page]=1;	
			
			if (ftp_time2bekilled()) return;

			$default_file_name="$PAGES/$page.$file_ext";


			$query="UPDATE webpage 
					SET nd_ftp=".time()." ,noproof=NULL,unproof_sids=':',
						unproof_counter=0,unproof_comment='',
						default_file_name='$default_file_name'
					WHERE server=$SERVER_ID AND lang='$lang' AND ver=$v
					AND id=$page";

			if ($ver==$v) $adodb->Execute($query);

			@unlink($tmp);
			$pipe="";
			if (file_exists("pipe_ftp/${SERVER_ID}_${lang}$ver")) $pipe="|pipe_ftp/${SERVER_ID}_${lang}$ver $page $fn";

			$cmd_exe="$PHP_EXE page.php";
			$cmd_arg="SERVER_ID=$SERVER_ID page=$page ver=$ver lang=$lang $CMS_API_HOST szablon=$szablon uimages_ver=$uimages_ver images_ver=$images_ver DOCBASE=$DOCBASE";
			
			$cmd="$cmd_exe $cmd_arg $pipe > $tmp";

			if (function_exists('kameleon_ftp_page') && strlen($_SERVER['HTTP_HOST']) ) kameleon_ftp_page($cmd_arg,$pipe,$tmp);
			else exec($cmd);

			
			if (strlen($fn) && filesize($tmp) )
			{
				if ($page==0) $index_uploaded=1;
				eval("\$filename=\"$fn\";");
				$filename="$PATH_PAGES_PREFIX$filename";

				$page_path=$filename;	

				up_dir($FTP_CONN,$PAGES);

				if ($page==0 && $lang==$SERVER->lang && $ver==$SERVER->ver) @k_ftp_oper('delete',$FTP_CONN,"$PATH_PAGES_PREFIXindex.$file_ext");

				$path=dirname($filename);
				$filename=basename($filename);

				if (strlen($path) && $path!=".")
				{
					$result=k_ftp_oper("chdir",$FTP_CONN,$path);
					$path=explode("/",$path);
					if (!$result)
					{
						for ($k=0;$k<count($path);$k++)
						{
							k_ftp_oper("mkdir",$FTP_CONN, $path[$k]);
							k_ftp_oper("chdir",$FTP_CONN, $path[$k]);
						
						}
					}

					$result=k_ftp_oper("put",$FTP_CONN,$filename,$tmp,FTP_BINARY,true);			
					for ($k=0;$k<count($path);$k++) k_ftp_oper("chdir",$FTP_CONN, "..");
				}
				else
					$result=k_ftp_oper("put",$FTP_CONN,$filename,$tmp,FTP_BINARY,true);

				k_ftp_oper("chdir",$FTP_CONN, $PAGES);
			}
			elseif (filesize($tmp))
			{
				$result=k_ftp_oper("put",$FTP_CONN,"$page.$file_ext",$tmp,FTP_BINARY,true);
				$page_path="$PAGES/$page.$file_ext";
				
			}

			

			if (!filesize($tmp)) $result=0;
			if ($result) 
			{
			  $total_pages++;
			  $now=time();

			  if ($ver==$v)
			  {
				  $wv_id =0;
				  $query="SELECT max(wv_id) AS wv_id FROM webver WHERE wv_table='webpage' AND wv_sid=$sid";
				  parse_str(ado_query2url($query));

				  if (!$wv_id)
				  {
					  webver_page(null,'ftp',$sid);
					  parse_str(ado_query2url($query));
				  }

				  $query="UPDATE webver SET wv_autor_ftp='$username',wv_date_ftp=$now WHERE wv_id=$wv_id";
				  $adodb->execute($query);
			  }
			}
			$wynik=$result?label("OK"):label("FAIL");
			$vvv=($ver!=$v)?" v.$v":"";
			$pp=ereg_replace("/\./","/",$page_path);
			ftp_log(label("Upload")." $pp [$page$vvv]",$wynik);	
						
		
		}
		if ($total_pages) ftp_log(label("Page report"),($total_pages+0)." ".label("files"));	

		up_dir($FTP_CONN,$PAGES);
		
		if ( isset($limitpage) || $nolimit )
		{
			include(dirname(__FILE__).'/sitemap.php');
			if (strlen($sitemap))
			{
				file_put_contents($tmp,$sitemap);
				$result=k_ftp_oper("put",$FTP_CONN,'sitemap.xml',$tmp,FTP_BINARY,true);	
		   		$wynik=$result?label("OK"):label("FAIL");
		   		ftp_log(label("Upload")." sitemap.xml",$wynik);			
			}
		}

		/* *********** UINCLUDES ************** */

		if ( strlen($INCLUDE) && ($nolimit || $limitinc) )
		{

			$result=k_ftp_oper("chdir",$FTP_CONN, $INCLUDE);
			if (!$result)
			{
				$result=mkdir_chdir($FTP_CONN,$INCLUDE);
				if (!$result) break;
			}

			$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,"api.h");
			$ts_local=filemtime("../remote/api.h");
			if ($ts_local>$ts_remote || $no_date_check)	
			{
		   		$result=k_ftp_oper("put",$FTP_CONN,"api.h","../remote/api.h",FTP_BINARY);
		   		$wynik=$result?label("OK"):label("FAIL");
		   		ftp_log(label("Upload")." $INCLUDE/api.h",$wynik);
			}

			if ($CONST_PLATNOSCI_PL)
			{
				$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,"kameleon.platnosci.pl.class.php");
				$ts_local=filemtime("../remote/platnosci.pl.class.php");
				if ($ts_local>$ts_remote || $no_date_check)	
				{
					$result=k_ftp_oper("put",$FTP_CONN,"kameleon.platnosci.pl.class.php","../remote/platnosci.pl.class.php",FTP_BINARY);
					$wynik=$result?label("OK"):label("FAIL");
					ftp_log(label("Upload")." $INCLUDE/kameleon.platnosci.pl.class.php",$wynik);
				}
			}

			if (is_array($FTP_PLUGINS)) foreach($FTP_PLUGINS AS $plugin_name=>$plugin_dirs) foreach (explode(',',$plugin_dirs) AS $plugin_dir)
			{
				$result=k_ftp_oper("chdir",$FTP_CONN, "$plugin_name");
				if (!$result)
				{
					$result=mkdir_chdir($FTP_CONN,"$plugin_name");
					if (!$result) break;
				}

				upload_dir($FTP_CONN,"../plugins/$plugin_name/$plugin_dir");

				up_dir($FTP_CONN, $plugin_name);

			}



			if (file_exists("../$KAMELEON_UINCLUDES"))
			{
				$img="../$KAMELEON_UINCLUDES";
				$inc=0;
				$handle=opendir($img);
				while (($file = readdir($handle)) !== false)
				{
                    if ($file[0]==".") continue;
					if (is_dir("$img/$file"))
                    {
						$inc+=upload_dir($FTP_CONN,"$img/$file");
						continue;
					}
					$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,$file);
					$ts_local=filemtime("$img/$file");
					if ($ts_local>$ts_remote || $no_date_check)	
					if (k_ftp_oper("put",$FTP_CONN,$file,"$img/$file",FTP_BINARY) )
					$inc++;

					if (ftp_time2bekilled()) return;
				}
				closedir($handle);
				if ($inc) ftp_log(label("Upload")." ->> $INCLUDE","$inc ".label("files"));
				
			}

			for ($m=0;$m<count($C_MODULES) && is_Array($C_MODULES);$m++)
			{
				$inc=0+upload_dir($FTP_CONN,"../modules/@$C_MODULES[$m]");
				if ($inc) ftp_log(label("Library")." $C_MODULES[$m] ->> $INCLUDE","$inc ".label("files"));
			}	


			if (is_Array($C_MODULES) && file_exists("$SZABLON_PATH/modules") )
			{
				$inc=0+upload_dir($FTP_CONN,"$SZABLON_PATH/modules");
				if ($inc) ftp_log(label("Module templates")." ->> $INCLUDE","$inc ".label("files"));
			}
			

			
			up_dir($FTP_CONN,$INCLUDE);		
		}

	
		if (!$CONST_BLOCK_REDIRECTING){
			$f=fopen($tmp,"w");
			fputs($f,"<? \$page+=0; if (\$pos=strpos(\$REQUEST_URI,\"?\")) \$_prms=substr(\$REQUEST_URI,\$pos); if (file_exists(\"_location.h\")) include (\"_location.h\"); ?>\n");
			fputs($f,"<? Header(\"Location: $PAGES/\$page.$file_ext\$_prms\"); return;?>\n");
			fputs($f,"<META HTTP-EQUIV='refresh' CONTENT='0;URL=$PAGES/0.$file_ext'>\n");
			fputs($f, "<A HREF='$PAGES/0.$file_ext'><FONT COLOR=WHITE>START</FONT></A>");
			fclose($f);
		}
		
		$fn="";
		$query="SELECT file_name AS fn FROM webpage 
			WHERE server=$SERVER_ID AND lang='$lang' AND ver=$_ver AND id=0";
		parse_str(ado_query2url($query));
		if ($ver==$SERVER->ver 
			&& $lang==$SERVER->lang 
			&& $nolimit 
			&& !$index_uploaded
			&& !strlen($fn)
			&& ($kameleon->checkRight('publish','page','0') || $dont_check_rights))
		{
			$result=k_ftp_oper("put",$FTP_CONN,"index.$file_ext",
					$tmp,FTP_BINARY);
			$wynik=$result?label("OK"):label("FAIL");
			ftp_log(label("Upload")." index.$file_ext",$wynik);	
		}

		find_nonzerofile_on_prefix($tmp,true);

		break;
	}

	if (!$SFTP_CONN)
	{
		if (function_exists('ftp_quit'))
		{
			ftp_quit($FTP_CONN);
			ftp_log(label("Disconnecting"),label("OK"));
		}
	}
	else
	{
		ftp_log(label("SSH end"),label("OK"));
	}
	ftp_end();
	
	//$adodb->Close();
