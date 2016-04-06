<?

	/*
	ini_set('display_errors','On');

	$dir=$UFILES.'/..';
	if (is_dir($dir)) {
    		if ($dh = opendir($dir)) {
        		while (($file = readdir($dh)) !== false) {
				if ($file[0]=='.') continue;
				mkdir ($dir.'/'.$file.'/.root');

				if (!is_dir($dir.'/'.$file.'/.root')) continue;

				copy($dir.'/robots.txt',$dir.'/'.$file.'/.root/robots.txt');
				copy($dir.'/favicon.ico',$dir.'/'.$file.'/.root/favicon.ico');

				echo "cp $dir/favicon.ico $dir/$file/.root <br>";
        		}
        		closedir($dh);
    		}
	}


	die();
	*/

	$sql="	
			ALTER TABLE towar RENAME to_opis_m_i TO to_opis_m_pl;
			ALTER TABLE towar RENAME to_opis_d_i TO to_opis_d_pl;

			ALTER TABLE kategorie RENAME ka_opis_m_i TO ka_opis_m_pl;
			ALTER TABLE kategorie RENAME ka_opis_d_i TO ka_opis_d_pl;			

			UPDATE messages SET msg_lang='pl' WHERE msg_lang='i';
	
	";


	foreach (explode(";",$sql) AS $q)
	{

		$WM->execute($q);
	}

	
