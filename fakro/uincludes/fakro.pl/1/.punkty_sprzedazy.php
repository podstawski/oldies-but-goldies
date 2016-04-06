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
      $xls->setOutputEncoding('latin2');
      $xls->read($importer->directory."/".$filename);
      
      $id_arkusz = 0;
      foreach($xls->boundsheets as $k1 => $v1) {
        //echo "\n";
        //echo "<b>arkusz ".iconv("latin2","UTF-8",$xls->boundsheets[$k1]['name'])."</b><br>";
        
        $wojewodztwo = iconv("latin2","UTF-8",$xls->boundsheets[$k1]['name']);
        $sql = "DELETE FROM punkty_sprzedazy WHERE ps_wojewodztwo = '".$wojewodztwo."' AND ps_typ='S'";
        $fakrodb->execute($sql);
        
        for($i = 1; $i <= $xls->sheets[$k1]['numRows']; $i++) {
          if(isset($xls->sheets[$k1]['cells'][$i])) {
            
            $row = $xls->sheets[$k1]['cells'][$i];
            
            $ps_ma_okno     = (isset($row[6])?iconv("latin2","UTF-8",$row[6]):0);
            $ps_ma_schody   = (isset($row[7])?iconv("latin2","UTF-8",$row[7]):0);
            $ps_ma_www      = (isset($row[8])?iconv("latin2","UTF-8",$row[8]):'');
            $ps_nazwa       = (isset($row[1])?iconv("latin2","UTF-8",$row[1]):'');
            $ps_kod         = (isset($row[2])?iconv("latin2","UTF-8",$row[2]):'');
            $ps_adres       = (isset($row[4])?iconv("latin2","UTF-8",$row[4]):'');
            $ps_miasto      = (isset($row[3])?iconv("latin2","UTF-8",$row[3]):'');
            $ps_kontakt     = (isset($row[5])?iconv("latin2","UTF-8",$row[5]):'');
            $ps_data        = time();
            $ps_wojewodztwo = $wojewodztwo;
            $ps_typ         = 'S';
            
            $sql = "INSERT INTO punkty_sprzedazy (
            ps_ma_okno,     ps_ma_schody,     ps_ma_www,      ps_nazwa,     ps_kod,     ps_adres,     ps_miasto,      ps_kontakt,     ps_data,    ps_wojewodztwo,     ps_typ) VALUES (
            '$ps_ma_okno',  '$ps_ma_schody',  '$ps_ma_www',   '$ps_nazwa',  '$ps_kod',  '$ps_adres',  '$ps_miasto',   '$ps_kontakt',  '$ps_data', '$ps_wojewodztwo',  '$ps_typ')";
            $fakrodb->execute($sql);
          }
        }
        $id_arkusz++;
      }
      echo "plik $filename - znaleziono $id_arkusz arkuszy<br>";
    }
  }

	echo $importer->printForm();
?>
