<?
define('ADODB_FETCH_CASE', 2);
include_once(ADODB_DIR."adodb.inc.php");
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;


class _NIC
{
}

class KDB
{
	var $adodb;
	var $_connectionID=0;
	var $debug=0;
	var $query_debug;
	var $timer;
	var $timer_total;
	var $action_state=0;
	var $action_name;
	var $template_context;
	var $flags;
	var $session;
	var $lang;
	var $sysinfo;
	var $now;
	var $query_cache;
	var $ws_debug;
	var $auth;
	var $width;
	var $debug_ips;
	var $session_file_prefix="wksess_";
	var $sessid;
	var $dbType;

	var $pe_parent='NULL';
	var $pe_count;
	var $freeze_debug=false;
	var $pe_negative;

	var $dontSaveSession=0;
	var $sessionDebugDir='';
	var $kameleon_after_include_vars;

	function KDB($C_DB_CONNECT_DBTYPE,$persistant_connection,
				 $C_DB_CONNECT_HOST, $C_DB_CONNECT_USER, 
				 $C_DB_CONNECT_PASSWORD, $C_DB_CONNECT_DBNAME,
				 $C_DEBUG_IP="")
	{

		global $CONST_SESSION_DEBUG_DIR;

		$this->sessionDebugDir=$CONST_SESSION_DEBUG_DIR;
		
		// Na wszelki wypadek, gdyby AdoDB chcia³by sobie zmieniæ nazwy silnikow baz danych
		if ($C_DB_CONNECT_DBTYPE == 'mssql') $this->dbType = 'mssql';
		if ($C_DB_CONNECT_DBTYPE == 'postgres') $this->dbType = 'postgres';
		if ($C_DB_CONNECT_DBTYPE == 'postgres7') $this->dbType = 'postgres';
		//end

		if (is_array($C_DEBUG_IP)) $this->debug_ips=$C_DEBUG_IP;
		else $this->debug_ips=explode(",",$C_DEBUG_IP);	


		if (strlen($_SERVER['REMOTE_ADDR']) && !headers_sent() ) $this->session_start();
			
		$this->now=time();
		$this->timer_total=$this->microtime_float();
		$this->pre_exec_query_dump();
		$this->adodb = &ADONewConnection($C_DB_CONNECT_DBTYPE);

		if ($persistant_connection)
			$this->adodb->PConnect($C_DB_CONNECT_HOST, $C_DB_CONNECT_USER, $C_DB_CONNECT_PASSWORD, $C_DB_CONNECT_DBNAME);
		else
			$this->adodb->Connect($C_DB_CONNECT_HOST, $C_DB_CONNECT_USER, $C_DB_CONNECT_PASSWORD, $C_DB_CONNECT_DBNAME);

		$this->_connectionID=$this->adodb->_connectionID;

		$ok=$this->_connectionID?1:0;

		$this->post_exec_query_dump(substr($_SERVER['REQUEST_URI'],1),1,0,0);

		if ($persistant_connection)
			$query="PCONNECT: $C_DB_CONNECT_USER@$C_DB_CONNECT_HOST -> $C_DB_CONNECT_DBNAME";
		else
			$query="CONNECT: $C_DB_CONNECT_USER@$C_DB_CONNECT_HOST -> $C_DB_CONNECT_DBNAME";
		$this->pe_count=0;
		$this->post_exec_query_dump($query,$ok,0,0);

	}

	function getSesionDir()
	{
		global $CONST_TEMP_DIR;
		$check=array(	$CONST_TEMP_DIR,
						ini_get ("session.save_path"),
						"/var/tmp","/tmp",dirname(dirname(dirname(__FILE__)))."/log");

		foreach ($check AS $pwd) if (strlen($pwd) && is_writable($pwd)) return $pwd;
		
	}

