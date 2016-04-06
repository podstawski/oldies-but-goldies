<?
$api_action="";
if ($SERVICE!="ogloszenia")
{
	return;
}
$apiOgloszeniaOpis=validateText($apiOgloszeniaOpis);
if (strlen($apiOgloszeniaOpis)==0)
{
	$error = label("Type any text!");
	return;
}
$apiOgloszeniaOsoba=validateText($apiOgloszeniaOsoba);
if (strlen($apiOgloszeniaOsoba)==0)
{
	$error = label("Type your name");
	return;
}

if (!validateEmail($apiOgloszeniaEmail))
{
	$error = label("This email is not valid!");
	return;
}

if (!validateDate($apiOgloszeniaDeadline))
{
	$error = label("Invalid date format!");
	return;
}

include_once("include/captcha/kcaptcha.php");	
if ($error = KCAPTCHA::error())
{
	 return;
}


	$apiOgloszeniaDeadline=FormatujDateSQL($apiOgloszeniaDeadline);
	$api_action="";



	$query="SELECT slownik FROM ogloszenia_ustawienia WHERE servername='$KEY' LIMIT 1";
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
				$apiOgloszeniaOpis=eregi_replace("$pattern","***",$apiOgloszeniaOpis);
				$apiOgloszeniaOsoba=eregi_replace("$pattern","***",$apiOgloszeniaOsoba);
			}
		}
	}


	$query="INSERT INTO ogloszenia
	 (opis,osoba,email,ndeadline,nwpis,grupa,servername)
	  VALUES
	 ('$apiOgloszeniaOpis','$apiOgloszeniaOsoba','$apiOgloszeniaEmail','$apiOgloszeniaDeadline',".time().",'$apiOgloszeniaGrupa','$KEY')";

	$adodb->Execute($query);
	include("include/sendmail.h");
   	$query="SELECT * FROM ogloszenia_ustawienia WHERE servername='$KEY' LIMIT 1";
	$result=$adodb->Execute($query);	
	if ($result->RecordCount())
	{
		parse_str(ado_ExplodeName($result,0));
		$from=$apiOgloszeniaEmail;
		$to=$email;
        $subject="$apiOgloszeniaOsoba | $subject";
        $msg=$apiOgloszeniaOpis;
        sendmail($from,$to,$subject,$msg);
	}

?>
