<?
if (function_exists("xml2obj")) return;

function kameleon_utf82iso88592($tekscik) 
{
	 $tekscik = str_replace("\xC4\x85", "�", $tekscik);
	 $tekscik = str_replace("\xC4\x84", '�', $tekscik);
	 $tekscik = str_replace("\xC4\x87", '�', $tekscik);
	 $tekscik = str_replace("\xC4\x86", '�', $tekscik);
	 $tekscik = str_replace("\xC4\x99", '�', $tekscik);
	 $tekscik = str_replace("\xC4\x98", '�', $tekscik);
	 $tekscik = str_replace("\xC5\x82", '�', $tekscik);
	 $tekscik = str_replace("\xC5\x81", '�', $tekscik);
	 $tekscik = str_replace("\xC5\x84", '�', $tekscik);    
	 $tekscik = str_replace("\xC5\x83", '�', $tekscik);
	 $tekscik = str_replace("\xC3\xB3", '�', $tekscik);
	 $tekscik = str_replace("\xC3\x93", '�', $tekscik);
	 $tekscik = str_replace("\xC5\x9B", '�', $tekscik);
	 $tekscik = str_replace("\xC5\x9A", '�', $tekscik);
	 $tekscik = str_replace("\xC5\xBC", '�', $tekscik);
	 $tekscik = str_replace("\xC5\xBB", '�', $tekscik);
	 $tekscik = str_replace("\xC5\xBA", '�', $tekscik);
	 $tekscik = str_replace("\xC5\xB9", '�', $tekscik);
	 $tekscik = str_replace("”",'&#147;', $tekscik);
	 return $tekscik;
} 


function kameleon_iso885922utf8($tekscik) 
{
	//return unPolish($tekscik);
  $iso88592 = array(
   '', '', '', '', '', '', '', '', '', '',
   '', '', '', '', '', '', '', '', '', '',
   '', '', '', '', '', '', '', '', '', '',
   '', '', '� ', 'Ą', '˘', 'Ł', '¤', 'Ľ', 'Ś', '§',
   '¨', '� ', 'Ş', 'Ť', 'Ź', '­', 'Ž', 'Ż', '°', 'ą',
   '˛', 'ł', '´', 'ľ', 'ś', 'ˇ', '¸', 'š', 'ş', 'ť',
   'ź', '˝', 'ž', 'ż', 'Ŕ', 'Á', 'Â', 'Ă', 'Ä', 'Ĺ',
   'Ć', 'Ç', 'Č', 'É', 'Ę', 'Ë', 'Ě', 'Í', 'Î', 'Ď',
   'Đ', 'Ń', 'Ň', 'Ó', 'Ô', 'Ő', 'Ö', '×', 'Ř', 'Ů',
   'Ú', 'Ű', 'Ü', 'Ý', 'Ţ', 'ß', 'ŕ', 'á', 'â', 'ă',
   'ä', 'ĺ', 'ć', 'ç', 'č', 'é', 'ę', 'ë', 'ě', 'í',
   'î', 'ď', 'đ', 'ń', 'ň', 'ó', 'ô', 'ő', 'ö', '÷',
   'ř', 'ů', 'ú', 'ű', 'ü', 'ý', 'ţ', '˙');
  return preg_replace("/([\x80-\xFF])/e", '$iso88592[ord($1) - 0x80]', $tekscik);
} 



