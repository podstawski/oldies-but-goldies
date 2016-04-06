<?
 $api_action="";
 if ($SERVICE!="polecam")
 {
	return;
 }
 $apiPolecamOsoba=validateText($apiPolecamOsoba);
 if (!validateEmail($apiPolecamEmail) || !trim($apiPolecamEmail))
 {
  $error = label("This email is not valid!");
  return;
 }

include_once("include/captcha/kcaptcha.php");	
if ($error = KCAPTCHA::error())
{
	 return;
}


 include("include/sendmail.h");

 $api_action="";
 $api_mode+=0;
 if ($api_mode>1)
 {
		$query="SELECT * FROM polecam WHERE servername='$KEY' and id=$api_mode";
		$result=$adodb->Execute($query);
		if ($result->RecordCount()==1)
		{
			parse_str(ado_ExplodeName($result,0));
			$from=$od;
			//$from="$apiPolecamOsoba";
			$to=$apiPolecamEmail;
		        $subject="$subject";
			$msg=addslashes($msg);
			eval("\$msg=\"$msg\";");
		        echo "<p class=api_polecam_odpowiedz>$dzieki</p>";
         		sendmail($from,$to,$subject,$msg);
		}
 }
?>