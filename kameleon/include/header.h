<?

	$parser_template = kameleon_template($SZABLON_PATH,$PAGE_TYPY,$WEBPAGE->type);
	$parser_start="%SECTION_HEADER_BEGIN%";
	$parser_end="%SECTION_HEADER_END%";
	parser($parser_start,$parser_end,$parser_template,$tokens);

