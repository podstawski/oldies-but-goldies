<?
	if ($SERVICE!="ogloszenia")
 	{
	 	$api_action="";
		return;
	}

	$mm=validateEmail($apiOgloszeniaEmail);
	if (!$mm)
	{
		$api_action="";
		$err=1;
		echo "<script>alert('".label("Your email is not valid!")."')</script>";
	}
	if ($err) return;

 	$api_action="";
	$query="
		DELETE FROM ogloszenia_ustawienia WHERE servername='$KEY';
		INSERT INTO ogloszenia_ustawienia (email,subject,servername,slownik)
		  VALUES
		 ('$apiOgloszeniaEmail','$apiOgloszeniaSubject','$KEY','$apiOgloszeniaSlownik')";

//   echo $query;
   $adodb->Execute($query);

?>
