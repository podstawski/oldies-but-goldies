<?
global $API_KEY_GOOGLE_MAPS;

$CFG['host'] = "localhost";
$CFG['user'] = "fakro_pl";
$CFG['pass'] = "zk4q7x";
$CFG['db'] = "fakro_pl";


require_once($INCLUDE_PATH.'/DB/idb_mysql.php');

$DB = new idatabase($CFG['host'],$CFG['user'],$CFG['pass'],$CFG['db']);

if(!function_exists('insert_str_replace')) {
	function insert_str_replace($row) {
		$re = str_replace("'", "\'",$row);
		return $re;
		}
	}
?>
