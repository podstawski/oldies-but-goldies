<?
	if ($SERVICE!="ksiega")
 	{
  		$api_action="";
		return;
	}
	$mm=validateEmail($apiKsiegaEmail);
	if (!$mm)
	{
		$api_action="";
		$err=1;
		echo "<script>alert('".label("This email is not valid!")."')</script>";
	}
	if ($err) return;

 	$api_action="";
	$query="
		DELETE FROM ksiega_ustawienia WHERE servername='$KEY';
		INSERT INTO ksiega_ustawienia (email,subject,servername,slownik)
		  VALUES
		 ('$apiKsiegaEmail','$apiKsiegaSubject','$KEY','$apiKsiegaSlownik')";

//   echo $query;
	$adodb->Execute($query);

?>
