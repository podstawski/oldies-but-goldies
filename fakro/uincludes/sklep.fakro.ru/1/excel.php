<?
	function fakro_charset_decode_utf_8($string) {
		if(! ereg("[\200-\237]", $string) and ! ereg("[\241-\377]", $string)) return $string;
		
		$string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e","'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",$string);
		$string = preg_replace("/([\300-\337])([\200-\277])/e","'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",$string);
		return $string;
		}

	function fakro_this2iso($f_text)
	{
		$f_text = strtr($f_text, '', 'Ж');
		$f_text = strtr($f_text, 'Й', 'Б');
		$f_text = strtr($f_text, '', 'М');
		$f_text = strtr($f_text, '', 'І');
		$f_text = strtr($f_text, 'Ѕ', 'Ё');
		$f_text = strtr($f_text, '', 'Ќ');
		
		return $f_text;
	}


	function fakro_iso2this($f_text)
	{
		$f_text = strtr($f_text, 'Ж', '');
		$f_text = strtr($f_text, 'Б', 'Й');
		$f_text = strtr($f_text, 'М', '');
		$f_text = strtr($f_text, 'І', '');
		$f_text = strtr($f_text, 'Ё', 'Ѕ');
		$f_text = strtr($f_text, 'Ќ', '');

		return $f_text;
	}


		function fakro_utf82iso88592($tekscik) 
		{
			 $tekscik = str_replace("\xC4\x85", "Б", $tekscik);
			 $tekscik = str_replace("\xC4\x84", 'Ё', $tekscik);
			 $tekscik = str_replace("\xC4\x87", 'ц', $tekscik);
			 $tekscik = str_replace("\xC4\x86", 'Ц', $tekscik);
			 $tekscik = str_replace("\xC4\x99", 'ъ', $tekscik);
			 $tekscik = str_replace("\xC4\x98", 'Ъ', $tekscik);
			 $tekscik = str_replace("\xC5\x82", 'Г', $tekscik);
			 $tekscik = str_replace("\xC5\x81", 'Ѓ', $tekscik);
			 $tekscik = str_replace("\xC5\x84", 'ё', $tekscik);    
			 $tekscik = str_replace("\xC5\x83", 'б', $tekscik);
			 $tekscik = str_replace("\xC3\xB3", 'ѓ', $tekscik);
			 $tekscik = str_replace("\xC3\x93", 'г', $tekscik);
			 $tekscik = str_replace("\xC5\x9B", 'Ж', $tekscik);
			 $tekscik = str_replace("\xC5\x9A", 'І', $tekscik);
			 $tekscik = str_replace("\xC5\xBC", 'П', $tekscik);
			 $tekscik = str_replace("\xC5\xBB", 'Џ', $tekscik);
			 $tekscik = str_replace("\xC5\xBA", 'М', $tekscik);
			 $tekscik = str_replace("\xC5\xB9", 'Ќ', $tekscik);
			 $tekscik = str_replace("т",'&#147;', $tekscik);
			 return $tekscik;
		} 


		function fakro_iso885922utf8($tekscik) 
		{
			//return unPolish($tekscik);
		  $iso88592 = array(
		   'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т',
		   'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т',
		   'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т', 'Т',
		   'Т', 'Т', 'Т ', 'Ф', 'Ы', 'Х', 'ТЄ', 'ФН', 'Х', 'ТЇ',
		   'ТЈ', 'Х ', 'Х', 'ХЄ', 'ХЙ', 'Т­', 'ХН', 'ХЛ', 'ТА', 'Ф',
		   'Ы', 'Х', 'ТД', 'ФО', 'Х', 'Ы', 'ТИ', 'ХЁ', 'Х', 'ХЅ',
		   'ХК', 'Ы', 'ХО', 'ХМ', 'Х', 'У', 'У', 'Ф', 'У', 'ФЙ',
		   'Ф', 'У', 'Ф', 'У', 'Ф', 'У', 'Ф', 'У', 'У', 'Ф',
		   'Ф', 'Х', 'Х', 'У', 'У', 'Х', 'У', 'У', 'Х', 'ХЎ',
		   'У', 'ХА', 'У', 'У', 'ХЂ', 'У', 'Х', 'УЁ', 'УЂ', 'Ф',
		   'УЄ', 'ФК', 'Ф', 'УЇ', 'Ф', 'УЉ', 'Ф', 'УЋ', 'Ф', 'У­',
		   'УЎ', 'Ф', 'Ф', 'Х', 'Х', 'УГ', 'УД', 'Х', 'УЖ', 'УЗ',
		   'Х', 'ХЏ', 'УК', 'ХБ', 'УМ', 'УН', 'ХЃ', 'Ы');
		  return preg_replace("/([\x80-\xFF])/e", '$iso88592[ord($1) - 0x80]', $tekscik);
		} 

		function fakro_arr2utf8(&$arr)
		{
			if (!is_array($arr)) 
			{
				$arr=fakro_iso885922utf8($arr);
				return;
			}
			while(list($k,$v)=each($arr))
			{
				fakro_arr2utf8(&$v);
				$arr[$k]=$v;
			}
		}


	function fakro_xml_nodes2attrib($obj,$exclude)
	{
		if (!is_object($obj)) return;
		if (!is_array($exclude)) $exclude=array($exclude);
		while (list($pole,$wart)=each($obj))
		{
			if (in_array($pole,$exclude)) continue;
			$wart=htmlspecialchars($wart);
			if (!strlen($wart)) continue;
			$wynik.=" $pole=\"$wart\"";
		}

		return $wynik;
	}


	function fakro_kameleon_wew_uchwyty(&$obj)
	{
		$prefix='_APP_WEW_UCHWYT(';
		$suffix_len=1;
		global $app;

		$a = is_array($obj);

		while(list($k,$sub)=each($obj))
		{
			if (is_array($sub) || is_object($sub) )
			{
				if ($a) fakro_kameleon_wew_uchwyty($obj[$k]);
				else fakro_kameleon_wew_uchwyty($obj->$k);
				continue;
			}

			if ( substr($sub,0,strlen($prefix))==$prefix )
			{
				$wew=substr($sub,strlen($prefix),strlen($sub)-strlen($prefix)-$suffix_len);
				$pos=strpos($wew,'(');
				if (method_exists($app,substr($wew,0,$pos))) 
				{
					eval("\$obj->\$k=\$app->$wew;");
				}
			}
		}
		reset ($obj);
	}




	function fakro_kameleon_xml_build_path($level)
	{
		global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;

		$path="";
		for ($i=0;$i<=$level;$i++)
		{
			if ($i) $path.="->";
			$path.=$kameleon_xml_stack[$i];
		}
		return($path);
	}

	function fakro_kameleon_xml_tag_start($parser, $name, $attrs)
	{
		global $kameleon_xml_obj;
		global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;
		global $kameleon_tag_end_extras,$kameleon_tag_ignore;

		$kameleon_xml_idx++;	
		$kameleon_xml_stack[$kameleon_xml_idx]=$name;
		$kameleon_xml_path=fakro_kameleon_xml_build_path($kameleon_xml_idx);

		eval("if (isset(\$$kameleon_xml_path)) \$arr_req=1;");
		if ($arr_req)
		{
			eval("if (is_array(\$$kameleon_xml_path)) \$arr_exists=1;");
			if (!$arr_exists)
			{
				eval("\$value=\$$kameleon_xml_path ;");
				if (!strlen($value)) $arr_req=0;
				if ($arr_req) eval("\$$kameleon_xml_path = array(\$$kameleon_xml_path) ;");
			}
			if ($arr_req)
			{
				eval("\$arr_size=sizeof(\$$kameleon_xml_path) ;");
				$kameleon_xml_stack[$kameleon_xml_idx]=$name."[$arr_size]";
				$kameleon_xml_path=fakro_kameleon_xml_build_path($kameleon_xml_idx);	
			}
		}

		if (strlen($attrs[id]))
		{
			$kameleon_xml_stack[$kameleon_xml_idx]=$name."[".$attrs[id]."]";
			$kameleon_xml_path=fakro_kameleon_xml_build_path($kameleon_xml_idx);	
		}
			 
		$kameleon_tag_ignore=0;

		$kameleon_tag_begin_extras="";
		$kameleon_tag_end_extras="";

		switch ($attrs[action])
		{
			case "sysmsg":
				$g=strlen($attrs[group])?$attrs[group]:'template';
				$kameleon_tag_begin_extras="_APP_WEW_UCHWYT(sysmsg(\"";
				$kameleon_tag_end_extras="\",'$g'))";
				break;
		}


		if ($kameleon_tag_ignore) return;
//		echo "\$$kameleon_xml_path = \"\";<hr>";
		eval("\$$kameleon_xml_path = \"\";" );
		$code="\$$kameleon_xml_path = \$$kameleon_xml_path . \$kameleon_tag_begin_extras ;" ;
		eval( $code);

	}

	function fakro_kameleon_xml_tag_end($parser, $name)
	{
		global $kameleon_xml_obj;
		global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;
		global $kameleon_tag_end_extras,$kameleon_tag_ignore;

		

		if (!$kameleon_tag_ignore && strlen($kameleon_tag_end_extras))
		{
			$code="\$$kameleon_xml_path = \$$kameleon_xml_path . \$kameleon_tag_end_extras ;";
			eval( $code);
			$kameleon_tag_end_extras="";
		}


		$kameleon_xml_idx--;
		$kameleon_xml_path=fakro_kameleon_xml_build_path($kameleon_xml_idx);

	}

	function fakro_kameleon_xml_data($parser,$data)
	{
		global $kameleon_xml_obj;
		global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;
		global $kameleon_tag_ignore;

		if (!strlen(trim($data)) || $kameleon_tag_ignore) return;

		$code="\$$kameleon_xml_path = \$$kameleon_xml_path . \$data ;" ;
		eval( $code);
	}

	function fakro_xml2obj($xml)
	{
		global $kameleon_xml_obj;
		global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;

		$kameleon_xml_obj="";

		$kameleon_xml_idx=0;	
		$kameleon_xml_stack[$kameleon_xml_idx]="kameleon_xml_obj";
		$kameleon_xml_path=fakro_kameleon_xml_build_path($kameleon_xml_idx);

		
		$parser = xml_parser_create(); 
		xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0); 
		xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1); 
		xml_parser_set_option($parser,XML_OPTION_SKIP_TAGSTART ,1);


		xml_set_element_handler($parser, "fakro_kameleon_xml_tag_start", "fakro_kameleon_xml_tag_end"); 
		xml_set_character_data_handler($parser, "fakro_kameleon_xml_data");
		if (!xml_parse($parser,$xml,1))
		{
				
			$wynik=xml_error_string(xml_get_error_code($parser));
			$wynik.="(line: ".xml_get_current_line_number($parser);	
			$wynik.=")";
			return $wynik;
		}

		xml_parser_free($parser); 
		global $app;

		//$app->puke($kameleon_xml_obj,'xml-transformation');
		fakro_kameleon_wew_uchwyty($kameleon_xml_obj);
		//$app->puke($kameleon_xml_obj,'xml-transformation');
		return $kameleon_xml_obj;
	}


	function fakro_obj2xml($obj,$XmlTag="xml",$depth=0)
	{
		$wynik="";

		if (strlen($XmlTag))
		{
			$bXmlTag=true;
			if (is_Array($obj))
			{
				$ak=array_keys($obj);
				if ($ak[0]=="0") $bXmlTag=false;
			}
		}


		if ($bXmlTag) 
		{
			for ($d=0; $d<$depth; $d++) $wynik.="\t";
			$wynik.="<$XmlTag>\n";
		}

		if ( is_Array($obj) || is_Object($obj) )
		{
			while(list($k,$v)=each($obj))
			{
				if (strlen($XmlTag) && is_Integer($k)) $k=$XmlTag;

				
				$bList=false;
				if (is_Array($v))
				{
					$ak=array_keys($v);
					if ($ak[0]=="0") $bList=true;
				}
				
				if (!$bList) 
				{
					for ($d=0; $d<$depth; $d++) $wynik.="\t";
					$wynik.="<$k>";
				}
				if ( is_Array($v) || is_Object($v) )
				{
					$tag="";
					if (!$bList) $wynik.="\n";
					else $tag=$k;
					$wynik.=fakro_obj2xml($v,$tag,$depth+1);
				}
				else
					$wynik.=htmlspecialchars($v);

				if (!$bList)
				{
					for ($d=0; $d<$depth && (is_Array($v) || is_Object($v)); $d++) $wynik.="\t";
					$wynik.="</$k>\n";
				}
			}
		}
		if ($bXmlTag) 
		{
			for ($d=0; $d<$depth; $d++) $wynik.="\t";
			$wynik.="</$XmlTag>\n";
		}
		
		return $wynik;
	}


	define('NULL_STRING','fakroEmptyCellIKJDSAKASHDKJHKHDKFHASDJFH');


	function excelxml2array($file,$zamien=array())
	{
		$xml=trim(implode('',file($file)));

		if (strtolower(substr($xml,0,5))!='<?xml')
		{
			echo basename($argv[1])." &nbsp;&nbsp;&nbsp;->&nbsp;&nbsp;&nbsp; to nie jest plik w formacie XML";
			return;

		}
/*
		if (!strstr(strtolower(substr($xml,0,80)),'excel.sheet'))
		{
			echo basename($argv[1])." &nbsp;&nbsp;&nbsp;->&nbsp;&nbsp;&nbsp; to nie jest arkusz Excela w formacie XML";
			return;

		}
*/
		/* $xml=fakro_utf82iso88592($xml); */
		/* $xml=fakro_charset_decode_utf_8($xml); */
		$xml=enc2enc($xml,'utf-8','iso-8859-5');

		$xml=eregi_replace("<([^> ]+)([^>/]*)/>","<\\1\\2></\\1>",$xml);

		$xml=eregi_replace("(<Cell[^>]*)ss:Index=\"([0-9]+)\"([^/>])*>","\\1\\3><Pos>\\2</Pos>",$xml);
		$xml=eregi_replace("(<Row[^>]*)ss:Index=\"([0-9]+)\"([^/>])*>","\\1\\3><Pos>\\2</Pos>",$xml);
		$xml=eregi_replace("(<Worksheet[^>]*)ss:Name=\"([^\"]+)\"([^/>])*>","\\1\\3><Name>\\2</Name>",$xml);
		$xml=eregi_replace("<\?[^>]+>","",$xml);


		$xml=eregi_replace("(<[/]*)ss:([a-z]+)","\\1\\2",$xml);
		$xml=eregi_replace("<([a-z0-9]+)[^>/]*(/*>)","<\\1\\2",$xml);		

		//$xml=str_replace('<Cell/>','<Cell></Cell>',$xml);

		$xml=str_replace('<Cell></Cell>','<Cell><Data>'.NULL_STRING.'</Data></Cell>',$xml);

		$xml=trim($xml);

	
		//fakro_xml_debug($xml);
		$obj=fakro_xml2obj($xml);
		
		if (!is_object($obj))
		{
			//echo "$obj<hr>";
			fakro_xml_debug($xml);
			return;
		}

		$sheets=$obj->Workbook->Worksheet;

		if (is_object($sheets)) $sheets=array($sheets);
		

		foreach ($sheets AS $sheet)
		{
			if (!is_object($sheet->Table)) continue;

			$table=$sheet->Table->Row;
			$wynik[$sheet->Name]=fakro_RowTable2Array($table);
		}
		return $wynik;

	}


	function fakro_xml_debug($xml)
	{
		echo "<pre>";
		$tab=explode("\n",$xml);
		for($i=0;$i<count($tab);$i++)
		{
			echo sprintf("%03d",$i+1);
			echo htmlspecialchars($tab[$i]);
		}
		echo "</pre>";
	}

	function fakro_RowTable2Array($table)
	{
		$row=0;
		for ($i=0;$i<count($table);$i++)
		{
			if ($table[$i]->Pos)
			{
				for ($j=$row;$j<$table[$i]->Pos ;$j++ ) $wynik[$row++]=array();
				$pos=$table[$i]->Pos-1;
			}
			if (is_object($table[$i]->Cell)) $table[$i]->Cell=array($table[$i]->Cell);
			$r=array();
			$cell=0;
			for($j=0;$j<count($table[$i]->Cell);$j++)
			{
				if (NULL_STRING==$table[$i]->Cell[$j]->Data) $table[$i]->Cell[$j]->Data='';
				$pos=$table[$i]->Cell[$j]->Pos;
				while ($pos>0 && $pos-1>$cell) $r[$cell++]='';
				$r[$cell++]=$table[$i]->Cell[$j]->Data;

			}
			$wynik[$row++]=$r;
		}

		return $wynik;
	}



?>

