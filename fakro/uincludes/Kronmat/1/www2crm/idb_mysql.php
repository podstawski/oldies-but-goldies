<?
#
# created on 2004-05-29 20:52:23
#
class idatabase {
	var $host;
	var $user;
	var $pass;
	var $db;
	var $rs;
	var $_query;
	var $row;
	var $havadata;
	var $_insertid;
	
	function idatabase($h,$u,$p,$d) {
		unset($this->_query);
		unset($this->rs);
		unset($this->row);
		
		$this->host=$h;
		$this->user=$u;
		$this->pass=$p;
		$this->db=$d;
		
		$this->havedata=false;
		
		mysql_pconnect($this->host,$this->user,$this->pass);
		echo mysql_error();
		mysql_select_db($this->db);
		echo mysql_error();
		}
	
	function query($str) {
		global $DEBUG,$REMOTE_ADDR;
		
		$this->_query = $str;
		if($this->havedata) {
			@mysql_free_result($this->rs);
			}
		$this->havedata = true;
		$this->rs = mysql_query($str);
		$this->_insertid = mysql_insert_id();
		
		if(mysql_errno() != 0)
			writelog($str.'/'.mysql_errno()."=".mysql_error());
		
		if($DEBUG == $REMOTE_ADDR) {
			echo mysql_error();
			}
		return $this->rs;
		}
	
	function list_tables() {
		global $DEBUG,$REMOTE_ADDR;
		
		$this->rs = mysql_list_tables($this->db);
		if($DEBUG == $REMOTE_ADDR) {
			echo mysql_error();
			}
		return $this->rs;
		}
	
	function sqlbackup($line) {
		$ret = '';
		
		mysql_query("$line");
		return $ret;
		}
	
	function sqlaction($action,$table,$where="",$fields="",$nvalues="") {
		global $HTTP_POST_VARS;
		
		$ret = '';
		
		if($action == "insert") {
			$ret = '';
			$values = "";
			$keys = "";
			for($i = 0; $i < count($fields); $i++) {
				$keys .= ($i > 0)?", ".$fields[$i]."":"".$fields[$i]."";
				}
			for($i = 0; $i < count($fields); $i++) {
				$values .= ($i > 0)?", '".$nvalues[$i]."'":"'".$nvalues[$i]."'";
				}
			mysql_query("INSERT INTO $table ($keys) VALUES ($values)");
			$this->_insertid = mysql_insert_id();
			}
		
		if($action == "select") {
			$rs = mysql_query("SELECT * FROM $table $where");
			$row = mysql_fetch_array($rs);
			for($i = 0; $i < count($fields); $i++) {
				$ret[$fields[$i]] = $row[$fields[$i]];
				}
			}
		
		if($action == "update") {
			$values = "";
			for($i = 0; $i < count($fields); $i++) {
				$values .= ($i > 0)?", ".$fields[$i]."='".$nvalues[$i]."'":$fields[$i]."='".$nvalues[$i]."'";
				}
			mysql_query("UPDATE $table SET $values WHERE $where");
			}
		
		if($action == "sql") {
			$values = "";
			for($i = 0; $i < count($fields); $i++) {
				$values .= $fields[$i];
				}
			mysql_query("UPDATE $table SET $values WHERE $where");
			}
		
		if($action == "delete") {
			mysql_query("DELETE FROM $table WHERE $where");
			}
		
		if(mysql_errno() != 0) writelog($action.' '.$table.' '.$where.' '.serialize($fields).' '.serialize($nvalues).'/'.mysql_errno()."=".mysql_error());
		return $ret;
		}
	
	function insertid() {
		return $this->_insertid;
		}
	
	function getvalues() {
		$this->row = mysql_fetch_array($this->rs);
		echo mysql_error();
		return $this->row;
		}
	
	function fetch_assoc() {
		unset($this->row);
		while($row = mysql_fetch_assoc($this->rs))
			$this->row[] = $row;
		
		#$this->row = mysql_fetch_assoc($this->rs);
		echo mysql_error();
		return $this->row;
		}
	
	
	function fetch_array() {
		
		unset($array);
		$array = array();
		while($row = mysql_fetch_assoc($this->rs))
			$array[] = $row;
		
		return $array;
		}
	
	function getrows() {
		$this->row = mysql_fetch_row($this->rs);
		return $this->row;
		}
	
	function free() {
		if($this->havedata) {
			mysql_free_result($this->rs);
			$this->havedata = false;
			}
		}
	
	function rowcount() {
		echo mysql_error();
		return @mysql_num_rows($this->rs);
		}
	
	function fieldcount() {
		return mysql_num_fields($this->rs);
		}
	
	function fieldname($nr) {
		return mysql_field_name($this->rs,$nr);
		}
	
	function error_number() {
		return mysql_errno();
		}
	}

class itablefields {
	var $tab;
	var $name;
	var $type;
	var $size;
	var $default;
	var $special;
	var $title;
	var $value;
	
	function itablefields($t) {
		$this->tab = $t;
		}
	
	function gettableinformation() {
		$rs = mysql_query("describe ".$this->tab);
		
		unset($this->name);
		unset($this->type);
		unset($this->size);
		unset($this->default);
		unset($this->special);
		
		while($row = mysql_fetch_row($rs)) {
			$this->name[] = $row[0];
			unset($ntab);
			ereg("\((.*)\)",$row[1],$ntab);
			$typeval = trim(ereg_replace("\((.*)\)","",$row[1]));
			$this->type[] = $typeval;
			
			if(!isset($ntab[2])) {
				if($typeval == "date") {
					$ntab[1] = 10;
					}
				if($typeval == "time") {
					$ntab[1] = 8;
					}
				}
			
			$this->size[] = $ntab[1];
			$this->default[] = $row[4];
			$this->special[] = $row[5];
			}
		mysql_free_result($rs);
		}
	}
?>