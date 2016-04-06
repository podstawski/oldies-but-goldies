<?
	if ($SERVICE!="forum")
 	{
  		$api_action="";
		return;
	}
	$mm=validateEmail($apiForumEmail);
	if (!$mm)
	{
		$api_action="";
		$err=1;
		echo "<script>alert('".label("This email is not valid!")."')</script>";
	}
	if ($err) return;

 	$api_action="";
	$query="
		DELETE FROM forum_ustawienia WHERE servername='$KEY';
		INSERT INTO forum_ustawienia (email,subject,servername,slownik)
		  VALUES
		 ('$apiForumEmail','$apiForumSubject','$KEY','$apiForumSlownik')";

//   echo $query;
	$adodb->Execute($query);

?>
