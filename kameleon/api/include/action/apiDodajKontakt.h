<?
$api_action="";
if ($SERVICE!="kontakt")
{
	return;
}
$apiKontaktSubject=validateText($apiKontaktSubject);
$apiKontaktOdpowiedz=validateText($apiKontaktOdpowiedz);
if (!validateEmail($apiKontaktEmail))
{
	$error = label("This email is not valid!");
	return;
}
include_once("include/captcha/kcaptcha.php");	
if ($error = KCAPTCHA::error())
{
	return;
}


 $api_action="";
 $query="INSERT INTO kontakt (subject,email,servername,opis)
		  VALUES
		 ('$apiKontaktSubject','$apiKontaktEmail','$KEY','$apiKontaktOdpowiedz')";
 $adodb->Execute($query);
?>
