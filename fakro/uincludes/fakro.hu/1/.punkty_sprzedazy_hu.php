<?
  include_once("$INCLUDE_PATH/excel.php");
  include_once("$INCLUDE_PATH/Excel/reader.php");
  require_once("$INCLUDE_PATH/importer.class.php");
  
  //ini_set('display_errors', 1);
  //ini_set('error_reporting', E_ALL);
  
	$importer = new Importer($UFILES."/import_pliki");
	$files_to_import = $importer->getFilesToImport();

	function indexOfkod($txt) {
    for ($i=0; $i<count($txt);$i++) {
      $t=$txt[$i];
      if (preg_match( '/^[0-9]{2}-[0-9]{3}$/',$t))
        return $i;
    }
    return -1;
  }
	
  if (is_array($files_to_import)) {
    reset($files_to_import);
    
    while(list(,$filename) = each($files_to_import)) {
      if(!file_exists($importer->directory."/".$filename)) continue;
      
      $xls = new Spreadsheet_Excel_Reader();
      $xls->setOutputEncoding('iso-8859-2');
      $xls->read($importer->directory."/".$filename);
      
	  
      $id_arkusz = 0;
      foreach($xls->boundsheets as $k1 => $v1) {
        //echo "\n";
        //echo "<b>arkusz ".$xls->boundsheets[$k1]['name']."</b><br>";
        
        $wojewodztwo = win2iso($xls->boundsheets[$k1]['name']);
        $sql = "DELETE FROM punkty_sprzedazy_hu WHERE id_woj = '".$wojewodztwo."' AND type = 'S' ";
        $fakrodb->execute($sql);
        
        for($i = 1; $i <= $xls->sheets[$k1]['numRows']; $i++) {
          if($i == 1) continue;
		  if(isset($xls->sheets[$k1]['cells'][$i])) {
            
            $row = $xls->sheets[$k1]['cells'][$i];
			$ps_nazwa       = (isset($row[1])?$row[1]:'');
			$ps_kod         = (isset($row[2])?$row[2]:'');
			$ps_miasto      = (isset($row[3])?$row[3]:'');
			$ps_adres       = (isset($row[4])?$row[4]:'');
            $ps_telefon1    = (isset($row[5])?$row[5]:'');
			$ps_telefon2    = '';
			$ps_mail		= '';
			$ps_www			= '';
            $ps_wojewodztwo = $wojewodztwo;
            
            $sql = "INSERT INTO punkty_sprzedazy_hu (
            nazwa,     		kod,     	miasto,			adres,			id_woj,				telefon1,		telefon2,		mail,		www,	type) VALUES (
            '$ps_nazwa',	'$ps_kod',	'$ps_miasto',	'$ps_adres',	'$ps_wojewodztwo',	'$ps_telefon1',	'$ps_telefon',	'$ps_mail',	'$ps_www',	'S')";
            //echo $sql;
			//echo "<br>";
			$fakrodb->execute(win2iso($sql));
          }
        }
        $id_arkusz++;
      }
      echo "plik $filename - znaleziono $id_arkusz arkuszy<br>";
    }
  }
  
	echo $importer->printForm();
?>
