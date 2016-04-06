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

 include("include/sendmail.h");

 $api_action="";
 if (strlen($api_mode)>0)
 {
		$sql="SELECT costxt FROM webtd WHERE sid=".$API_REQUEST[sid];
		parse_str(ado_query2url($sql));
		$costxt+=0;

		$query="SELECT * FROM kontakt WHERE  servername='$KEY' AND id=$costxt";
		$result=$adodb->Execute($query);
		if ($result->RecordCount()==1)
		{
			parse_str(ado_ExplodeName($result,0));
			$to=$email;
			$from=$apiKontaktEmail;
			 $subject="$apiKontaktSubject | $subject";
			 $msg=$apiKontaktOpis;
			 echo "<p class=api_kontakt_odpowiedz>$opis</p>";
			 sendmail($from,$to,$subject,$msg);
			 //AM przej¶cie na nastêpn± strone
			 //PP: //
			 //global $next;
			 //echo "<script>document.location.href=\"$next\"</script>";
		}
 }
?>