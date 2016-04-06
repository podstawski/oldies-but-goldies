<?
	//wejscie: tablica linii tekstowych (pierwsza linia zawiera nagГѓwki - nie brana pod uwagъ
	//wyjecie: XML

	

	function import_convert($text_a)
	{
		for ($i=1;$i<count($text_a);$i++)
		{
			$l=explode(";",trim($text_a[$i]));		// $l - linia
			for ($j=0;$j<count($l);$j++) 
			{
				$l[$j] = trim($l[$j]);
				$str=$l[$j];
				if ($str[0]=="\"" && $str[strlen($str)-1]=="\"") $l[$j]=substr($str,1,strlen($str)-2); 
				$l[$j]=ereg_replace("\"\"","\"",$l[$j]);
			}
			$r="";									// $r - rekord
			$p="";									// $p - parametry
			$k="";									// $k - tablica kategoria

			$r[indeks]=$l[1];
			$r[nazwa]=$l[2];
			$r[jm]=$l[24];
			$r[linia]=$i+1;
			

			$KAT=array();
			for ($j=5;$j<8;$j++) if (strlen($l[$j])) $k[]=trim($l[$j]);

			$k1=$k;
			$k2=$k;
			for ($j=8;$j<10;$j++)
			{
				$k1=$k;
				$k2=$k;
				if (!strlen($l[$j])) continue;
				$k1[]=trim($l[$j]);
				for ($jj=10;$jj<12;$jj++)
				{
					$k2=$k1;
					if (!strlen($l[$jj])) continue; 
					$k2[]=trim($l[$jj]);
					$KAT[]=$k2;
				}
				if (!count($KAT)) $KAT[]=$k1; 
			}
			if (!count($KAT)) $KAT[]=$k;

			for ($j=0;$j<count($KAT);$j++) $KAT[$j]=implode("|",$KAT[$j]);
			$r[kategoria]=array_unique($KAT);


			if (strlen($l[13])) $p->zastosowanie=$l[14];
			if (strlen($l[14])) $p->a=toFloat($l[14]);
			if (strlen($l[15])) $p->b=toFloat($l[15]);
			if (strlen($l[16])) $p->c=toFloat($l[16]);
			if (strlen($l[17])) $p->d=toFloat($l[17]);
			if (strlen($l[18])) $p->l=toFloat($l[18]);
			if (strlen($l[19])) $p->r1=toFloat($l[19]);
			if (strlen($l[20])) $p->r2=toFloat($l[20]);
			if (strlen($l[21])) $p->o=toFloat($l[21]);
			if (strlen($l[22])) $p->gatunek=$l[22];
			if (strlen($l[23])) $p->stan=$l[23];
			if (strlen($l[4])) $p->m_jm=toFloat($l[4]);
			if (strlen($l[25])) $p->m_m=toFloat($l[25]);
			if (strlen($l[26])) $p->m_m2=toFloat($l[26]);
			if (strlen($l[27])) $p->m_szt=toFloat($l[27]);
			$r[parametry]=$p;


			$towar[]=$r;
		}
		$obj->towar=$towar;
		$wynik=obj2xml($obj,"magazyn");

		//echo_xml($wynik);
		//mail("camel@gammanet.pl","o kurwa, to do ciebie idzie ten Xml",$wynik);
		return $wynik;
	}



	function echo_xml($txt)
	{
		$txt=htmlspecialchars($txt);
		$txt=ereg_replace(" ","&nbsp;",$txt);
		$txt=ereg_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$txt);
		$txt=ereg_replace("\n","<br>",$txt);
		echo $txt;
	}
?>
