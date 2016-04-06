<?

	$razem=0;
	$dodano=0;
	$dupa=0;
	$cz_start=time();

	if (!is_array($obj->magazyn->stan)) $obj->magazyn->stan = array($obj->magazyn->stan);

	while(list($mag,$stany)=each($obj->magazyn->stan))
	{
		if (!$mag) $query="SELECT ma_id FROM magazyn WHERE ma_glowny=1";
		else $query="SELECT ma_id FROM magazyn WHERE ma_nazwa='$mag'";
		parse_str(ado_query2url($query));

		if (!$ma_id) 
		{
			echo $mag ? "Nieznany magazyn <B>$mag</B><br>" : "Brak gГѓwnego magazynu<br>";
			continue;
		}

		if (!is_array($stany->towar)) $stany->towar = array($stany->towar);

		//echo count($stany->towar);

		foreach ($stany->towar AS $towar)
		{
			$to_id='';
			$i=addslashes(stripslashes($towar->indeks));
			$query="SELECT to_id FROM towar WHERE to_indeks='$i'";
			parse_str(ado_query2url($query));

			if (!$to_id) 
			{
				$dupa++;
				echo sprintf("%02d",$dupa).". nieznany indeks: <B>$towar->indeks</B> (linia $towar->linia)<br>";
				continue;
			}

			$sm_id=0;
			$query="SELECT sm_id FROM stany_magazynowe WHERE sm_to_id=$to_id AND sm_ma_id=$ma_id";
			parse_str(ado_query2url($query));


			if (!$sm_id)
			{
				$query="INSERT INTO stany_magazynowe (sm_ilosc,sm_to_id,sm_ma_id) VALUES (0,$to_id,$ma_id)";
				parse_str(ado_query2url($query));
			}

			$WM->towar_ruch(0,$to_id,"Ustalono przez administratora",0,$towar->ilosc,$ma_id);


			$razem++;
			if (!($razem%100) || $razem==1)
			{
				$txt="";
				if ($LINIA["od"]) $txt.="Start w linii: ".number_format($LINIA["od"],0," "," ")."<br><br>";

				$txt.="Razem: ".number_format(count($obj->magazyn->towar),0," "," ");
				$txt.="<br>Zanalizowano $razem pozycji z czego dodano $dodano.";
		
				$czas=time()-$cz_start;
				$sek=$czas%60;
				$czas=floor($czas/60);
				$txt.="<br>Czas: $czas min. $sek s.";

				$czas=time()-$import_start_time;
				$sek=$czas%60;
				$czas=floor($czas/60);
				$txt.="<br>Czas od startu: $czas min. $sek s.";
				

				echo "<script> import_debug('$txt'); </script>";
				ob_flush();
				flush();
			}





		}
	}
	
?>
