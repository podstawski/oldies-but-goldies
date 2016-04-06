<?
define('ADODB_FETCH_CASE', 2);
define ('ADODB_DIR',"$SKLEP_INCLUDE_PATH/adodb/");
$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
include_once(ADODB_DIR."adodb.inc.php");

class DB
{
	var $projdb;
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

	var $forget_debug = false;

	// dodatek do session
	var $session_file_prefix="sksess_";
	var $sessid;
	var $dontSaveSession=0;
	var $sessionDebugDir='';


	var $debugArray=array();

	function DB()
	{
		if (!$_connectionID) $this->dbconnect();
		$this->now=time();
	}

	function debug($str="")
	{
		if ($this->forget_debug) return false;
		return false;
	}

	function dbconnect()
	{
		$this->timer_total=$this->microtime_float();
		$this->pre_exec_query_dump();
		$this->projdb = &ADONewConnection(C_PROJ_CONNECT_DBTYPE);

		if (C_PROJ_CONNECT_PERSISTANT) 
			@$this->projdb->PConnect(C_PROJ_CONNECT_HOST, C_PROJ_CONNECT_USER, C_PROJ_CONNECT_PASSWORD, C_PROJ_CONNECT_DBNAME);
		else
			@$this->projdb->Connect(C_PROJ_CONNECT_HOST, C_PROJ_CONNECT_USER, C_PROJ_CONNECT_PASSWORD, C_PROJ_CONNECT_DBNAME);

		$this->_connectionID=$this->projdb->_connectionID;

		$ok=$this->_connectionID?1:0;
		$query="CONNECT: ".C_PROJ_CONNECT_USER."@".C_PROJ_CONNECT_HOST." -> ".C_PROJ_CONNECT_DBNAME;
		$this->post_exec_query_dump($query,$ok,0,0);
		return $this->_connectionID;
	}

	function execute($query)
	{
		$this->projdb->debug=$this->debug;
		if ($this->debug()) $this->pre_exec_query_dump();
		$res=$this->projdb->execute($query);
		if ($this->debug()) $this->post_exec_query_dump($query,$res,-1,-1);
		return $res;
	}

