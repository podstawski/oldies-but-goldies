<?
$_path = $INCLUDE_PATH.'/rezerwacja/';
$_action = $self;

require_once($_path.'includes/conf.php');
require_once($_path.'includes/idb_mysql.php');

$idb =  new idatabase($CFG['host'],$CFG['user'],$CFG['pass'],$CFG['db']);

?>

<script language="javascript" type="text/javascript" src="<?=$_path?>js/checkform.js"></script>

<?
$m = '';
if(isset($_POST['m'])) $m = $_POST['m'];
	elseif(isset($_GET['m'])) $m = $_GET['m'];
	else $m = '';

$mode = $_POST['mode'];

switch($m) {
	case 'akcja':
		$mode = 'ava';
		break;
	case 'summary':
		break;
	case 'save':
		break;
	}

?>

<?

switch($mode) {
	case 'akcja':
		require_once($_path.'rezerwacja_akcja.php');
		break;
	case 'ava':
		require_once($_path.'rezerwacja_ava.php');
		break;
	case 'zap':
		require_once($_path.'rezerwacja_zap.php');
		break;
	case 'zapis':
		require_once($_path.'rezerwacja_zapis.php');
		break;
	default:
    	require_once($_path.'rezerwacja_data.php');
	}
?>

<?
/*
echo $_path;
echo '<pre>';
print_r($_POST);
print_r($_GET);
print_r($_SESSION);
echo '</pre>';

echo session_id();
*/
?>
