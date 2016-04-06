<?
$api_action="";

if ($SERVICE!="polecam")
{
	return;
}
if (!validateEmail($apiPolecamFrom) || !$apiPolecamFrom)
{
	$error = label("This email is not valid!");
	return;
}
include_once("include/captcha/kcaptcha.php");	
if ($error = KCAPTCHA::error())
{
	 return;
}

 $apiPolecamSubject=validateText($apiPolecamSubject);
 $apiPolecamDzieki=validateText($apiPolecamDzieki);
 $apiPolecamMsg=validateText($apiPolecamMsg);

 $api_action="";
 $query="INSERT INTO polecam (dzieki,od,subject,msg,servername)
		  VALUES
		 ('$apiPolecamDzieki','$apiPolecamFrom','$apiPolecamSubject','$apiPolecamMsg','$KEY')";
//echo $query;
 $adodb->Execute($query);
?>
