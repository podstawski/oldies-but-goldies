<?


	include_once("$INCLUDE_PATH/.api/winiso.h");

	if ($lang=="p") $message=iso2win($message);
	if ($lang=="i") $message=win2iso($message);


	$button="";
	$order_id=base64_decode($order_id);
	if (strlen($ret_code)) 
	{
		$ret_code=base64_decode($ret_code);
		$button_v=label("Return");
		$button="<br><br><input value=\"$button_v\" type='button' onClick=\"document.all['return_href'].click()\" class=\"api2_polcard_submit\">";
	}
	else $ret_code=".";

	echo "<br><a id='return_href' href='$ret_code'>$order_id -> <b>$POLCARD[resp] </b> ($err_code)</a><br> $message $button";


	$dzis=date("Y-m-d");
	$action="SendmailOnAction";
	$sendmail_action="PolcardResp";



	include("$INCLUDE_PATH/.api/action.h");

?>