	function SelectLimit($query,$limit=-1,$offset=-1)
	{
		$this->projdb->debug=$this->debug;
		if ($this->debug()) $this->pre_exec_query_dump();
		$res=$this->projdb->SelectLimit($query,$limit,$offset);
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

	function query_debug_max_lines()
	{
		return 250;
	}

	function post_exec_query_dump($query,$res,$limit,$offset)
	{
		if (!$this->debug()) return;
		if (count($this->query_debug)>=$this->query_debug_max_lines()) return;

		$q=ereg_replace("[ \r\n\t]+"," ",$query);
		if (is_object($res))
		{
			$c=$res->RecordCount();
		}
		else
		{
			$c=($res)?$res:"ERROR";
		}

		$context=$this->action_state?$this->action_name:$this->template_context;
		$t=$this->microtime_float()-$this->timer;
		$t=round($t*10000)/10000;	
		$this->query_debug[]=array($t,$c,$limit,$offset,$context,$q);	
		if (count($this->query_debug)==$this->query_debug_max_lines())
		{
			$this->query_debug[]=array(0,"-",0,0,"system","query_debug_max_lines exceeded");
		}
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
		return $this->projdb->BeginTrans();
	}

	function CommitTrans()
	{
		return $this->projdb->CommitTrans();
	}

	function RollbackTrans()
	{
		return $this->projdb->RollBackTrans();
	}

	function table_fields($table)
	{
		if (is_array($this->session[tf][$table])) return $this->session[tf][$table];
		$query="SELECT * FROM $table";
		$result=$this->SelectLimit($query,1,0);
		if (!$result) return "";
		$result->Move(0);
		$data=$result->FetchRow();
		$wynik="";
		if (is_array($data) || is_object($data))
			foreach( array_keys($data) AS $key ) 
			{	
				$number=$key+0;
				if ("$number"=="$key") continue;
				$wynik[]=$key;
			}
		if (is_array($wynik)) $this->session[tf][$table]=$wynik;
		return $wynik;
	}


	function update_table($table,$indexes,$data,$floats="",$noupdate="")
	{
		$fields=$this->table_fields($table);
		if (!is_array($fields)) return false;
		if (!is_array($data)) return false;
		while (list($key,$val)=each($data))
		{
			if (!in_array($key,$fields)) continue;
			if (in_array($key,array_keys($indexes))) continue;
			if (is_array($noupdate) && in_array($key,$noupdate)) continue;
			$set.=",$key=";
			if (!strlen($val) && strtolower($val)=="null") $set.="NULL";
			elseif (is_array($floats) && in_array($key,$floats)) $set.=toFloat($val);
			else $set.="'".addslashes(stripslashes($val))."'";
		}
		if (strlen($set))
		{
			$set=substr($set,1);
			$where="";
			while (list($key,$val)=each($indexes))
			{
				if (!$val) continue;
				if (strlen($where)) $where.=" AND ";
				$where.="$key=$val";
			}

			if (strlen($where))
			{
				$query="UPDATE $table SET $set WHERE $where";
				if ($this->execute($query)) return true;
			}
		}
		return false;
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

	function _sysmsg($msg,$grupa="")
	{

		if (!strlen($msg)) return $msg;

		$lang=$this->lang;

		if (isset($this->session['sysmsg'][$lang]) AND is_array($this->session['sysmsg'][$lang]) AND strlen($this->session['sysmsg'][$lang]["msg_$msg"]))
		    return $this->session['sysmsg'][$lang]["msg_$msg"];
		                        
		$defaultlang="ms";
		$msg=trim($msg);

		$m=addslashes($msg);


		$query="SELECT msg_id, msg_msg FROM messages WHERE msg_label='$m' AND msg_lang='$defaultlang'";
		parse_str($this->ado_query2url($query));
		if (!strlen($msg_id)) 
		{
			$query="INSERT INTO messages (msg_label,msg_lang,msg_msg,msg_group) 
					VALUES ('$m','$defaultlang','$m','$grupa')";
			$this->ado_query2url($query);
		}	


		

		$msg_msg="";
		$msg_id=0;
		$query="SELECT msg_id,msg_msg FROM messages WHERE msg_label='$m' AND msg_lang='$lang'";

		//echo $query;
		parse_str($this->ado_query2url($query));
		

		if (!$msg_id) $wynik=$msg;
		else $wynik=stripslashes($msg_msg);

		$this->session[sysmsg][$lang]["msg_$msg"]=$wynik;
		return $wynik;
	}

	function action($action,&$FORM,&$LIST,&$_REQUEST,$osoba,$action_id)
	{
		if ($action=="OsobaRejestruj") return;
		if ($action=="KoszykOfertDoKoszyka") return;

		ob_start();
		if (is_array($_REQUEST))
		{
			print_r($_REQUEST);
		}
		$opis=ob_get_contents();
		ob_end_clean();

		$NOW=$this->now;

		$opis=addslashes(stripslashes($opis));
		if ($osoba > 0)
		{
			$query="INSERT INTO system_action_log
					(sal_user_id,sal_action,sal_data,sal_opis,sal_klucz)
					VALUES ($osoba,'$action',$NOW,'$opis','$action_id')";
			$this->execute($query);
		}

		return "";
	}


	function close()
	{
		$this->projdb->close();

		if (strlen($this->sysinfo))
		{
			$s=ereg_replace("'","\\'",$this->sysinfo);
			$s=ereg_replace("[\r\n]+","\\n",$s);
			echo "<script>alert('$s');</script>";
		}
		
		if (count($this->debugArray))
		{
			echo "<pre>";
			echo implode("\n",$this->debugArray);
			echo "</pre>";
		}

		if (count($this->query_debug))
		{
			$date_array=array(date("Y-m-d",$this->now),date("H.i.s",$this->now));
			foreach($this->query_debug AS $qd)
			{
				$suma+=$qd[0];
				$qd=array_merge($date_array,$qd);
				$log.=implode(";",$qd)."\r\n";
			}
			$plik=@fopen("/var/tmp/wm.debug.".$this->sk_nazwa.".query.log","a");			

			$log=implode(";",$date_array).";".$suma.";;;;;SUMA CZASU ZAPYTAÑ\r\n".$log;
			$t=$this->microtime_float() - $this->timer_total;
			$t=round($t*10000)/10000;
			$log=implode(";",$date_array).";".$t.";;;;;CA£KOWITA SUMA CZASU\r\n".$log;

			if ($plik)
			{
				fwrite($plik,$log);
				fclose($plik);
			}

			if ($this->debug())
			{
				echo "<hr size=1><h2>SQL:</h2>";
				echo "<div style='width:100%;overflow:auto;height:300px'>";
				echo "<table cellspacing=0 border=1><tr><td valign=top>";
				$log=ereg_replace("\n","<tr><td valign=top nowrap>",$log);
				$log=ereg_replace(";","&nbsp;<td valign=top nowrap>",$log);
				echo $log;
				echo "</table></div>";
			}
		}
		global $SYSTEM,$SKLEP_SESSION;

		if ($this->debug())
		{
			$kuki = session_get_cookie_params();

			echo "<hr size=1><h2>SESJA:</h2>";
			echo "<div style='width:100%;overflow:auto;height:200px'>";
			echo "
			name: ".session_name()."<br>
			id: ".session_id()."<br>
			cookie_lifetime: ".$kuki[lifetime]."<br>
			cookie_path: ".$kuki[path]."<br>
			cookie_domain: ".$kuki[domain]."<br>
			session_cache_expire : ".session_cache_expire()."<br>system:
			";

			print_r($SYSTEM);
			echo "<br>auth:";
			print_r($this->auth);
			echo "<br><pre>";
			print_r ($SKLEP_SESSION);

			echo "</pre></div>";

		}

		

		
		
		$this->session_close();
	}

	function session_close()
	{
		global $SYSTEM,$SKLEP_SESSION;

		if($this->sklep) $SKLEP_SESSION[sklep]=$this->sklep;
		$sid=$this->sessid;
		if (strlen($this->getSesionDir()) && strlen($sid))
		{
			$plik=@fopen($this->getSesionDir()."/".$this->session_file_prefix.$sid,"w");
			flock($plik, LOCK_EX);
			@fwrite($plik,serialize($SKLEP_SESSION));
			fclose($plik);
		}
	}

	function puke_ws_debug()
	{
		if ( strlen($this->ws_debug) && $this->debug())
		{
			echo "<hr size=1><h2>WebServices:</h2>";
			echo "<div style='width:100%;overflow:auto;height:300px'>";
			echo $this->ws_debug;
			echo "</div>";
		}
		$this->ws_debug="";
	}

	function poptemp($indeks,$fk1=0,$fk2=0,$fk3=0,$id=false)
	{
		if (!$this->system[temptime]) return "";
		$query="SELECT te_id,te_wart FROM temp WHERE";

		if (strlen($indeks)) $warunek.="AND te_indeks='$indeks' ";

		if ($fk1) $warunek.="AND te_fk1=$fk1 ";
		if ($fk2) $warunek.="AND te_fk2=$fk2 ";
		if ($fk3) $warunek.="AND te_fk3=$fk3 ";

		if (!$id) $warunek.="AND te_deadline>".$this->now;

		$query.=substr($warunek,3);
		
		parse_str($this->ado_query2url($query));

		if (!$id) return stripslashes($te_wart);
		if ($te_id) return $te_id;
		$sql="INSERT INTO temp (te_indeks,te_fk1,te_fk2,te_fk3)
			VALUES ('$indeks',$fk1,$fk2,$fk3)";
		$this->execute($sql);
		parse_str($this->ado_query2url($query));
		return $te_id;
	
	}

	function pushtemp($indeks,$value,$fk1=0,$fk2=0,$fk3=0)
	{
		if (!$this->system[temptime]) return "";
		$id=$this->poptemp($indeks,$fk1,$fk2,$fk3,true)+0;

		$deadline=$this->now+$this->system[temptime];

		$v=addslashes(stripslashes($value));
		$sql="UPDATE temp SET te_wart='$v',te_deadline=$deadline WHERE te_id=$id";
		$this->execute($sql);
	}

	function ws_soapclient($wsdl)
	{
		return new soapclient($wsdl, true, false, false, false, false,0,30);
	}
	

	function ws_action_pre($ws_action,&$evalstr,&$_operation,&$_output)
	{
		if (!file_exists($ws_action)) return null;
		include_once($this->include_path."/admin/ws_fun.php");
		require_once($this->include_path."/nusoap/nusoap.php");

		$ws_debug=$this->debug();
		if ($ws_debug) ob_start();

		$plik=file($ws_action);
		for ($i=0;$i<count($plik);$i++) 
		{
				parse_str(trim($plik[$i]));
				$var=substr($plik[$i],0,strpos($plik[$i],"="));	
				eval("\$$var=stripslashes(\$$var);");
		}
		
		if (file_exists("$ws_action.wsdl")) $wsdl="$ws_action.wsdl";

		$client = $this->ws_soapclient($wsdl);
		$error = $client->getError();
		if (strlen($this->system[wsu])) $client->setCredentials($this->system[wsu], $this->system[wsp]);
		$client->decode_utf8=false;
		$client->soap_defencoding = 'UTF-8';
		if ($error) return null;

		//if ($ws_debug) { echo "<h2>Input def:</h2><pre>";print_r($input);echo "</pre>";}

		$evalstr="";
		while (is_array($input) && list($k,$v)=each($input))
		{
			if (!is_array($v)) $evalstr.=" \$params[$k]=$v ;\n";
			else $evalstr.=createInputSubstr("\$params[$k]",$v);	
		}

		if ($ws_debug) {echo "<h2>Param build:</h2><pre>";print_r($evalstr);echo "</pre>";}	

		$_operation=$operation;
		$_output=$output;

		$this->ws_debug.=ob_get_contents();
		ob_end_clean();

		return $client;
	}

	function ws_action($client,$operation,$params,$output,&$evalstr)
	{
		$ws_debug=$this->debug();
		if ($ws_debug) ob_start();

		$this->pre_exec_query_dump();
		if ($ws_debug) {echo "<h2>Parameters:</h2><pre>";print_r($params);echo "</pre>";}
		arr2utf8($params);

		$result=$client->call($operation, array('parameters'=>$params));

		if ($client->fault)
		{
			$error=addslashes(utf82iso88592($result[faultstring]));
			$error=ereg_replace("\n","; ",$error);
		}
		if ($err=$client->getError())
		{
			$error=addslashes(utf82iso88592($err));
			$error=ereg_replace("\n","; ",$error);
		}

		$this->post_exec_query_dump("SOAP:$operation.$error",strlen($error)?0:1,-1,-1);
		$evalstr="";
	
		if (strlen($error) && $ws_debug)
		{
			echo '<h2>Request:</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
			echo '<h2>Response:</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
			$this->ws_debug.=ob_get_contents();
			ob_end_clean();
			return ;
		}

		if (strlen($error)) 
		{
			$this->ws_debug.=ob_get_contents();
			ob_end_clean();
			return $error;
		}

		if ($ws_debug) {echo "<h2>Result:</h2><pre>";print_r($result); echo"</pre>";}


		if (is_array($output) && count($output)==1)
		{
			foreach($output AS $o) if (is_array($o)) $output = $o;
		}
	
		$wynik=ws_result_string($result,$output);	

		if ($ws_debug) {echo "<h2>Output:</h2><pre>";print_r($output); echo"</pre>";}
		if ($ws_debug) {echo "<h2>Wynik:</h2><pre>$wynik</pre>";}


		$this->ws_debug.=ob_get_contents();
		ob_end_clean();

		$evalstr=$wynik;
		return;
	}

	function getSesionDir()
	{
		$check=array(ini_get ("session.save_path"),"/var/tmp","/tmp","log");
		foreach ($check AS $pwd) if (strlen($pwd) && is_writable($pwd)) return $pwd;		
	}

	function session_start()
	{
		global $REMOTE_ADDR, $_COOKIE, $SKLEP_SESSION;


		if (strlen($_COOKIE["SKSESSID"])) 
		{

			$sid=$_COOKIE["SKSESSID"];
			$plik=$this->getSesionDir()."/".$this->session_file_prefix.$sid;
			$session="";
	
			$fs=0;$fe=0;
			$try=3;
			while ($try && file_exists($plik)) 
			{
				$fe=1;
				$fp=fopen($plik,"r");
				flock($fp, LOCK_EX);
				$SKLEP_SESSION = unserialize(fread($fp, filesize($plik) ));
				fclose($fp);

				
				if (is_array($SKLEP_SESSION)) break;
				$try--;
				usleep(200);
			}
			$this->session=$session;
			$this->sessid=$sid;

			return $sid;
		}
		

		$sid="php_wk".rand(100000,999999)."${REMOTE_ADDR}".time();
		$sid=md5($sid);

		if (!headers_sent()) SetCookie("SKSESSID",$sid,0,"/");
		$this->sessid=$sid;
		$_COOKIE["SKSESSID"]=$sid;
	}

	function url2array($url)
	{
		if (!strlen($url)) return;
		foreach (explode('&',$url) AS $para)
		{
			$p=explode('=',$para);
			$wynik[urldecode($p[0])]=urldecode($p[1]);
		}
		return $wynik;
	}

}
?>