	function session_start()
	{
	

		if (strlen($_COOKIE["WKSESSID"])) 
		{

			$sid=$_COOKIE["WKSESSID"];
			$plik=$this->getSesionDir()."/".$this->session_file_prefix.$sid;
			$session="";


			$fs=0;$fe=0;
			$try=3;
			while ($try && file_exists($plik)) 
			{
				$fe=1;
				$fp=fopen($plik,"r");
				flock($fp, LOCK_EX);
				$session=@unserialize(fread($fp, filesize($plik) ));

		/*		
				ob_start();
				$fs=filesize($plik);
				include($plik);
				ob_end_clean();
		*/
				fclose($fp);

				
				if (strlen($session['login.login'])) break;
				$try--;
				usleep(200);
			}


			$this->session=$session;
			$this->sessid=$sid;

			
			if (strlen($this->sessionDebugDir))
			{

				$plik=fopen($this->sessionDebugDir."/kickout.log","a");
				$pid=getmypid();
				$login=$session['login.login'];
				fwrite($plik,"$pid:$sid:".$_SERVER['REQUEST_URI'].":$fs:$fe:try($try):$login\n");
				fclose($plik);
			}
			return $sid;
		}
		
		
		$sid="php_wk".rand(100000,999999).$_SERVER['REMOTE_ADDR'].time();
		$sid=md5($sid);

		if (strlen($this->sessionDebugDir))
		{

			$plik=fopen($this->sessionDebugDir."/kickout.log","a");
			$pid=getmypid();
			fwrite($plik,"$pid:new-$sid:".$_SERVER['REQUEST_URI'].":$fs:$fe:try($try)\n");
			fclose($plik);
			
		}

		//time()+180*60
		SetCookie("WKSESSID",$sid,0,"/");
		$this->sessid=$sid;
		$_COOKIE["WKSESSID"]=$sid;
	}

	function session_destroy()
	{
			$this->session=null;
			$sid=$this->sessid;
			$plik=$this->getSesionDir()."/".$this->session_file_prefix.$sid;
			@unlink($plik);
	}


	function debug($str="")
	{
	

		if (!strlen($_SERVER['REMOTE_ADDR'])) return false;

		$net=explode(".",$_SERVER['REMOTE_ADDR']);
		$net8=$net[0];
		$net16=$net[0].".".$net[1];
		$net24=$net[0].".".$net[1].".".$net[2];

		if (in_array($_SERVER['REMOTE_ADDR'],$this->debug_ips)) return true;
		if (in_array("$net24/24",$this->debug_ips)) return true;
		if (in_array("$net16/16",$this->debug_ips)) return true;
		if (in_array("$net8/8",$this->debug_ips)) return true;

		return false;
		
	}


	function execute($query)
	{
		$this->adodb->debug=$this->debug;
		if ($this->debug()) $this->pre_exec_query_dump();
		$res=$this->adodb->Execute($query);
		if ($this->debug()) $this->post_exec_query_dump($query,$res,-1,-1);
		return $res;
	}

	function SelectLimit($query,$limit=-1,$offset=-1)
	{
		$this->adodb->debug=$this->debug;
		if ($this->debug()) $this->pre_exec_query_dump();
		$res=$this->adodb->SelectLimit($query,$limit,$offset);
		if ($this->debug()) $this->post_exec_query_dump($query,$res,$limit,$offset);
		return $res;
	}
	
	function microtime_float() 
	{ 
   		list($usec, $sec) = explode(" ", microtime()); 
   		return ((float)$usec + (float)$sec); 
	} 

	function pre_exec_query_dump()
	{
		$this->timer=$this->microtime_float();
	}


	function post_exec_query_dump($query,$res,$limit,$offset)
	{
		if (!$this->debug()) return;
		if ($this->freeze_debug) return;

		$q=ereg_replace("[ \r\n\t]+"," ",$query);
		$q=addslashes($q);

		$r='';
		if (is_object($res))
		{
			$c=$res->RecordCount();
			if ($c>0) $r=$this->ado_explodeName($res,0);
		}
		else
		{
			if ($this->adodb->ErrorNo()) 
			{
				$c=-1;
				$this->pe_negative=true;
				$q.=" \n\n".addslashes($this->adodb->ErrorMsg());
			}
			else
				$c=0;
		}

		$t=$this->microtime_float()-$this->timer;
		$t=round($t*10000)/10000;	
		$now=time();
		$s=$this->sessid;



		$p=$this->pe_parent;



		$debug=$this->debug;
		$this->debug=false;
		$this->freeze_debug=true;


		$sql="SELECT nextval('kameleon_performance_pe_id_seq'::text) AS pe_id";
		parse_str($this->ado_query2url($sql));
		if ($p=='NULL' && $pe_id) $this->pe_parent=$pe_id;

		$query="INSERT INTO kameleon_performance (pe_id,pe_data,pe_czas,pe_sql,pe_limit,pe_offset,pe_count,pe_sess_id,pe_parent,pe_result) 
				VALUES ($pe_id,$now,$t,'$q',$limit,$offset,$c,'$s',$p,'$r')";



		$this->pe_count++;
		

		parse_str($this->ado_query2url($query));
		$this->freeze_debug=false;
		$this->debug=$debug;
	}


