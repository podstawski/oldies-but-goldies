<?
	include_once("$INCLUDE_PATH/excel.php");
	require_once("$INCLUDE_PATH/importer.class.php");

	//$importer = new Importer("/var/tmp/cenniki");
	$importer = new Importer($UFILES."/import_cenniki");

	$files_to_import = $importer->getFilesToImport();

	function drainForText($obj)
	{
		if (is_object($obj) || is_array($obj))
		{
			reset($obj);
			while(list(,$val) = each($obj))
				return drainForText($val);
		}
		else
			return $obj;
	}

	if (is_array($files_to_import))
	{
		reset($files_to_import);
		while (list(,$filename) = each($files_to_import))
		{

			if (!file_exists($importer->directory."/".$filename)) continue;
			$xml = excelxml2array($importer->directory."/".$filename);
			reset($xml);
			$ile_towarow = 0;
			while (list(,$cell) = each($xml))
			{
				for ($i=0; $i < count($cell); $i++)
				{
					$row = $cell[$i];

					if (count($row) < 2) continue;

					if (trim(strtoupper($row[0])) == "ROZMIARY")
					{
						for ($r=1; $r < count($row); $r++)
							$rozmiary["".$row[$r].""]=$r;

					}
					else
						if (count($rozmiary) && trim(strtoupper($row[0])) != "CENA BRUTTO")
						{
							$row[0] = drainForText($row[0]);
							$nazwa_produktu = trim(str_replace("*","",$row[0]));
//							echo $nazwa_produktu."<br>";continue;
							$sql = "SELECT ka_id FROM kategorie WHERE ka_nazwa = '$nazwa_produktu'";
							$ka_id = "";
							parse_str(ado_query2url($sql));
							if (!strlen($ka_id))
							{
								echo "Brak kategorii $nazwa_produktu<hr>";
								continue;
							}
							$sql = "SELECT ka_id FROM kategorie WHERE ka_parent = $ka_id";
							$res = $adodb->execute($sql);
							$lista_kategorii = $ka_id;
							for ($x=0; $x < $res->RecordCount(); $x++)
							{
								parse_str(ado_explodename($res,$x));
								$lista_kategorii.= ",".$ka_id;
							}

							reset($rozmiary);
							while (list($wymiary,$indeks) = each($rozmiary))
							{
								if (!strlen(trim($row[$indeks]))) continue;
								list($a,$b) = explode("x",$wymiary);

								$sql = "SELECT ts_to_id FROM towar_sklep, towar_parametry, towar_kategoria
										WHERE tp_to_id = tk_to_id AND tk_ka_id IN ($lista_kategorii)
										AND tp_to_id = ts_to_id AND ts_sk_id = $SKLEP_ID
										AND tp_a = '$a' AND tp_b = '$b'";

								$res = $adodb->execute($sql);

								for ($x=0; $x < $res->RecordCount(); $x++)
								{
									parse_str(ado_explodename($res,$x));
									$sql = "UPDATE towar_sklep
											SET ts_cena = ".str_replace(",",".",$row[$indeks])."
											WHERE ts_to_id = $ts_to_id";
									$adodb->execute($sql);
									$ile_towarow++;

								}//for
								
							}//while

						}//else if

				}//for

			}//while

		}
		echo "Uaktualniono ".$ile_towarow." towarów<br>";
	}

	echo $importer->printForm();
?>
