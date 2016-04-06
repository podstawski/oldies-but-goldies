<?
	global $HTTP_HOST;

	if (!strlen($sendmail_action)) return;
	$http_host=$HTTP_HOST;
	
	include_once("include/sendmail2.h");

	push($WEBTD);

	$query="SELECT * FROM webtd
		WHERE server = $SERVER_ID 
		AND lang = '$lang' AND ver = $ver 
		AND mod_action='$sendmail_action'";
	

	$_webtd = ado_ObjectArray($adodb,$query);
	
	$list = new sendmail_obj;
	$list->action = $sendmail_action;


	for ($_webtd_i=0; strlen($action) && is_array($_webtd) && $_webtd_i < count($_webtd); $_webtd_i++)
	{
		$WEBTD = $_webtd[$_webtd_i];
		$action_progress=1;
		if (!file_exists("modules/".$WEBTD->html)) continue;
	
		$list->webtd_sid=$WEBTD->sid;

		include("modules/".$WEBTD->html);
	}


	$WEBTD=pop;
?>
