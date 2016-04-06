<?
$api_action="";
if ($SERVICE!="ksiega")
{
	return;
}
$apiKsiegaOpis=validateText($apiKsiegaOpis);
if (strlen($apiKsiegaOpis)==0)
{
	$error = label("Message to short!");
	return;
}

$apiKsiegaOsoba=validateText($apiKsiegaOsoba);
if (strlen($apiKsiegaOsoba)==0)
{
	$error = label("Type your name");
	return;
}

$mm=validateEmail($apiKsiegaEmail);
if (!$mm)
{
	$error = label("This email is not valid!");
	return;
}

include_once("include/captcha/kcaptcha.php");	
if ($error = KCAPTCHA::error())
{
	 return;
}



	$query="SELECT slownik FROM ksiega_ustawienia WHERE servername='$KEY' LIMIT 1";
	$result=$adodb->Execute($query);	
	if ($result->RecordCount())
		parse_str(ado_ExplodeName($result,0));

	if (strlen($slownik))
	{
		$patterns=explode(":",$slownik);
		for ($p=0;$p<count($patterns);$p++)
		{
			$pattern=$patterns[$p];
			if (strlen($pattern))
			{
				$apiKsiegaOpis=eregi_replace("$pattern","***",$apiKsiegaOpis);
				$apiKsiegaOsoba=eregi_replace("$pattern","***",$apiKsiegaOsoba);
			}
		}
	}




	$api_action="";
	$query="INSERT INTO ksiega (opis,osoba,email,nwpis,grupa,servername)
		  VALUES
		 ('$apiKsiegaOpis','$apiKsiegaOsoba','$apiKsiegaEmail',".time().",'$apiKsiegaGrupa','$KEY')";

   	$adodb->Execute($query);
	
	include("../include/sendmail2.h");
   	$query="SELECT * FROM ksiega_ustawienia WHERE servername='$KEY' LIMIT 1";
	$result=$adodb->Execute($query);	
	if ($result->RecordCount())
	{
		parse_str(ado_ExplodeName($result,0));

		$m=new sendmail_obj;
		$m->from=$apiKsiegaEmail;
		$m->to=$email;
		$m->subject="$apiKsiegaOsoba | $subject";
		$m->msg=$apiKsiegaOpis;

		if (strlen($email)) sendmail2($m);
	}

?>
