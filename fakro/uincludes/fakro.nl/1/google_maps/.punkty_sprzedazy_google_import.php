<?
include($INCLUDE_PATH.'/google_maps/config_maps.php');
include($INCLUDE_PATH.'/Excel/reader.php');
require_once($INCLUDE_PATH.'/google_maps/google_importer.class.php');

global $WEBTD, $punkty, $list;

$importer = new Importer($UFILES."/import_pliki");
$files_to_import = $importer->getFilesToImport();

$xls = new Spreadsheet_Excel_Reader();

/*
echo '<pre>';
print_r($WEBTD);
echo '</pre>';
*/
echo "
<fieldset style=\"width:99%; margin-left:2px;\">
<legend>Punkty sprzedazy GOOGLE MAPS import (FAKRO)</legend>
<table border=0 cellspacing=0 cellpadding=3 width=\"100%\">
<tr>
	<td>
	SID - ".$WEBTD->sid."<br>
	</td>
</tr>
</table>
</fieldset>
<br/>";

echo "
<div align=\"right\">
<TABLE>
<TR>
<form method=post action=\"$self\" enctype=\"multipart/form-data\">
	<td>
	<INPUT TYPE=\"file\" name=\"userfile\">
	<INPUT TYPE=\"submit\" value=\"Import\" class=\"k_button\"></td>
</form>
</TR>
</TABLE>
</div><br><br>";


if(is_array($files_to_import)) {
	reset($files_to_import);
	while(list(,$filename) = each($files_to_import)) {
		
		if(!file_exists($importer->directory."/".$filename)) continue;
		
		$xls->setOutputEncoding('iso-8859-1');
		$xls->read($importer->directory."/".$filename);
		
		for($i = 1; $i <= $xls->sheets[0]['numRows']; $i++) {
			if($i > 1) {
				
				$DB_GOOGLE_MAPS->sqlaction('insert',$xls->boundsheets[0]['name'],'',
								array($xls->sheets[0]['cells'][1][1],$xls->sheets[0]['cells'][1][2],$xls->sheets[0]['cells'][1][3],$xls->sheets[0]['cells'][1][4],$xls->sheets[0]['cells'][1][5],$xls->sheets[0]['cells'][1][6],$xls->sheets[0]['cells'][1][7],$xls->sheets[0]['cells'][1][8],$xls->sheets[0]['cells'][1][9],$xls->sheets[0]['cells'][1][10]),
								array($xls->sheets[0]['cells'][$i][1],$xls->sheets[0]['cells'][$i][2],insert_str_replace($xls->sheets[0]['cells'][$i][3]),insert_str_replace($xls->sheets[0]['cells'][$i][4]),insert_str_replace($xls->sheets[0]['cells'][$i][5]),insert_str_replace($xls->sheets[0]['cells'][$i][6]),insert_str_replace($xls->sheets[0]['cells'][$i][7]),insert_str_replace($xls->sheets[0]['cells'][$i][8]),$xls->sheets[0]['cells'][$i][9],$xls->sheets[0]['cells'][$i][10]));
				if($DB_GOOGLE_MAPS->insertid()) $insertid+=1;
				}
			}
		
		echo "<div align=\"center\"><strong>Arkusz ".$xls->boundsheets[0]['name']." - wprowadzono ".$insertid." rekordow</strong></div><br><br>";
		
		if(file_exists($importer->directory."/".$filename)) @unlink($importer->directory."/".$filename);
		}
	}

echo '<div align="right"><font color="#FF0000">Prosze nie zmieniac nazwy arkusza! oraz nazwy kolumn!</font></div>';
?>