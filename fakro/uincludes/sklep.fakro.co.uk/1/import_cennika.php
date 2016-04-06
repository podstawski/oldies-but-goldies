<?
  include_once("$INCLUDE_PATH/excel.php");
  include_once("$INCLUDE_PATH/Excel/reader.php");
  require_once("$INCLUDE_PATH/importer.class.php");

  //  ini_set('display_errors', 1);
  //  ini_set('error_reporting', E_ALL);

  $importer = new Importer($UFILES."/import_cenniki");

  $files_to_import = $importer->getFilesToImport();

  function drainForText($obj) {
    if (is_object($obj) || is_array($obj)) {
      reset($obj);
      while(list(,$val) = each($obj))
        return drainForText($val);
    }else return $obj;
  }

  if(is_array($files_to_import)) {
    reset($files_to_import);

    while(list(,$filename) = each($files_to_import)) {
      if(!file_exists($importer->directory."/".$filename)) continue;

      $xls = new Spreadsheet_Excel_Reader();
      $xls->setOutputEncoding('iso-8859-2');
      $xls->read($importer->directory."/".$filename);

      foreach($xls->boundsheets as $k1 => $v1) {
        //echo "\n";
        //echo "arkusz ".$xls->boundsheets[$k1]['name']."\n\n";

        $rozmiary = array();

        for($i = 1; $i <= $xls->sheets[$k1]['numRows']; $i++) {

          $row = $xls->sheets[$k1]['cells'][$i];

          if(trim(strtoupper($row[1])) == "ROZMIARY") {
            foreach($row as $row_key => $row_val) {
              if(trim(strtoupper($row_val)) != "ROZMIARY") $rozmiary["".$row_val.""]=$row_key;
            }
          }else{
            if(count($rozmiary) && trim(strtoupper($row[1])) != "CENA BRUTTO") {
              
              $row[1] = drainForText($row[1]);
              $nazwa_produktu = trim(str_replace("*","",$row[1]));
              
              $sql = "SELECT ka_id FROM kategorie WHERE ka_nazwa = '$nazwa_produktu'";
              //echo $sql;echo '<br>';
              $ka_id = "";
              
              parse_str(ado_query2url($sql));
              if(!strlen($ka_id)) {
                echo "Brak kategorii $nazwa_produktu<hr>";
                continue;
              }
              
              $sql = "SELECT ka_id FROM kategorie WHERE ka_parent = $ka_id";
              //echo $sql;echo '<br>';
              
              $res = $adodb->execute($sql);
              $lista_kategorii = $ka_id;
              for($x=0; $x < $res->RecordCount(); $x++) {
                parse_str(ado_explodename($res,$x));
                $lista_kategorii.= ",".$ka_id;
              }
              
              reset($rozmiary);
              
              while(list($wymiary,$indeks) = each($rozmiary)) {
                
                if(!isset($row[$indeks])) continue;
                list($a,$b) = explode("x",$wymiary);
                
                $sql = "SELECT ts_to_id FROM towar_sklep, towar_parametry, towar_kategoria
                WHERE tp_to_id = tk_to_id AND tk_ka_id IN ($lista_kategorii)
                AND tp_to_id = ts_to_id AND ts_sk_id = $SKLEP_ID
                AND tp_a = '$a' AND tp_b = '$b'";
                $res = $adodb->execute($sql); 
                
                for ($x=0; $x < $res->RecordCount(); $x++) {
                  parse_str(ado_explodename($res,$x));
                  $sql = "UPDATE towar_sklep
                  SET ts_cena = ".str_replace(",",".",$row[$indeks])."
                  WHERE ts_to_id = $ts_to_id";
                  //echo $sql;echo '<br>';
                  $adodb->execute($sql);
                  $ile_towarow++;
                }
              }
            }
          }
        }
      }
    }
    echo "Uaktualniono ".$ile_towarow." towarów<br>";
  }

  echo $importer->printForm();
?>
