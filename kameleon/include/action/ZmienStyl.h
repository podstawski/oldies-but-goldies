<?
	if (!$kameleon->checkRight('write','class'))
	{
		$error=$norights;
		return;
	}

	$action="";

	$exploreclass=$nazwa;

	if (!is_Array($pole)) 	return;

	$query="";

	for ($i=0;$i<count($pole);$i++)
		$query.="UPDATE class SET wart='$wart[$i]', hash='kameleonOverwrite' 
				 WHERE server=$SERVER_ID AND nazwa='$nazwa' AND pole='$pole[$i]' AND ver=$ver;\n";

	if (strlen($nowe_pole))
	{
		$sql="SELECT domysl FROM classp WHERE pole='$nowe_pole'";
		parse_str(ado_query2url($sql));
		$query.="INSERT INTO class (server,nazwa,pole,wart,ver, hash) 
				VALUES($SERVER_ID,'$nazwa','$nowe_pole','$domysl',$ver, 'kameleonOverwrite')";
	}
	
	//echo nl2br($query);return;

	if ($adodb->Execute($query)) logquery($query) ;
	
	//zapisz plik z nowym stylem z róznicami.
	
	$action="ZapiszStyle";

