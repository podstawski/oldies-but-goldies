<?
	global $_REQUEST, $import_plik, $import_plik_name;

	if (is_uploaded_file($import_plik))
		$file_content = file($import_plik);
	
	$nazwa_konwertera = $FORM[nazwa];

	$kod_klienta = $AUTH[kod];

	eval("\$nazwa_konwertera = \"$nazwa_konwertera\";");

	$conv_name = $SKLEP_INCLUDE_PATH."/import_converters/".$nazwa_konwertera;

	if ($FORM[wymagany])
	{
		if (file_exists($conv_name))
		{
			include_once($conv_name);
			$converted_xml = import_convert($file_content);
			$converted_xml = win2iso($converted_xml);
			$obj=xml2obj($converted_xml);
		}
		else
		{
			$error = "Brak wymaganego konwertera - ".$conv_name;
			return;		
		}
	}
	else
		$obj=xml2obj(implode("",$file_content));

	
	$TODO=array();
	while (is_object($obj->magazyn) && list($__key,$v)=each($obj->magazyn))
	{
		if ($__key == "identyfikator") continue;
		if (file_exists("$SKLEP_INCLUDE_PATH/import/$__key.php"))
		{
			//echo "$SKLEP_INCLUDE_PATH/import/$__key.php";
			$TODO[]="$SKLEP_INCLUDE_PATH/import/$__key.php";
		}
		else
			echo "Nieznany poziom: <B>$__key</B><br>";
	}
	foreach ($TODO AS $f) include($f);

?>
