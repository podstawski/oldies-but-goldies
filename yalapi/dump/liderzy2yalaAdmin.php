<?php

	if(!file_exists($argv[1])) die("W parametrze ma być nazwa pliku z mailami liderów!\n");
	$liderzy=file($argv[1]);

	foreach ($liderzy AS $lider)
	{
		$lider=trim($lider);

		if (!strlen($lider)) continue;

		$email=explode('@',$lider);
		$database=preg_replace('/[^a-z0-9]/','_',$email[1]);

		$cmd='echo "UPDATE users SET role_id=1 WHERE email=\''.$lider.'\'; " | psql -d '.$database;
	
		echo "$cmd\n";
	}