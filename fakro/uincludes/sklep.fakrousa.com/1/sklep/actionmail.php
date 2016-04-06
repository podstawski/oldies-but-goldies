<?
	$template="action_$actionmail";

	if (!is_dir("$SKLEP_INCLUDE_PATH/templates/$template")) return;
	

	$costxt="cache=0&filename=$template";


	$magic=$AUTH[id].(($WM->now)%$AUTH[parent]);
	if ($KAMELEON_MODE) $magic="9q8we";
	$boundary="---=nextPart_".$WM->now."_$magic";

	ob_start();
	include("$SKLEP_INCLUDE_PATH/template.php");
	$mail=ob_get_contents();
	ob_end_clean();
	
	//echo '<pre>';
	//echo htmlspecialchars($mail);
	//echo '</pre>';

	if (strlen($mail)<50) return;



	$WM->pre_exec_query_dump();

	$program=ini_get("sendmail_path");
	if (strlen($C_SENDMAIL_PATH)) $program=$C_SENDMAIL_PATH;

	$prog=popen($program,"w");
	if ($prog)
	{
		fwrite($prog,$mail);
		pclose($prog);
	}
	$WM->post_exec_query_dump("SENDMAIL due to action",1,-1,-1);

?>
