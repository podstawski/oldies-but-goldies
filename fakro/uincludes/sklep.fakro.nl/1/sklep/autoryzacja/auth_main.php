<?

if ($AUTH[id] > 0)
{
	include ($SKLEP_INCLUDE_PATH."/template.php");
	return;
}

include ($SKLEP_INCLUDE_PATH."/autoryzacja/login.php");

?>