function kameleon_wew_uchwyty(&$obj)
{
	$prefix='_APP_WEW_UCHWYT(';
	$suffix_len=1;
	global $app;

	$a = is_array($obj);

	
	foreach ($obj AS $k=>$sub)
	{
		if (is_array($sub) || is_object($sub) )
		{
			if ($a) kameleon_wew_uchwyty($obj[$k]);
			else kameleon_wew_uchwyty($obj->$k);
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




function kameleon_xml_build_path($level)
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

function kameleon_xml_tag_start($parser, $name, $attrs)
{
	global $kameleon_xml_obj;
	global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;
	global $kameleon_tag_end_extras,$kameleon_tag_ignore;

	$kameleon_xml_idx++;	
	$kameleon_xml_stack[$kameleon_xml_idx]=$name;
	$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);

	eval("if (isset(\$$kameleon_xml_path)) \$arr_req=1;");
	if ($arr_req)
	{
		eval("if (is_array(\$$kameleon_xml_path)) \$arr_exists=1;");
		if (!$arr_exists)
		{
			eval("\$value=\$$kameleon_xml_path ;");
			if (!is_array($value) && !is_object($value)) if (!strlen($value)) $arr_req=0;
			if ($arr_req) eval("\$$kameleon_xml_path = array(\$$kameleon_xml_path) ;");
		}
		if ($arr_req)
		{
			eval("\$arr_size=sizeof(\$$kameleon_xml_path) ;");
			$kameleon_xml_stack[$kameleon_xml_idx]=$name."[$arr_size]";
			$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);	
		}
	}

	if (strlen($attrs[id]))
	{
		$kameleon_xml_stack[$kameleon_xml_idx]=$name."[".$attrs[id]."]";
		$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);	
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

	eval("\$$kameleon_xml_path = \"\";" );
	$code="\$$kameleon_xml_path = \$$kameleon_xml_path . \$kameleon_tag_begin_extras ;" ;
	eval( $code);

}

function kameleon_xml_tag_end($parser, $name)
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
	$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);

}

function kameleon_xml_data($parser,$data)
{
	global $kameleon_xml_obj;
	global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;
	global $kameleon_tag_ignore;

	global $CHARSET;

	if (!strlen(trim($data)) || $kameleon_tag_ignore) return;

	$version=phpversion();
	
	if (strstr(strtolower($CHARSET),'iso')) 
	{
		$data=kameleon_utf82iso88592($data);
	}
	if (strstr(strtolower($CHARSET),'utf') && $version[0]=='4') 
	{
		$data=kameleon_iso885922utf8($data);
	}


	$code="\$$kameleon_xml_path = \$$kameleon_xml_path . \$data ;" ;
	eval( $code);
}

function xml2obj($xml)
{
	$md5=md5($xml);

	static $md5_cache;

	if (is_object($md5_cache[$md5])) return $md5_cache[$md5];

	global $kameleon_xml_obj;
	global $kameleon_xml_stack,$kameleon_xml_idx,$kameleon_xml_path;

	$kameleon_xml_obj="";

	$kameleon_xml_idx=0;	
	$kameleon_xml_stack[$kameleon_xml_idx]="kameleon_xml_obj";
	$kameleon_xml_path=kameleon_xml_build_path($kameleon_xml_idx);

	
	$parser = xml_parser_create(); 
	xml_parser_set_option($parser,XML_OPTION_CASE_FOLDING,0); 
	xml_parser_set_option($parser,XML_OPTION_SKIP_WHITE,1); 
	//xml_parser_set_option($parser,XML_OPTION_SKIP_TAGSTART ,1);


	xml_set_element_handler($parser, "kameleon_xml_tag_start", "kameleon_xml_tag_end"); 
	xml_set_character_data_handler($parser, "kameleon_xml_data");
	if (!xml_parse($parser,$xml,1))
	{
			
		$wynik=xml_error_string(xml_get_error_code($parser));
		$wynik.="(line: ".xml_get_current_line_number($parser);	
		$wynik.=")";
		return $wynik;
	}

	xml_parser_free($parser); 

	//$app->puke($kameleon_xml_obj,'xml-transformation');
	kameleon_wew_uchwyty($kameleon_xml_obj);
	//$app->puke($kameleon_xml_obj,'xml-transformation');

	$md5_cache[$md5]=$kameleon_xml_obj;
	return $kameleon_xml_obj;
}


function obj2xml($obj,$XmlTag="xml",$depth=0)
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
		foreach ( $obj AS $k=>$v)
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
				$wynik.=obj2xml($v,$tag,$depth+1);
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

