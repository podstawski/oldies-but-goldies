<?
	
	function param2form($name,$obj,$types,$values,$arrayof="")
	{
		//print_r($types);
		if (!is_array($obj[elements])) return "";

		while ( list($k,$v) = each ($obj[elements]) )
		{
			$type=$v[type];
			while ($pos=strpos($type,":")) $type=substr($type,$pos+1);

			if (in_array($type,array_keys($types)))
			{
				$aof="";
				if (substr(strtolower($type),0,7)=="arrayof") $aof="ArrayOf";
				$wynik.=param2form("$name:$arrayof$k",$types[$type],$types,$values["$arrayof$k"],$aof);
			}
			else
			{
				$val=$values[$k];
				$wynik.="<tr><td>$name.$k</td><td><input name=\"WS[$name:$k]\" size=60 value=\"$val\"></td></tr>\n";
			}

			//echo "<br> $k $type";

		}
		return $wynik;
	}


	function ws_result_line($var,$result)
	{
		$va=explode(";",$var);
		$result=addslashes(stripslashes($result));

		for ($i=0;$i<count($va);$i++)
		{
			if (!strlen($va[$i])) continue;
			
			$v=explode("|",$va[$i]);
			$res="\"$result\"";
			if (strlen($v[1])) $res=ereg_replace("@",$res,$v[1]);
	
			$wynik.=$v[0]."=$res;\n";
		}

		return $wynik;
	}

	function ws_result_string($result,$output,$arr_indx="")
	{

		if (!is_array($result))
		{
			if (!strlen($result)) return "";
			if (count($output)==1)
			{
				foreach($output AS $o) $output = $o;
			}

			if (!is_array($output)) $wynik=ws_result_line($output,$result);

			return $wynik;
		}

		if (count($result)==1 && count($output)==1)
		{
			$ak=array_keys($output);
			if (substr(strtolower($ak[0]),0,7)=="arrayof")
			{
				$indx=substr($ak[0],7);
				if (is_array($result[$indx]))
				{
					$result[0]=$result[$indx];
					unset($result[$indx]);
				}
			}

		}

		while (list($k,$v)=each($result))
		{
			$ki=$k+0;
			if ("$ki"=="$k")
			{
				if (is_array($v))
				{
					$key="";
					foreach(array_keys($output) AS $ak)
					{
						if (substr(strtolower($ak),0,7)=="arrayof") $key=$ak;
					}
					if (!strlen($key)) continue;

					$wynik.=ws_result_string($v,$output[$key],$k);

				}
				continue;
			}

			
			if (!is_array($v)) 
			{
				$v=utf82iso88592($v);
				$variable=$output[$k];
				if (strlen($arr_indx)) 
				{
					$variable=str_replace("[\$i]","[$arr_indx]",$variable);
				}
				if (strlen($variable)) $wynik.=ws_result_line($variable,$v);
			}
			else
			{
				$wynik.=ws_result_string($v,$output[$k],$arr_indx);
			}

		}
		return $wynik;
	}

	function createInputSubstr ($arrname,$arr,$loop_idx=0)
	{
		//if (!is_array($arr)) return "\$$arrname=$arr;\n";

		while (list($k,$v)=each($arr))
		{
			if (!is_array($v)) 
			{
				$token="pizda8723jhjhsgdsdj";
				$vv=ereg_replace("\[\\$([i-o])\]","\\1$token",$v);
				$pos=strpos($vv,$token);
				if ($pos && $v[0]=="\$") 
				{
					$letter=substr($vv,$pos-1,1);
					$vv=substr($vv,0,$pos-1);
					$wynik.="if (\$$letter>=count($vv)) \$loop_$letter=0;\n";
				}
				$wynik.="$arrname"."[$k]=$v;\n";
			}
			elseif (substr(strtolower($k),0,7)!="arrayof")
				$wynik.=createInputSubstr ($arrname."[$k]",$v,$loop_idx);
			else
			{
				
				$subname=substr($k,7);
				$letter=chr(ord("i")+$loop_idx);
				$wynik.="\$loop_$letter=1;\nfor(\$$letter=0;\$loop_$letter;\$$letter++)\n{\n";
				$wynik.=createInputSubstr ("if (\$loop_$letter) ".$arrname."[$subname][\$i]",$v,$loop_idx+1);
				$wynik.="}\n";
			}
		}
		return $wynik;
	}


	function utf82iso88592($tekscik) 
	{
		 $tekscik = str_replace("\xC4\x85", "ą", $tekscik);
		 $tekscik = str_replace("\xC4\x84", 'Ą', $tekscik);
		 $tekscik = str_replace("\xC4\x87", 'ć', $tekscik);
		 $tekscik = str_replace("\xC4\x86", 'Ć', $tekscik);
		 $tekscik = str_replace("\xC4\x99", 'ę', $tekscik);
		 $tekscik = str_replace("\xC4\x98", 'Ę', $tekscik);
		 $tekscik = str_replace("\xC5\x82", 'ł', $tekscik);
		 $tekscik = str_replace("\xC5\x81", 'Ł', $tekscik);
		 $tekscik = str_replace("\xC5\x84", 'ń', $tekscik);    
		 $tekscik = str_replace("\xC5\x83", 'Ń', $tekscik);
		 $tekscik = str_replace("\xC3\xB3", 'ó', $tekscik);
		 $tekscik = str_replace("\xC3\x93", 'Ó', $tekscik);
		 $tekscik = str_replace("\xC5\x9B", 'ś', $tekscik);
		 $tekscik = str_replace("\xC5\x9A", 'Ś', $tekscik);
		 $tekscik = str_replace("\xC5\xBC", 'ż', $tekscik);
		 $tekscik = str_replace("\xC5\xBB", 'Ż', $tekscik);
		 $tekscik = str_replace("\xC5\xBA", 'ź', $tekscik);
		 $tekscik = str_replace("\xC5\xB9", 'Ź', $tekscik);
		 $tekscik = str_replace("â",'&#147;', $tekscik);
		 return $tekscik;
	} 


	function iso885922utf8($tekscik) 
	{
		//return unPolish($tekscik);
	  $iso88592 = array(
	   'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â',
	   'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â',
	   'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â', 'Â',
	   'Â', 'Â', 'Â ', 'Ä', 'Ë', 'Ĺ', 'Â¤', 'Ä˝', 'Ĺ', 'Â§',
	   'Â¨', 'Ĺ ', 'Ĺ', 'Ĺ¤', 'Ĺš', 'Â­', 'Ĺ˝', 'Ĺť', 'Â°', 'Ä',
	   'Ë', 'Ĺ', 'Â´', 'Äž', 'Ĺ', 'Ë', 'Â¸', 'ĹĄ', 'Ĺ', 'ĹĽ',
	   'Ĺş', 'Ë', 'Ĺž', 'Ĺź', 'Ĺ', 'Ă', 'Ă', 'Ä', 'Ă', 'Äš',
	   'Ä', 'Ă', 'Ä', 'Ă', 'Ä', 'Ă', 'Ä', 'Ă', 'Ă', 'Ä',
	   'Ä', 'Ĺ', 'Ĺ', 'Ă', 'Ă', 'Ĺ', 'Ă', 'Ă', 'Ĺ', 'ĹŽ',
	   'Ă', 'Ĺ°', 'Ă', 'Ă', 'Ĺ˘', 'Ă', 'Ĺ', 'ĂĄ', 'Ă˘', 'Ä',
	   'Ă¤', 'Äş', 'Ä', 'Ă§', 'Ä', 'ĂŠ', 'Ä', 'ĂŤ', 'Ä', 'Ă­',
	   'ĂŽ', 'Ä', 'Ä', 'Ĺ', 'Ĺ', 'Ăł', 'Ă´', 'Ĺ', 'Ăś', 'Ăˇ',
	   'Ĺ', 'ĹŻ', 'Ăş', 'Ĺą', 'Ăź', 'Ă˝', 'ĹŁ', 'Ë');
	  return preg_replace("/([\x80-\xFF])/e", '$iso88592[ord($1) - 0x80]', $tekscik);
	} 

	function arr2utf8(&$arr)
	{
		if (!is_array($arr)) 
		{
			$arr=iso885922utf8($arr);
			return;
		}
		while(list($k,$v)=each($arr))
		{
			arr2utf8(&$v);
			$arr[$k]=$v;
		}
	}

?>
