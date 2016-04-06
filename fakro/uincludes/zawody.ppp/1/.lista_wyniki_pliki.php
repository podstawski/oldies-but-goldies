<?
global $lang,$ver,$CHARSET_TAB,$WEBTD,$SERVER_ID;
global $_data_zawodow;
global $lang;

$_data_zawodow = $WEBTD->costxt;

require_once($INCLUDE_PATH."/importer.class.php");

$importer = new Importer($UFILES."/wyniki/".$_data_zawodow);
$files_to_import = $importer->getFilesToImport();
echo $importer->printForm();
?>
