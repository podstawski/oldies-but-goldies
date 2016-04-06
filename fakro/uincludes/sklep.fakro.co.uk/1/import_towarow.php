<?
  include_once("$INCLUDE_PATH/excel.php");
  include_once("$INCLUDE_PATH/Excel/reader.php");
	require_once("$INCLUDE_PATH/importer.class.php");
  
//  ini_set('display_errors', 1);
//  ini_set('error_reporting', E_ALL);

	$dir=$UFILES."/import_towary";

	if (!file_exists($dir)) @mkdir ($dir);
	if (!file_exists($dir)) {
    echo sysmsg("Hard to create")." $dir";
    return;
	}

	$importer = new Importer($UFILES."/import_towary");
	$files_to_import = $importer->getFilesToImport();

	function tab_update2sql($table,$obj,$warunek) {
		$sql="";
		while (list($pole,$wart) = each($obj)) {
			if (strlen($sql)) $sql.=',';
			if (strlen(trim($wart))) $sql.="$pole='".addslashes(stripslashes(trim($wart)))."'";
			else $sql.="$pole=NULL";
		}
		if (!strlen($sql)) return "";

		$sql="UPDATE $table SET $sql WHERE $warunek";

		return $sql;
	}

	function ka_id($k){
		$ka_parent=" IS NULL";

		foreach (explode('->',$k) AS $ka_nazwa) {
			$ka_id=0;
			
			$sql="SELECT ka_id FROM kategorie WHERE ka_nazwa='$ka_nazwa' AND ka_parent$ka_parent";
			parse_str(ado_query2url($sql));

			$ka_parent="=$ka_id";
			if (!$ka_id) {
				echo ' '.sysmsg('brak kategorii','import')." '$brak<b>$ka_nazwa</b>'<br>";
				return 0;
			}
			$brak.=$ka_nazwa."->";
		}
		return $ka_id;
	}
  
  function csvToArray($re) {

    $lineResponse   = explode("\n", $re);
    $titleResponse  = trim($lineResponse[0],"\r");
    $titleResponse  = explode(",", $titleResponse);

    $responseArr = array();
    foreach($lineResponse as $k => $v) {
      $lineRes  = trim($v,"\r");
      $lineRes  = explode(",", $lineRes);

      if($k > 0 and is_array($lineRes) and !empty($lineRes) and count($lineRes) > 1) {
        $responseArr[] = array_combine($titleResponse, $lineRes);
      }
    }

    return $responseArr;
  }

	$slownik_naglowka = array (
		'NAZWA'                   => 'towar->to_nazwa',
		'KOD'                     => 'towar->to_indeks',
		'EAN'                     => 'towar->to_ean',
		'VAT'                     => 'towar->to_vat',
		'ZDJECIE'                 => 'towar->to_foto_s',
		'CENA'                    => 'towar_sklep->ts_cena',
		'KATEGORIA'               => 'kategoria[]',
		'SZEROKOSC'               => 'towar_parametry->tp_a',
		'WYSOKOSC'                => 'towar_parametry->tp_b',
		'STEROWANIE ELEKTRYCZNE'  => 'towar_parametry->tp_gatunek',
		'PROMOCJE'                => 'promocje[]'
	);


	$adodb->debug=0;
  
  if(is_array($files_to_import)) {
    reset($files_to_import);
    
    while(list(,$filename) = each($files_to_import)) {
      if(!file_exists($importer->directory."/".$filename)) continue;
      
      $xls = new Spreadsheet_Excel_Reader();
      $xls->setOutputEncoding('iso-8859-2');
      $xls->read($importer->directory."/".$filename);
      
      foreach($xls->boundsheets as $k1 => $v1) {
        
        $headersLineRes = array();
        $ile_towarow = 0;
        $headers = '';
        for($i = 1; $i <= $xls->sheets[$k1]['numRows']; $i++) {
          $row = $xls->sheets[$k1]['cells'][$i];
          
          if($i == 1) {
            foreach($row as $row_key => $row_val) {
              $headersLineRes[$row_key]  = trim(strtoupper($row_val));
              
              $headers[$row_key] = $slownik_naglowka[trim(strtoupper($row_val))];
            }
          }else{
            $lineArr = array();
            foreach($headersLineRes as $row_key => $row_val) {
              $cel = (isset($row[$row_key]))?$row[$row_key]:'';
              
              if($row_val == 'KATEGORIA') $lineArr[$row_val][] = $cel;
              else $lineArr[$row_val] = $cel;
            }
            
            $rek = null;
            
            foreach(array_keys($headers) as $h) {
              $cel = (isset($row[$h]))?$row[$h]:'';
              eval("\$rek->".$headers[$h]." = \$cel;");
            }
            
//            echo '<pre>';
//            print_r($rek);
            
            if (!strlen($lineArr['KOD'])) continue;
            
            echo $ile_towarow+1;
            echo ". <font color=green>".$lineArr['KOD'].'</font>';
            
            $adodb->BeginTrans();
            
            $to_id = 0;
            $query = "SELECT to_id FROM towar WHERE to_indeks='".$rek->towar->to_indeks."'";
            parse_str(ado_query2url($query));
            
            if(!$to_id) {
              $sql = "INSERT INTO towar (to_indeks) VALUES ('".$rek->towar->to_indeks."'); $query";
              parse_str(ado_query2url($sql));
              
              if(!$to_id) {
                echo '<br>';
                continue;
              }
            
            echo ", <b>nowy</b>";
            $ile_towarow_dodano++;
            }

            $ts_id = 0;
            $query = "SELECT ts_id FROM towar_sklep WHERE ts_to_id=$to_id AND ts_sk_id=$SKLEP_ID";
            parse_str(ado_query2url($query));
            if(!$ts_id) {
              $sql = "INSERT INTO towar_sklep (ts_to_id,ts_sk_id,ts_pri,ts_pri2) VALUES ($to_id,$SKLEP_ID,ts_pri_seq(),ts_pri2_seq()); $query";
              parse_str(ado_query2url($sql));
            }
            
            $tp_id = 0;
            $query = "SELECT tp_id FROM towar_parametry WHERE tp_to_id=$to_id";
            parse_str(ado_query2url($query));
            if(!$tp_id) {
              $sql = "INSERT INTO towar_parametry (tp_to_id) VALUES ($to_id); $query";
              parse_str(ado_query2url($sql));
            }

            $sql = tab_update2sql('towar',$rek->towar,"to_id=$to_id");
            if(strlen($sql)) $adodb->execute($sql);

            $sql = tab_update2sql('towar_parametry',$rek->towar_parametry,"tp_to_id=$to_id");
            if(strlen($sql)) $adodb->execute($sql);

            $rek->towar_sklep->ts_aktywny=($rek->towar_sklep->ts_cena)?1:0;

            $sql = tab_update2sql('towar_sklep',$rek->towar_sklep,"ts_to_id=$to_id AND ts_sk_id=$SKLEP_ID");
            if(strlen($sql)) $adodb->execute($sql);

            if(is_array($rek->kategoria)) {
              $sql = "DELETE FROM towar_kategoria WHERE tk_to_id=$to_id";
              $adodb->execute($sql);
              $kat = 0;
              
              foreach($rek->kategoria as $k) {
                if(!$kategoria[$k]) {
                  $kategoria[$k] = ka_id($k);
                  if(!$kategoria[$k]) {
                    $adodb->RollbackTrans();
                    continue 2;
                  }
                }
                
                $ka_id = $kategoria[$k];
                $sql = "INSERT INTO towar_kategoria (tk_to_id,tk_ka_id) VALUES ($to_id,$ka_id)";
                $adodb->execute($sql);
                $kat++;
              }
              echo ", $kat kat.";
            }

            if(is_array($rek->promocje)) {
              $sql = "DELETE FROM promocja_towaru WHERE pt_ts_id=$ts_id";
              $adodb->execute($sql);
              
              foreach($rek->promocje as $p) {
                $p+=0;
                if(!$p) continue;
                $pm_rabat_domyslny = 0;
                $query = "SELECT pm_rabat_domyslny,pm_symbol FROM promocja WHERE pm_id=$p";
                parse_str(ado_query2url($query));
                
                $wsp = (100-$pm_rabat_domyslny)/100;
                $sql = "INSERT INTO promocja_towaru (pt_ts_id,pt_pm_id,pt_cena) SELECT ts_id,$p,$wsp*ts_cena FROM towar_sklep WHERE ts_id=$ts_id";
                $adodb->execute($sql);
                
                echo ", $pm_symbol";
              }
            }
            
          $ile_towarow++;
          $adodb->CommitTrans();
          echo " ... <font color=blue>ok.</font><br>";
          } // if
        } // for        
      } // foreach
    } // while
  } // if
  
  echo $importer->printForm();

	if (is_array($hints))
	{
		while (list($sup,$hint) = each($hints))
			echo "<sup>$sup</sup> $hint<br><br>";
	}
  
//  ini_set('display_errors', 0);
?>
