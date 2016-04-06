<?
	//wejscie: tablica linii tekstowych (pierwsza linia zawiera nag³ówki - nie brana pod uwagê
	//wyjecie: XML

	

	function import_convert($text_a)
	{
		if (!is_array($text_a)) return;

		$l=explode(";",trim($text_a[0]));
		for ($i=1;$i<count($l);$i++)
		{
			$co=ereg_replace(".*\[(.+)\].*","\\1",$l[$i]);

			if (strlen($co)<=2) $LANGS[$co]=$i;
		}

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
			
			if (!strlen($l[1])) continue;

			$r[label]=$l[1];
			$r[group]=$l[2];
			foreach ( array_keys($LANGS) AS $lang ) $r[langs][$lang]=$l[$LANGS[$lang]]; 


			$messages[]=$r;
		}
		$obj->messages=$messages;
		$wynik=obj2xml($obj,"magazyn");

		//echo_xml($wynik);
		//mail("camel@gammanet.pl","to do ciebie idzie ten Xml",$wynik);
		return $wynik;
	}



	/*
	function echo_xml($txt)
	{
		$txt=htmlspecialchars($txt);
		$txt=ereg_replace(" ","&nbsp;",$txt);
		$txt=ereg_replace("\t","&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$txt);
		$txt=ereg_replace("\n","<br>",$txt);
		echo $txt;
	}
	*/
?>
