<?
	define ("CACHE_READ",1);
	define ("CACHE_FLUSH",0);
	define ("CACHE_WRITE",0);
	define ("CACHE_DIR","cache");

	function cache_dt_sql2unix($sql,$time)
	{
		$m=0+substr($sql,5,2);
		$d=0+substr($sql,8,2);
		$y=0+substr($sql,0,4);

		$t=explode(":",$time);
		return mktime($t[0],$t[1],$t[2],$m,$d,$y);
	}	


	function cache_td($INCLUDE_PATH,$obj,$action)
	{
		global $CONST_CACHE_STATICINCLUDE,$KAMELEON_MODE,$ver,$lang;

		if (!$obj->staticinclude) return null;
		if ($obj->cos) return null;
		if (!file_exists(CACHE_DIR)) return null;
		
		$k=$KAMELEON_MODE+0;

		$cache_file=CACHE_DIR."/".$obj->server."_".$lang.$ver."_".ereg_replace("-","_","$obj->page_id")."_".$obj->pri.".$k";

		if ($action==CACHE_READ)
		{
			 
			$itime=filemtime("$INCLUDE_PATH/".$obj->html);
			$otime=cache_dt_sql2unix($obj->d_update,$obj->t_update);
			$ftime=0;
			if (file_exists($cache_file)) $ftime=filemtime($cache_file);
			if ( $itime>$ftime || $otime>$ftime || time()>$ftime+$CONST_CACHE_STATICINCLUDE)
			{
				ob_start();
				return "";
			}
			else
			{
				return read_file($cache_file);
			}
		}
		if ($action==CACHE_FLUSH)
		{
			$f=@fopen($cache_file,"w");
			if ($f)
			{
				fwrite($f,ob_get_contents());
				fclose($f);
			}
			ob_end_flush();
		}
	}


	function cache_var2include($key,$val)
	{
		if (is_array($val)) $arr=1;
		if (is_object($val)) $obj=1;

		if ($arr || $obj)
		{
			while ( list( $k, $v ) = each( $val ) )
			{
				if ($obj) $newkey=$key."->".$k;
				if ($arr) $newkey=$key."[".$k."]";
				$wynik.=cache_var2include($newkey,$v);
			}
			return ($wynik);
		}
		else
		{
			$v=addslashes($val);
			return "	\$$key = stripslashes(\"$v\") ;\n";
		}
	}

	function cache_var_file($key,$action,$val=null)
	{
		if (!file_exists(CACHE_DIR)) return null;

		global $SERVER_ID,$ver,$lang,$KAMELEON_MODE,$C_LOGFILE;

		$k=$KAMELEON_MODE+0;
		$cache_file=CACHE_DIR."/${SERVER_ID}_$lang${ver}_$key.$k";

		if ($action==CACHE_READ)
		{
			$ftime=0;
			$ftime=@filemtime($cache_file);
			$atime=@filemtime("$C_LOGFILE.$SERVER_ID");			 

			if ($ftime<$atime) return null;

			if (!file_exists($cache_file)) return null;

			include ($cache_file);
			$cmd="\$wynik = \$cached_$key ;";
			eval ($cmd);
			return ($wynik);
		}

		if ($action==CACHE_WRITE)
		{
			$f=@fopen($cache_file,"w");
			if ($f)
			{
				$plik="<?\n".cache_var2include("cached_$key",$val)."?>";
				fwrite($f,$plik);
				fclose($f);
			}
		}
	
	}

	function kameleonCache($filename, $content='')
	{
		if (!is_dir(CACHE_DIR)) return false;

		$filename=CACHE_DIR."/$filename";

		clearstatcache();
		$t=@filemtime($filename);
		
		if (!$t) 
		{
			//echo "<!-- CACHE FILE: NONE ($filename)-->\n";
			$fp=@fopen($filename,"w");
			@fclose($fp);
			return false;
		}
		else 
		{
			$fs=filesize($filename);
			//echo "<!-- CACHE FILE: ".date("H:i:s",$t)." ($filename, size=$fs)-->\n";
		}

		if (!filesize($filename) && !strlen($content)) return false;

		$wr=0;
		if (strlen($content))
		{
			$fp=fopen($filename.'---','w');
			if ($fp)
			{
				$wr=1;
				fwrite($fp,$content);
				@fclose($fp);
				@unlink($filename);
				rename ($filename.'---',$filename);	
			}
			else
				return false;
		}

		if ($wr) return true;

		if ((time()-$t) < 3600)
			return true;
		else
			return false;

	}

	function kameleonCacheContent($filename)
	{
		if (!is_dir(CACHE_DIR)) return false;

		$filename=CACHE_DIR."/$filename";

		$fd = @fopen ($filename, "r");
		if ($fd)
		{
			$content = @fread ($fd, filesize ($filename));
			@fclose ($fd);
		}
		return $content;
	}



?>