	function begin()
	{
		return $this->BeginTrans();
	}

	function commit()
	{
		return $this->CommitTrans();
	}

	function rollback()
	{
		return $this->RollBackTrans();
	}

	function BeginTrans()
	{
		return $this->adodb->BeginTrans();
	}

	function CommitTrans()
	{
		return $this->adodb->CommitTrans();
	}

	function RollbackTrans()
	{
		return $this->adodb->RollBackTrans();
	}

	function table_row2url($table,$idx,$sess=false)
	{
		while(list($k,$v)=each($idx))
		{
			$index.="${k}_$v";
			$where.=" AND $k=$v";
		}
		$where=substr($where,4);
		
		if ($sess) $wynik=$this->session[table][$table][$index];
		if (strlen($wynik)) return $wynik;
		
		$sql="SELECT * FROM $table WHERE $where";
		$wynik=$this->ado_query2url($sql,!$sess);;
		if ($sess) $this->session[table][$table][$index]=$wynik;
		return $wynik;	
	}

	function ado_query2url($query,$may_cache=false)
	{
		if ($may_cache) $q=ereg_replace("[ \r\n\t]+"," ",$query);
		if ($may_cache && strlen($this->query_cache[$q])) return $this->query_cache[$q];
		$result=$this->Execute($query);
		
		if (!$result) return;
		if ( $result->RecordCount()!=1 ) return "";

		$wynik=$this->ado_ExplodeName($result,0);
		$result->Close();
		if ($may_cache) $this->query_cache[$q]=$wynik;
		return ($wynik);
	}

 	function ado_ExplodeName ($result,$row)
 	{
		$text="";

		$result->Move($row);
		$data=$result->FetchRow();

		if (is_array($data) || is_object($data))
		while ( list( $key, $val ) = each( $data ) )
		{
			if (strlen($text)) $text.="&";
			$text.=$key."=".urlencode(trim($val));
		}
		return $text;
 	}



	function close($sysinfo="",$persistant_connection=0)
	{
		$sid=$this->sessid;
		if (strlen($_SERVER['REMOTE_ADDR']) 
			&& is_array($this->session) 
			&& strlen($sid) && !$this->dontSaveSession 
		)
		{
			//global $SCRIPT_NAME;
			//if (basename($SCRIPT_NAME)=='index.php' || basename($SCRIPT_NAME)=='tdedit.php') echo "<script>document.cookie='WKSESSID=$sid'</script>";
			
			$fs_before=@filesize($this->getSesionDir()."/".$this->session_file_prefix.$sid);


			if (true) //$inclen!=$fs_before
			{
				$plik=@fopen($this->getSesionDir()."/".$this->session_file_prefix.$sid,"w");
				flock($plik, LOCK_EX);
				$session=$this->session;
				@fwrite($plik,serialize($session));
				//@fwrite($plik,$inc);
				fclose($plik);

				if (strlen($this->sessionDebugDir))
				{
					$fs=filesize($this->getSesionDir()."/".$this->session_file_prefix.$sid);
					$plik=fopen($this->sessionDebugDir."/kickout.log","a");
					$pid=getmypid();
					fwrite($plik,"$pid:$sid:".$_SERVER['REQUEST_URI'].":write($fs_before,$inclen,$fs)\n");
					fclose($plik);
				}
			}
		}

		$this->sysinfo.=$sysinfo;
		if (strlen($this->sysinfo))
		{
			$s=ereg_replace("'","\\'",$this->sysinfo);
			$s=ereg_replace("[\r\n]+","\\n",$s);
			echo "<script>alert('$s');</script>";
		}

		$t=$this->microtime_float()-$this->timer_total;
		$t=round($t*10000)/10000;	

		if ($this->pe_parent)
		{
			$c=$this->pe_count;
			$p=$this->pe_parent;
			if ($this->pe_negative) $c*=-1;
			$query="UPDATE kameleon_performance SET pe_czas=$t,pe_count=$c WHERE pe_id=$p";
			$this->freeze_debug=true;
			$debug=$this->debug;
			$this->debug=false;
			$this->execute($query);
			$this->debug=$debug;
			$this->freeze_debug=false;

		}

		if (!$persistant_connection) $this->adodb->close();

	}


