<?

	include($INCLUDE_PATH."/excel.php");
	require_once($INCLUDE_PATH."/importer.class.php");
	require_once($INCLUDE_PATH."/debug.window.php");

	$importer = new Importer($UFILES."/import_pliki");

	$files_to_import = $importer->getFilesToImport();

	function indexOfkod($txt)
	{
		for ($i=0; $i<count($txt);$i++)
		{
			$t=$txt[$i];
			if (preg_match( '/^[0-9]{2}-[0-9]{3}$/',$t))
				return $i;
		}
		return -1;
	}
	


	if (is_array($files_to_import))
	{
		debug_window('start');
		reset($files_to_import);
		while (list(,$filename) = each($files_to_import))
		{

			if (!file_exists($importer->directory."/".$filename)) continue;
			$xml = excelxml2array($importer->directory."/".$filename);

			reset($xml);
			//$sql = "DELETE FROM punkty_sprzedazy WHERE ps_typ='D'";
			//$fakrodb->execute($sql);

			while (list($woj,$punkty) = each($xml))
			{
				if (!strlen($woj)) continue;

				$w=win2iso($woj);
				debug_window($w);
				$sql = "DELETE FROM punkty_sprzedazy WHERE ps_wojewodztwo = '$w' AND ps_typ='D'";
				$fakrodb->execute($sql);

				for ($i=0; $i < count($punkty); $i++)
				{
					$punkt = $punkty[$i];
					
					for($k=0; $k < count($punkt); $k++)
						if (!is_array($punkt[$k]) && !is_object($punkt[$k]))
							$punkt[$k] = trim(addslashes(stripslashes($punkt[$k])));
						else
						{
							$str = "";
							if (is_array($punkt[$k]->Font))
								foreach($punkt[$k]->Font as $pf)
									$str.= trim(addslashes(stripslashes(str_replace("â","\"",str_replace("","\"",$pf)))));
							$punkt[$k] = $str;
						}

					$gdzie = indexOfkod($punkt);
					if ($gdzie==-1) continue;

					$nazwa = $punkt[$gdzie-2];
					$kod = $punkt[$gdzie];
					$miasto = $punkt[$gdzie+1];
					$adres = $punkt[$gdzie-1];
					$kontakt = $punkt[$gdzie+2];
					$imie= $punkt[$gdzie-4];
					$nazwisko= $punkt[$gdzie-3];

					$sql = "INSERT INTO punkty_sprzedazy (
							ps_nazwa,
							ps_kod,
							ps_adres,
							ps_miasto,
							ps_kontakt,
							ps_data,
							ps_wojewodztwo,
							ps_typ,
							d_imie,d_nazwisko)
							VALUES
							(
							'$nazwa',
							'$kod',
							'$adres',
							'$miasto',
							'$kontakt',
							".time().",
							'$woj',
							'D',
							'$imie','$nazwisko')";
					$fakrodb->execute(win2iso($sql));
				}
			}
			echo "plik $filename - znaleziono ".count($xml)." arkuszy<br>";
		}
	}

	echo $importer->printForm();
?>
