<?

	$xml=implode('',file($argv[1]));

	if (!strlen($argv[2])) 
	{
		echo $xml;
		return;
	}

	$header=ereg_replace("(<api>.*)<files>.*</api>.*","\\1",$xml);

	echo $header;

	for ($i=2;$i<$argc;$i++)
	{
		$f=$argv[$i];
		$file=ereg_replace("<api>.*<files>.*(<$f>.*</$f>).*</api>.*","\\1",$xml);
		$files.="	$file\n";
	}


	echo "<files>\n";
	echo "$files";
	echo " </files>\n</api>";

?>