	//echo debug function
	function puke( $zmienna, $exit=false ) 
	{
		if (is_numeric($zmienna) || is_bool($zmienna)) 
		{
		     $zmienna = intval($zmienna);
		}
		//print '<pre style="debugStyle">debug: ';
		//print_r( $zmienna );
		//print '</pre>';
		if ( $exit ) exit;
	}

	function setCookie($name,$value="")
	{
		$sessionArray = &$this->session;
		$sessionArray['ciastka'][$name]=$value;
	}

	function getCookie($name)
	{
		$sessionArray = &$this->session;
		if (is_array($sessionArray['ciastka'])) return $sessionArray['ciastka'][$name];
	}

	function getCookies()
	{
		$sessionArray = &$this->session;
		return $sessionArray['ciastka'];
	}



	function addToSession($name, $value, $overwrite=false)
	{
		$sessionArray = $this->session;
		
		if ( !isset($sessionArray[$name]) )
		{
			$sessionArray[$name] = $value;
		}
		else if ( isset($sessionArray[$name]) && $overwrite == true )
		{
			$sessionArray[$name] = $value;
		}

		$this->session = $sessionArray;
	}
	
	function checkSessionValue($name)
	{
		$sessionArray = $this->session;
		
		if ( isset($sessionArray[$name]) && !empty($sessionArray[$name]) )
		{
			return true;
		}
		else if ( isset($sessionArray[$name]) && empty($sessionArray[$name]) )
		{
			return null;
		}
		else
		{
			return false;
		}
	}

	function getFromSession($name)
	{
		$sessionArray = $this->session;
		return $sessionArray[$name];
	}

	function delSessionVar($name)
	{
		$sessionArray =$this->session;
		unset($sessionArray[$name]);
		$this->session = $sessionArray;
	}


	function var2include($key,$val)
	{
		if (is_array($val)) $arr=1;
		if (is_object($val)) $obj=1;

		if ($arr || $obj)
		{
			while ( list( $k, $v ) = each( $val ) )
			{
				if ($obj) $newkey=$key."->".$k;
				if ($arr) $newkey=$key."[\"".$k."\"]";
				$wynik.=$this->var2include($newkey,$v);
			}
			return ($wynik);
		}
		else
		{
			$v=addslashes($val);
			return "	\$$key = stripslashes(\"$v\") ;\n";
		}
	}

	function clear_sessions($autologout=0)
	{
		if (!$autologout) $autologout=180;

		$dir=$this->getSesionDir();

		$handle=opendir($dir); 
		while (($file = readdir($handle)) !== false) 
		{ 
			if ($file=="." || $file=="..") continue;
			if (substr($file,0,strlen($this->session_file_prefix))!=$this->session_file_prefix) continue; 
			$t=$this->now - filemtime("$dir/$file");		
			if ($t<60*$autologout) continue;
			@unlink("$dir/$file");
		} 
		closedir($handle); 
	}

	function DBDate( $time = '' )
	{
		return $this->adodb->DBDate( $time );
	}
	
	function humandate($unix)
	{
		return date("d-m-Y",$unix);
	}

	function humanshort($unix)
	{
		return substr(humandate($unix),0,5);
	}

 
	function unixdate($human,$plus="")
	{
		$d=explode("-",ereg_replace("[^0-9\-]","",$human));

		if (3!=count($d)) return 0;
	
		if (!strlen($plus))
		{
			$h=date("H");
			$m=date("i");
			$s=date("s");
			return mktime($h,$m,$s,$d[1],$d[0],$d[2]);
		}
	
	return ($plus*3600*24) + mktime(0,0,0,$d[1],$d[0],$d[2]);
	}

	function getUploadTempDir()
	{
		$iniDir = ini_get('upload_tmp_dir');
		if (!empty($iniDir))
		{	
			$iniDir .= DIRECTORY_SEPARATOR;
			return str_replace("\\","\\\\", $iniDir ) ;
		}
		else
		{
			return '/tmp/';			
		}
		

	}

}

