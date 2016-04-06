<?
global $API_KEY_GOOGLE_MAPS;

if($KAMELEON_MODE==0) {
	// key: www.fakro.nl
	$API_KEY_GOOGLE_MAPS = "ABQIAAAA7OSC64oLNVnWRIabrKnBZRRzBfMIT-1rJgJLlPobBbjlXt4dhRTx63oOaT5_-DBRhUd0fsuySE1IVw";
	}else{
	
	if($_SERVER['HTTP_HOST'] == 'kameleon') {
		// key: kameleon
		$API_KEY_GOOGLE_MAPS = "ABQIAAAA7OSC64oLNVnWRIabrKnBZRTc7ThDh8dymwEzDob3sDF0ZklZGxStjBQLGe20_NKXJs0IzHePwl75Mw";
		}else{
		// key: kameleon01.fakro.pl
		$API_KEY_GOOGLE_MAPS = "ABQIAAAA7OSC64oLNVnWRIabrKnBZRTvvno4W6z5umspeDGprMgiZVlFJhTXkpQa65Y8I0kUPGD1UaqSBhTyBQ";
		}
	}

$CFG['host'] = "isp6.i24.pl";
$CFG['user'] = "fakro_pl";
$CFG['pass'] = "zk4q7x";
$CFG['db'] = "fakro_pl";

global $DB_GOOGLE_MAPS;

require_once($INCLUDE_PATH.'/google_maps/DB/idb_mysql.php');

$DB_GOOGLE_MAPS = new idatabase($CFG['host'],$CFG['user'],$CFG['pass'],$CFG['db']);

if(!function_exists('insert_str_replace')) {
	function insert_str_replace($row) {
		$re = str_replace("'", "\'",$row);
		return $re;
		}
	}
?>
