<?
	$action="";

	if (!$kameleon->checkRight('write','class') )
	{
		$error=$norights;
		return;
	}


	if (!file_exists($KAMELEON_UIMAGES))  mkdir_p($KAMELEON_UIMAGES);

	$file="$UIMAGES/$DEFAULT_TEXTFILE_CSS";
	
	$f=fopen($file,"w");
	if (!$f) $error="File creation error";
	if (strlen($error)) return;

	$query="SELECT nazwa,pole,wart FROM class WHERE server=$SERVER_ID AND ver=$ver and hash='kameleonOverwrite' 
		ORDER BY nazwa,pole";
	$style=ado_ObjectArray($adodb,$query);

	$nazwa="";
	for ($i=0;is_array($style) && $i<count($style);$i++)
	{
		if ($nazwa!=$style[$i]->nazwa)
		{
			if (strlen($nazwa)) fputs($f,"}\n\n");
			fputs($f,$style[$i]->nazwa." {\n");
		}
		$nazwa=$style[$i]->nazwa;
		fputs($f,"	".$style[$i]->pole.": ".$style[$i]->wart.";\n");
	}
	fputs($f,"}\n");	
	fclose($f);
?>
