<?
	if (!$FORM[za_email]) 
	{
		$error=" ";
		return;
	}
	
	$query="SELECT * FROM zapytania WHERE za_id=$action_id";
	parse_str(ado_query2url($query));
	$za_odp=stripslashes($FORM[za_odp]);
?>
