<?
	//wejscie: tablica linii tekstowych (pierwsza linia zawiera nag³ówki - nie brana pod uwagê
	//wyjecie: XML

	

	function import_convert($text_a)
	{
		$deli=",";
		for ($i=0;$i<count($text_a);$i++)
		{
			$l=explode($deli,trim($text_a[$i]));		// $l - linia
			for ($j=0;$j<count($l);$j++) 
			{
				$l[$j] = trim($l[$j]);
				$str=$l[$j];
				if ($str[0]=="\"" && $str[strlen($str)-1]=="\"") $l[$j]=substr($str,1,strlen($str)-2); 
				if ($str[0]=="\"" && $str[strlen($str)-1]!="\"")
				{
					$l[$j]=substr($str,1);

					for ($jj=$j+1;$jj<count($l);$jj++)
					{
						$str=trim($l[$jj]);
						if ($str[strlen($str)-1]=="\"")
						{
							$str=substr($str,0,strlen($str)-1);
							$l[$j].=$deli.$str;
							break;
						}
						$l[$j].=$deli.$str;
					}
					for ($jjj=$j+1;$jjj<=$jj;$jjj++)
					{
						$l[$jjj]=$l[$jjj+$jj+$j];
						$l[$jjj+$jj+$j]="";
					}

				}


				$l[$j]=ereg_replace("\"\"","\"",$l[$j]);
			}


			$r="";									// $r - rekord

			$r[indeks]=$l[0];
			$r[ilosc]=$l[1];
			$r[linia]=$i+1;
			$towar[]=$r;
		}
		$obj->stan->towar=$towar;
		$wynik=obj2xml($obj,"magazyn");

		//echo_xml($wynik);
		mail("camel@gammanet.pl","o shit, to do ciebie idzie ten Xml",$wynik);

		return $wynik;
	}


	function toFloat($f)
	{
		$f=ereg_replace("[^0-9,]*","",$f);
		$f=ereg_replace(",",".",$f);
		return $f+0;
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
