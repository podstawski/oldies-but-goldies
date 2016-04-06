<?

	
	$dir="$SKLEP_INCLUDE_PATH/update";

	if (!file_exists($dir)) return;

	$query="SELECT * FROM system_update";
	$res=$projdb->SelectLimit($query,1,0);



	if (!is_object($res))
	{
		ob_start();
		include("$dir/.schema.sql");
		$query=trim(ob_get_contents());
		ob_end_clean();
		if ($projdb->Execute($query))
				echo "Wykonano inicjacjê<br>";
		else
		{
			echo "Nie uda³o siê dokonaæ inicjacji<br>";
			if ($KAMELEON_MODE)
			{
				$projdb->debug=1;
				echo "<pre>";
				$projdb->Execute($query);
				$projdb->debug=0;
				echo "</pre>";
			}
		}

		if ($KAMELEON_MODE)
		{
			
			
			$query="SELECT nazwa FROM servers WHERE id=$SERVER_ID";
			$r=$kameleon_adodb->Execute($query);
			parse_str(ado_explodeName($r,0));

			$query="INSERT INTO sklep (sk_server,sk_nazwa) VALUES ($SERVER_ID,'$nazwa');
					SELECT max(sk_id) AS sk_id FROM sklep;
					";
			parse_str(query2url($query));

			$query="INSERT INTO magazyn (ma_nazwa,ma_glowny) VALUES ('$nazwa',1);
					SELECT max(ma_id) AS ma_id FROM magazyn;
					";
			parse_str(query2url($query));

			$query="INSERT INTO magazyn_sklep (ms_ma_id,ms_sk_id) VALUES ($ma_id,$sk_id)";
			parse_str(query2url($query));


		}
	}

	//return;

	if ($dh = opendir($dir)) 
	{
		while (($file = readdir($dh)) !== false)
		{
			if ($file[0]==".") continue;

			$su_data=0;
			$query="SELECT su_data FROM system_update WHERE su_klucz='$file'";
			parse_str(ado_query2url($query));
			if ($su_data) continue;

			ob_start();
			include("$dir/$file");
			$sql=trim(ob_get_contents());
			ob_end_clean();

			if (!strlen($sql)) continue;
			
			$sql_e=addslashes($sql);
			$query="$sql; 
						INSERT INTO system_update(su_klucz,su_data,su_sql)
						VALUES ('$file',$NOW,'$sql_e');";

			if ($projdb->Execute($query))
				echo "Wykonano zmianê w bazie danych na podstawie pliku $file<br>";
			else
			{
				echo "Nie uda³o siê dokonaæ zmian na podstawie pliku $file<br>";
				if ($KAMELEON_MODE)
				{
					$projdb->debug=1;
					echo "<pre>";
					$projdb->Execute($query);
					$projdb->debug=0;
					echo "</pre>";
				}
			}
			

		}
		closedir($dh);
	}



?>
