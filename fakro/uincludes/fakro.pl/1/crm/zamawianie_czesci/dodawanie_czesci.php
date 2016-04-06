<?
$_path = './';
$_path = $INCLUDE_PATH.'/crm/zamawianie_czesci/';

$_action = './dodawanie_czesci.php';
$_action = $self;

require_once($_path.'includes/config_cvsignore.php');
require_once($_path.'includes/common.php');
require_once($_path.'includes/sql.php');

require_once($_path.'bp-save.php');

db_connect();

$user_action = '';
if(isset($_POST['user_action'])) $user_action = $_POST['user_action'];
	elseif(isset($_GET['user_action'])) $user_action = $_GET['user_action'];
	else $user_action = 'continue';

$mode = $_POST['mode'];

switch($user_action) {
	case 'continue':
		proceed();
		break;
	case 'summary':
		$summary = summary();
		$mode = 'summary';
		break;
	case 'save':
		save();
		break;
	}
?>

<link rel="stylesheet" type="text/css" media="all" href="<?=$_path?>js/jscalendar/calendar-win2k-1.css" title="win2k-1" />

<script src="<?=$_path?>bp.js" type="text/javascript"></script>
<script src="<?=$_path?>common.js" type="text/javascript"></script>
<script src="<?=$_path?>numer-tabliczki.js" type="text/javascript"></script>
<script src="<?=$_path?>js/overlib.js" type="text/javascript"></script>

<script type="text/javascript" src="<?=$_path?>js/jscalendar/calendar.js"></script>
<script type="text/javascript" src="<?=$_path?>js/jscalendar/lang/calendar-pl.js"></script>
<script type="text/javascript" src="<?=$_path?>js/jscalendar/calendar-setup.js"></script>
<script type="text/javascript">
function disallowDate(date) {
	var now = new Date().getTime();
	if (date.getTime() > (now)) {
		return true;
		}
	return false; // enable other dates
	}
</script>

<? if($mode == 'done') { ?>
<script type="text/javascript">
	<? if($_SESSION['done']['sposob_platnosci'] == 1) { ?>window.location = "<?=$platnosci_page_2;?>"<? } ?>
	<? if($_SESSION['done']['sposob_platnosci'] == 2) { ?>window.location = "<?=$platnosci_page_1;?>"<? } ?>
</script>
<? } ?>

<?
switch($mode) {
	case 'results':
		require_once($_path.'dodawanie_czesci_results.php');
		break;
	case 'summary':
		require_once($_path.'dodawanie_czesci_summary.php');
		break;
	case 'done':
		require_once($_path.'dodawanie_czesci_done.php');
		break;
	default:
    	require_once($_path.'dodawanie_czesci_szukaj.php');
	}
?>

<?
/*
echo '<pre>';
print_r($_POST);
print_r($_GET);
print_r($_SESSION);
echo '</pre>';
echo session_id();
*/
?>