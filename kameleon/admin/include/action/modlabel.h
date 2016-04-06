<?
	$action="";
	include_once("../include/winiso.h");

	if ( !strlen($label) || !strlen(trim($value)) ) return;

	$value=addslashes(stripslashes($value));
	$label=addslashes(stripslashes($label));

	
	push($lang);

	$lang_array=array($lang);

	if ($lang=="i" || $lang=="p") $lang_array=array("i","p");

	foreach ($lang_array AS $lang)
	{
		if ($lang=="p") $value=iso2win($value);
		if ($lang=="i") $value=win2iso($value);

		$sql="SELECT count(*) AS c FROM label 
			WHERE label='$label' AND lang='$lang'";
		parse_str(ado_query2url($sql));

		if ($c)
			$query="UPDATE label SET value='$value' 
			 	WHERE label='$label' AND lang='$lang'";
		else
			$query="INSERT INTO label (lang,label,value)
			 	VALUES ('$lang','$label','$value')";
	
		//echo nl2br($query);break;
		if ($adodb->Execute($query)) logquery($query) ;
	}

	$lang=pop();

?>
