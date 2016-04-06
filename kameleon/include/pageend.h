<?
include("include/helpend.h");

$parser_template = kameleon_template($SZABLON_PATH,$PAGE_TYPY,$WEBPAGE->type);
$parser_start="%SECTION_PAGE_FOOTER_BEGIN%";
$parser_end="%SECTION_PAGE_FOOTER_END%";
$parser_tokens=$tokens;
parser($parser_start,$parser_end,$parser_template,$tokens);

