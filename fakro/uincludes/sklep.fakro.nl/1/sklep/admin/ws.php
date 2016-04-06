<?
	parse_str($costxt);
	if (!strlen($filename)) return;



	if (!file_exists("$SKLEP_INCLUDE_PATH/admin/ws/$filename")) return;


	include("$SKLEP_INCLUDE_PATH/admin/ws/$filename");

?>
