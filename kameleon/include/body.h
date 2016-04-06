<?
	$_ver=$version;


	$parser_template = kameleon_template($SZABLON_PATH,$PAGE_TYPY,$WEBPAGE->type);

	$parser_start="%SECTION_BODY_BEGIN%";
	$parser_end="%SECTION_BODY_END%";
	parser($parser_start,$parser_end,$parser_template,$tokens);

	$levels=$adodb->getFromSession('page_levels');
	if (!is_array($levels['body'][$WEBPAGE->type]))
	{
		$levels['body'][$WEBPAGE->type]=array();

		$parser_content=read_file($parser_template);
		$parser_b=strpos($parser_content,$parser_start);
		$parser_b+=strlen($parser_start);
		$parser_e=strpos($parser_content,$parser_end);
		$parser_content=substr($parser_content,$parser_b,$parser_e-$parser_b);

		$parser_content=ereg_replace("%WEBBODY_LEVEL([0-9]+)","\n:LEVEL:\\1\n",$parser_content);
		$parser_content=explode("\n",$parser_content);
		foreach ($parser_content AS $line)
			if (substr($line,0,7)==':LEVEL:')
			{
				$l=substr($line,7)+0;		
				if ($l && !in_array($l,$levels['body'][$WEBPAGE->type]) ) $levels['body'][$WEBPAGE->type][]=$l;
			}
		if (count($levels['body'][$WEBPAGE->type]))
		{
			$adodb->addToSession('page_levels', $levels, true); 
		}
	}

