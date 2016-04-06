<?
	$action="";
        
	if (!$kameleon->checkRight('insert','class') )
	{
		$error=$norights;
		return;
	}        

	$default_class_field="font-family";
	
	$nazwa=strtolower(trim($nazwa));
	if (!strlen($nazwa)) return;

	if ($nazwa[0]=="<") $nazwa=substr($nazwa,1,strlen($nazwa)-2);
	else $nazwa=".$nazwa";

	
	$query="SELECT count(*) AS c FROM class
		WHERE server=$SERVER_ID AND nazwa='$nazwa' AND ver=$ver";
	parse_str(ado_query2url($query));

	if ($c) $error=label("Class exists");

	if (strlen($error)) return;
	
	$query="SELECT domysl FROM classp
		WHERE pole='$default_class_field'";
	parse_str(ado_query2url($query));
	
	
	$query="INSERT INTO class
		 (server,nazwa,pole,wart,ver)
		  VALUES
		 ($SERVER_ID,'$nazwa','$default_class_field','$domysl',$ver)";
	
	//echo nl2br($query);return;

	if ($adodb->Execute($query)) logquery($query) ;

	$exploreclass=$nazwa;

?>
