<?php

	function ftp_time2bekilled()
	{
		global $adodb, $FTP_ID;

		$sql="SELECT killed FROM ftp WHERE id=$FTP_ID";
		parse_str($adodb->ado_query2url($sql));

		return $killed;
	}

	function ftp_log($rozkaz,$wynik)
	{
		global $adodb, $FTP_ID;
		global $debug_mode;

		if ($debug_mode)
		{
			echo "$rozkaz: $wynik<br />";
			flush();
			ob_flush();
		}


		$query="INSERT INTO ftplog(ftp_id,nczas,rozkaz,wynik) VALUES ($FTP_ID,".time().",'$rozkaz','$wynik')";
		$adodb->Execute($query);
	}

	function ftp_begin()
	{
		global $adodb, $FTP_ID;

		$query="UPDATE ftp SET t_begin=".time().",pid=".getmypid()." WHERE id=$FTP_ID; 
			DELETE FROM ftplog WHERE ftp_id=$FTP_ID; ";
		$adodb->Execute($query);
	}

	function ftp_end()
	{
		global $adodb, $FTP_ID;

		$query="UPDATE ftp SET t_end=".time()." WHERE id=$FTP_ID";
		$adodb->Execute($query);

		k_ftp_oper('chdir',$FTP_ID,'/');
	}


	function upload_dir($FTP_CONN,$dir_path)
	{
		global $no_date_check;

		$dir=basename($dir_path);

		$img=$dir_path;
		$handle=opendir($img); 
		if ($handle === false) return;

			
		$result=k_ftp_oper("chdir",$FTP_CONN, $dir);
		if (!$result)
		{
			k_ftp_oper("mkdir",$FTP_CONN, $dir);
			k_ftp_oper("chdir",$FTP_CONN, $dir);
		}


	
		while (($file = readdir($handle)) !== false) 
		{
			if (ftp_time2bekilled()) return;

			if ($file[0]==".") continue;
			if (is_dir("$img/$file")) 
			{
				$rekur[]="$img/$file";
				//$images+=upload_dir($FTP_CONN,"$img/$file");
				continue;
			}
			$ts_remote=k_ftp_oper('mdtm',$FTP_CONN,$file);
			$ts_local=filemtime("$img/$file");
			if ($ts_local>$ts_remote || $no_date_check)	
				if (k_ftp_oper("put",$FTP_CONN,$file,"$img/$file",FTP_BINARY) )
					$images++;

		} 
		closedir($handle); 
		for ($i=0;$i<count($rekur) && is_Array($rekur);$i++)
			$images+=upload_dir($FTP_CONN,$rekur[$i]);

		k_ftp_oper("chdir",$FTP_CONN, "..");
		return($images);
	}

	function up_dir($FTP_CONN,$dir_path)
	{
		if (!strlen(trim($dir_path))) return;
		$path=explode("/",$dir_path);

		$dir="";
		for ($i=0;$i<count($path);$i++ ) 
		{
			if ($path[$i]==".") continue;
			if (strlen($dir)) $dir.="/";
			$dir.="..";
		}
		if (!strlen($dir)) return;
		k_ftp_oper("chdir",$FTP_CONN, $dir);
	}

	function mkdir_chdir($FTP_CONN,$dir_path)
	{
		if (!strlen(trim($dir_path))) return;
		$path=explode("/",$dir_path);
		for ($i=0;$i<count($path);$i++ )
		{
			$result=k_ftp_oper("mkdir",$FTP_CONN, $path[$i]);	
			$result=k_ftp_oper("chdir",$FTP_CONN, $path[$i]);	

		}
		$wynik=$result?label("OK"):label("FAIL");
		ftp_log(label("Mkdir")." $dir_path",$wynik);
		return $result;
	}

	
	function k_sftp_oper($oper,$SFTP_CONN,$param_1=false,$param_2=false,$param_3=false,$param_4=false)
	{
		global $uniq_name;
		global $SSH_CONN;
		static $dir;


		$s=10;
		$wynik=false;
		for ($i=0;$i<3;$i++)
		{
			switch ($oper)
			{
				case "mdtm":
					$file=strlen($dir)?"$dir/$param_1":$param_1;
					$stat=@ssh2_sftp_stat($SFTP_CONN,$file);
					if ($stat)
					{
						$wynik=$stat['mtime'];
					}
					$s=1;		
					break 2;


				case "chdir":
					$sdir=strlen($dir)?"$dir/$param_1":$param_1;
					$zabezp=0;
					while ( strstr($sdir,'/..') ) 
					{
						$sdir=ereg_replace("/*[^/]+/\.\.","", $sdir);
						if ($zabezp++>3) break;
					}

					$stat=@ssh2_sftp_stat($SFTP_CONN,$sdir);
					if (!$stat)
					{
						$wynik=false;
						break 2;
					}
					$dir=$sdir;
					$wynik=true;

					if ($param_1=='/')
					{
						$dir='';
					}
					break;

				case "put":
					$create_mode=0644;
					$executable=0;
					if (function_exists('fileperms')) 
					{
						$perms=substr(sprintf('%o', fileperms($param_2)), -3);
						for($s=0;$s<strlen($perms);$s++) if ($perms[$s]+0 & 1 ) $executable=1;
					}
					if ($executable) $create_mode=0755;

					$wynik=ssh2_scp_send($SSH_CONN,$param_2,$uniq_name,$create_mode);
					
					if (!$wynik) break;

					$file=strlen($dir)?"$dir/$param_1":$param_1;
					
			
					ssh2_sftp_unlink($SFTP_CONN,$file);
					$wynik=ssh2_sftp_rename($SFTP_CONN,$uniq_name,$file );
					
					break;

				case "mkdir":
					$file=strlen($dir)?"$dir/$param_1":$param_1;
					$wynik=ssh2_sftp_mkdir($SFTP_CONN,$file);
					$s=1;		
					break;

				case "delete":
					$file=strlen($dir)?"$dir/$param_1":$param_1;
					$wynik=ssh2_sftp_unlink($SFTP_CONN,$file);
					$s=1;		
					break;


				default:
					$wynik=true;
					echo "k_sftp_oper: $oper $param_1 $param_2 $param_3 $param_4<br>";
					break;

			}

			if ($wynik) break;
			sleep($s);
		}


		if ($oper=='put' && !$param_4)
		{
			ftp_log(label("Upload")." $file", $wynik ? label("OK") : label("FAIL"));
		}

		//echo "$oper,($dir),$param_1 <br/>";

		return $wynik;

	}

	function k_ftp_oper($oper,$FTP_CONN,$param_1=false,$param_2=false,$param_3=false,$param_4=false)
	{
		global $uniq_name;
		global $CONST_COMPRESS_JS;
		global $tmp;


		if (!strlen($uniq_name))
		{
			$uniq_name="k".getmypid().uniqid("");
			$uniq_name=substr($uniq_name,0,8).".tmp";
		}

		global $SFTP_CONN;

		if ($SFTP_CONN) return k_sftp_oper($oper,$SFTP_CONN,$param_1,$param_2,$param_3,$param_4);

		$wynik=0;
		$s=10;
		for ($i=0;$i<3;$i++)
		{
			switch ($oper)
			{
				case "mdtm":
					$wynik=ftp_mdtm($FTP_CONN,$param_1);
					$s=1;		
					break 2;

				case "delete":
					$wynik=ftp_delete($FTP_CONN,$param_1);
					$s=1;		
					break;
	
				case "mkdir":
					$wynik=@ftp_mkdir($FTP_CONN,$param_1);
					$s=1;		
					break;
				case "chdir":
					$wynik=@ftp_chdir($FTP_CONN,$param_1);
					$s=1;
					break 2;			
				case "put":
					if (false && $CONST_COMPRESS_JS)
					{
						
						$a=explode('.',strtolower($param_2));
						$ext=$a[count($a)-1];
				
						if ($ext=='js')
						{
							$packer = new JavaScriptPacker(file_get_contents($param_2), 'Normal', true, false);
							$packed = $packer->pack();	
							file_put_contents($tmp, $packed);
							$param_2=$tmp;
						}

					}
					$wynik=@ftp_put($FTP_CONN,$uniq_name,$param_2,$param_3);
					if (!$wynik) break;
					$executable=0;
					if (function_exists('fileperms')) 
					{
						$perms=substr(sprintf('%o', fileperms($param_2)), -3);
						for($s=0;$s<strlen($perms);$s++) if ($perms[$s]+0 & 1 ) $executable=1;
					}

					if ($executable) @ftp_site($FTP_CONN,"chmod 0755 $uniq_name"); 
					@ftp_delete ($FTP_CONN,$param_1);
					
					$wynik=@ftp_rename($FTP_CONN,$uniq_name,$param_1);
					if ($executable) @ftp_site($FTP_CONN, "chmod 0755 $param_1"); 
					break;

				default:
					$wynik=1;
					break;
			}
			if ($wynik) break;
			sleep($s);
		}


		if ($oper=='put' && !$param_4)
		{
			$dir=@ftp_pwd($FTP_CONN);
			ftp_log(label("Upload")." $dir/$param_1", $wynik ? label("OK") : label("FAIL"));
		}

		return $wynik;
	}


	function find_nonzerofile_on_prefix($full_path, $delete_after=false)
	{
		$wynik="";
		$prefix=basename($full_path);
		$dir_name=dirname($full_path);

		if ($dir = @opendir($dir_name)) 
		{
			while (($file = readdir($dir)) !== false) 
			{

				if (strstr($file,$prefix))
				{
					if (filesize("$dir_name/$file")) $wynik="$dir_name/$file";
					if ($delete_after) unlink("$dir_name/$file");
				}
			}  
			closedir($dir);
		}

		return ($wynik);
	}

	function poszukaj_galerii($path)
	{
		global $UFILES,$UIMAGES;

		$wynik=array();
		for ($galeria=1;$galeria<15;$galeria++)
		{
			$rootdir='';
			include('../include/ufiles_const.h');
			if ("$rootdir"=="$path") $wynik[$galeria]=$galeria; 
		}

		if (!count($wynik)) return 0;

		return implode(',',$wynik);
	}
